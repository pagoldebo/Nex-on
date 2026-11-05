<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Employee OKRs & Goals
        Schema::create('employee_goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title', 255);
            $table->text('description')->nullable();
            $table->enum('metric_type', ['numeric', 'revenue', 'percentage', 'boolean'])->default('percentage');
            $table->decimal('target_value', 12, 2)->default(100);
            $table->decimal('current_value', 12, 2)->default(0);
            $table->string('unit', 50)->default('%');
            $table->unsignedTinyInteger('progress_percent')->default(0);
            $table->enum('status', ['not_started', 'on_track', 'at_risk', 'completed'])->default('not_started');
            $table->date('due_date');
            $table->string('category', 50)->default('General Performance');
            $table->unsignedTinyInteger('weightage')->default(20);
            $table->timestamps();

            $table->index(['user_id', 'status']);
        });

        // 2. Appraisal Cycles
        Schema::create('appraisal_cycles', function (Blueprint $table) {
            $table->id();
            $table->string('title', 150);
            $table->date('start_date');
            $table->date('cutoff_date');
            $table->text('description')->nullable();
            $table->enum('status', ['upcoming', 'active', 'evaluation', 'closed'])->default('active');
            $table->timestamps();
        });

        // 3. Staff Appraisals & Scoring Matrix
        Schema::create('staff_appraisals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appraisal_cycle_id')->constrained('appraisal_cycles')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('self_rating', 3, 2)->default(0); // 1.0 - 5.0
            $table->decimal('manager_rating', 3, 2)->default(0); // 1.0 - 5.0
            $table->decimal('peer_rating', 3, 2)->default(0); // 1.0 - 5.0
            $table->decimal('final_score', 3, 2)->default(0); // Composite score
            $table->json('competency_scores')->nullable(); // punctuality, client_care, technical, teamwork
            $table->text('strengths')->nullable();
            $table->text('areas_for_improvement')->nullable();
            $table->enum('recommendation', ['salary_hike', 'promotion', 'bonus', 'training_pip', 'retain'])->default('salary_hike');
            $table->decimal('hike_percentage', 4, 1)->default(0);
            $table->enum('status', ['draft', 'submitted', 'reviewed', 'finalized'])->default('draft');
            $table->timestamps();

            $table->index(['appraisal_cycle_id', 'user_id']);
        });

        // 4. 360-Degree Feedback & Praise Badges
        Schema::create('employee_feedbacks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('to_user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['praise', 'manager_feedback', 'peer_review'])->default('praise');
            $table->string('badge', 50)->nullable(); // Customer Hero, Inventory Wizard, Problem Solver, Team Player
            $table->text('message');
            $table->boolean('is_public')->default(true);
            $table->timestamps();

            $table->index(['to_user_id', 'created_at']);
        });

        // Seed initial appraisal cycle
        DB::table('appraisal_cycles')->insert([
            'title'       => 'Annual Performance Review FY 2026-27',
            'start_date'  => now()->startOfYear()->toDateString(),
            'cutoff_date' => now()->endOfYear()->toDateString(),
            'description' => 'Comprehensive annual evaluation covering sales targets, client hospitality, and teamwork.',
            'status'      => 'active',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_feedbacks');
        Schema::dropIfExists('staff_appraisals');
        Schema::dropIfExists('appraisal_cycles');
        Schema::dropIfExists('employee_goals');
    }
};

