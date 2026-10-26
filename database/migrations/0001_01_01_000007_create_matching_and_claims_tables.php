<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('possible_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('source_report_id')->constrained('reports')->cascadeOnDelete();
            $table->foreignId('matched_report_id')->constrained('reports')->cascadeOnDelete();
            $table->decimal('score', 5, 2)->default(0);
            $table->json('matching_factors')->nullable();
            $table->string('status')->default('pending'); // pending, reviewed, confirmed, dismissed
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->unique(['source_report_id', 'matched_report_id']);
            $table->index('score');
            $table->index('status');
        });

        Schema::create('recovery_claims', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number', 20)->unique();
            $table->foreignId('report_id')->constrained()->restrictOnDelete();
            $table->foreignId('claimant_id')->constrained('users')->restrictOnDelete();
            $table->text('claim_description');
            $table->string('status')->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('report_id');
            $table->index('claimant_id');
            $table->index('status');
        });

        Schema::create('claim_evidence', function (Blueprint $table) {
            $table->id();
            $table->foreignId('claim_id')->constrained('recovery_claims')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type')->nullable();
            $table->unsignedInteger('file_size')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_confidential')->default(true);
            $table->timestamps();

            $table->index('claim_id');
        });

        Schema::create('recovery_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('claim_id')->constrained('recovery_claims')->restrictOnDelete();
            $table->foreignId('report_id')->constrained()->restrictOnDelete();
            $table->string('handover_method')->nullable(); // in_person, office_pickup, etc.
            $table->text('handover_notes')->nullable();
            $table->timestamp('handover_date')->nullable();
            $table->foreignId('confirmed_by_finder')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('confirmed_by_owner')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('finder_confirmed_at')->nullable();
            $table->timestamp('owner_confirmed_at')->nullable();
            $table->string('status')->default('pending'); // pending, scheduled, completed, cancelled
            $table->timestamps();

            $table->index('claim_id');
            $table->index('report_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recovery_transactions');
        Schema::dropIfExists('claim_evidence');
        Schema::dropIfExists('recovery_claims');
        Schema::dropIfExists('possible_matches');
    }
};
