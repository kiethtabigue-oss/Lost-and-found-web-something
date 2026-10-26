<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lost_property_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->cascadeOnDelete();
            $table->string('brand')->nullable();
            $table->string('color')->nullable();
            $table->string('model')->nullable();
            $table->text('distinguishing_marks')->nullable();
            $table->string('serial_number_hint')->nullable(); // partial, not full serial
            $table->decimal('estimated_value', 10, 2)->nullable();
            $table->timestamps();

            $table->unique('report_id');
        });

        Schema::create('found_property_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->cascadeOnDelete();
            $table->string('brand')->nullable();
            $table->string('color')->nullable();
            $table->string('model')->nullable();
            $table->text('distinguishing_marks')->nullable();
            $table->string('custody_status')->default('with_finder'); // with_finder, turned_over, stored
            $table->string('storage_location_hint')->nullable();
            $table->boolean('turned_over_to_office')->default(false);
            $table->string('verification_status')->default('unverified'); // unverified, verified, flagged
            $table->timestamps();

            $table->unique('report_id');
        });

        Schema::create('missing_pet_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->cascadeOnDelete();
            $table->string('pet_name')->nullable();
            $table->string('animal_type'); // dog, cat, bird, etc.
            $table->string('breed')->nullable();
            $table->string('color_markings')->nullable();
            $table->string('approximate_age')->nullable();
            $table->string('size')->nullable(); // small, medium, large
            $table->boolean('has_collar')->default(false);
            $table->string('collar_description')->nullable();
            $table->string('microchip_id_hint')->nullable(); // partial or presence only
            $table->text('medical_notes')->nullable();
            $table->text('behavioral_notes')->nullable();
            $table->boolean('reward_offered')->default(false);
            $table->string('reward_description')->nullable();
            $table->timestamps();

            $table->unique('report_id');
        });

        Schema::create('found_pet_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->cascadeOnDelete();
            $table->string('animal_type'); // dog, cat, bird, etc.
            $table->string('breed_guess')->nullable();
            $table->string('color_markings')->nullable();
            $table->string('approximate_age')->nullable();
            $table->string('size')->nullable();
            $table->boolean('has_collar')->default(false);
            $table->string('collar_description')->nullable();
            $table->string('visible_tag_info')->nullable();
            $table->string('condition')->nullable(); // healthy, injured, malnourished, etc.
            $table->text('immediate_needs')->nullable();
            $table->boolean('is_safely_sheltered')->default(false);
            $table->string('shelter_location_hint')->nullable();
            $table->string('verification_status')->default('unverified');
            $table->timestamps();

            $table->unique('report_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('found_pet_details');
        Schema::dropIfExists('missing_pet_details');
        Schema::dropIfExists('found_property_details');
        Schema::dropIfExists('lost_property_details');
    }
};
