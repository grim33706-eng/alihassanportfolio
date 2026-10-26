<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('case_studies', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 120)->unique();
            $table->string('title', 160);
            $table->string('brand', 120)->nullable();
            $table->string('industry', 120);
            $table->string('country', 120);
            $table->string('category', 40);
            $table->json('platforms');
            $table->string('period', 80)->nullable();
            $table->text('strategy');
            $table->text('improvements')->nullable();
            // [{label, before, after, unit, direction: up|down}] — real figures only.
            $table->json('metrics')->nullable();
            // Demonstration entries are labelled as such on the site.
            $table->boolean('is_demo')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('case_study_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_study_id')->constrained()->cascadeOnDelete();
            $table->enum('stage', ['before', 'after']);
            $table->string('path');
            $table->string('caption', 200)->nullable();
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['case_study_id', 'stage', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_study_images');
        Schema::dropIfExists('case_studies');
    }
};
