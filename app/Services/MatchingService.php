<?php

namespace App\Services;

use App\Enums\ReportStatus;
use App\Enums\ReportType;
use App\Models\PossibleMatch;
use App\Models\Report;
use App\Models\SystemSetting;

class MatchingService
{
    /**
     * Run matching for a specific report against all complementary active reports.
     *
     * @return int Number of potential matches identified
     */
    public function findMatchesFor(Report $report): int
    {
        $targetType = match ($report->type) {
            ReportType::LostProperty => ReportType::FoundProperty,
            ReportType::FoundProperty => ReportType::LostProperty,
            ReportType::MissingPet => ReportType::FoundPet,
            ReportType::FoundPet => ReportType::MissingPet,
        };

        // Query active candidate reports
        $candidates = Report::where('type', $targetType)
            ->whereIn('status', [ReportStatus::Active, ReportStatus::PendingReview, ReportStatus::PossibleMatch])
            ->where('id', '!=', $report->id)
            ->with(['category', 'barangay', 'lostPropertyDetail', 'foundPropertyDetail', 'missingPetDetail', 'foundPetDetail'])
            ->get();

        $threshold = (int) SystemSetting::get('match_threshold', 40);
        $matchCount = 0;

        foreach ($candidates as $candidate) {
            $analysis = $this->calculateScore($report, $candidate);

            if ($analysis['score'] >= $threshold) {
                // Save match for both directions or source
                PossibleMatch::updateOrCreate(
                    [
                        'source_report_id' => $report->id,
                        'matched_report_id' => $candidate->id,
                    ],
                    [
                        'score' => $analysis['score'],
                        'matching_factors' => $analysis['factors'],
                        'status' => 'pending',
                    ]
                );

                PossibleMatch::updateOrCreate(
                    [
                        'source_report_id' => $candidate->id,
                        'matched_report_id' => $report->id,
                    ],
                    [
                        'score' => $analysis['score'],
                        'matching_factors' => $analysis['factors'],
                        'status' => 'pending',
                    ]
                );

                $matchCount++;
            }
        }

        return $matchCount;
    }

    /**
     * Calculate similarity score between two complementary reports.
     *
     * @return array{score: float, factors: array<string, mixed>}
     */
    public function calculateScore(Report $a, Report $b): array
    {
        $score = 0.0;
        $factors = [];

        // 1. Category / Animal Type (up to 25 pts)
        if ($a->category_id === $b->category_id) {
            $score += 25;
            $factors['category'] = ['points' => 25, 'note' => 'Exact category match'];
        }

        // 2. Geographic Proximity (up to 25 pts)
        if ($a->barangay_id === $b->barangay_id) {
            $score += 15;
            $zoneBonus = ($a->community_zone_id && $a->community_zone_id === $b->community_zone_id) ? 10 : 0;
            $score += $zoneBonus;
            $factors['location'] = [
                'points' => 15 + $zoneBonus,
                'note' => $zoneBonus > 0 ? 'Same barangay and community zone' : 'Same barangay',
            ];
        }

        // 3. Date Proximity (up to 15 pts)
        if ($a->incident_date && $b->incident_date) {
            $daysDiff = abs($a->incident_date->diffInDays($b->incident_date));
            if ($daysDiff <= 2) {
                $score += 15;
                $factors['date'] = ['points' => 15, 'note' => "Reported within {$daysDiff} days"];
            } elseif ($daysDiff <= 7) {
                $score += 10;
                $factors['date'] = ['points' => 10, 'note' => "Reported within {$daysDiff} days"];
            } elseif ($daysDiff <= 14) {
                $score += 5;
                $factors['date'] = ['points' => 5, 'note' => "Reported within {$daysDiff} days"];
            }
        }

        // 4. Type-Specific Attribute Matching (up to 25 pts)
        $attrPoints = 0;
        $attrNotes = [];

        if (in_array($a->type, [ReportType::LostProperty, ReportType::FoundProperty])) {
            $propA = $a->type === ReportType::LostProperty ? $a->lostPropertyDetail : $a->foundPropertyDetail;
            $propB = $b->type === ReportType::LostProperty ? $b->lostPropertyDetail : $b->foundPropertyDetail;

            if ($propA && $propB) {
                if ($propA->brand && $propB->brand && strcasecmp(trim($propA->brand), trim($propB->brand)) === 0) {
                    $attrPoints += 12;
                    $attrNotes[] = 'Same brand: '.$propA->brand;
                }
                if ($propA->color && $propB->color && stripos($propA->color, $propB->color) !== false || ($propB->color && stripos($propB->color, $propA->color) !== false)) {
                    $attrPoints += 8;
                    $attrNotes[] = 'Color match';
                }
                if ($propA->model && $propB->model && (stripos($propA->model, $propB->model) !== false || stripos($propB->model, $propA->model) !== false)) {
                    $attrPoints += 5;
                    $attrNotes[] = 'Model similarity';
                }
            }
        } else {
            // Pet matching
            $petA = $a->type === ReportType::MissingPet ? $a->missingPetDetail : $a->foundPetDetail;
            $petB = $b->type === ReportType::MissingPet ? $b->missingPetDetail : $b->foundPetDetail;

            if ($petA && $petB) {
                if ($petA->animal_type === $petB->animal_type) {
                    $attrPoints += 10;
                    $attrNotes[] = 'Same animal type: '.$petA->animal_type;
                }

                $breedA = $a->type === ReportType::MissingPet ? ($petA->breed ?? '') : ($petA->breed_guess ?? '');
                $breedB = $b->type === ReportType::MissingPet ? ($petB->breed ?? '') : ($petB->breed_guess ?? '');

                if (! empty($breedA) && ! empty($breedB) && (stripos($breedA, $breedB) !== false || stripos($breedB, $breedA) !== false)) {
                    $attrPoints += 8;
                    $attrNotes[] = 'Breed match: '.$breedA;
                }

                if ($petA->has_collar && $petB->has_collar) {
                    $attrPoints += 4;
                    $attrNotes[] = 'Both noted with collar';
                }

                if ($petA->size && $petB->size && $petA->size === $petB->size) {
                    $attrPoints += 3;
                    $attrNotes[] = 'Same size: '.$petA->size;
                }
            }
        }

        if ($attrPoints > 0) {
            $score += $attrPoints;
            $factors['attributes'] = [
                'points' => $attrPoints,
                'note' => implode(', ', $attrNotes),
            ];
        }

        // 5. Title & Description Keyword Overlap (up to 10 pts)
        $textA = strtolower($a->title.' '.$a->description);
        $textB = strtolower($b->title.' '.$b->description);
        similar_text($textA, $textB, $percent);
        $textPoints = round(($percent / 100) * 10, 1);
        if ($textPoints > 2) {
            $score += $textPoints;
            $factors['text_similarity'] = [
                'points' => $textPoints,
                'note' => round($percent, 1).'% text similarity',
            ];
        }

        return [
            'score' => min(100.0, round($score, 2)),
            'factors' => $factors,
        ];
    }
}
