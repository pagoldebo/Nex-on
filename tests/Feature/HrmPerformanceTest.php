<?php

namespace Tests\Feature;

use App\Models\AppraisalCycle;
use App\Models\EmployeeFeedback;
use App\Models\EmployeeGoal;
use App\Models\StaffAppraisal;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HrmPerformanceTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin.performance@nexon.com',
            'role'  => 'admin',
        ]);

        $this->staff = User::factory()->create([
            'name'  => 'Ananya Sen',
            'email' => 'ananya.sen@nexon.com',
            'role'  => 'vendor',
        ]);

        Template::create(['key' => 'buttercup', 'name' => 'Buttercup', 'is_active' => true]);
    }

    public function test_goals_tracker_directory_renders_successfully(): void
    {
        EmployeeGoal::create([
            'user_id'          => $this->staff->id,
            'creator_id'       => $this->admin->id,
            'title'            => 'Achieve ₹10 Lakhs in Bridal POS Revenue',
            'metric_type'      => 'revenue',
            'target_value'     => 1000000,
            'current_value'    => 650000,
            'unit'             => '₹',
            'progress_percent' => 65,
            'status'           => 'on_track',
            'due_date'         => now()->addMonths(2)->toDateString(),
            'category'         => 'Retail Sales',
            'weightage'        => 30,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.hrm.performance.goals'));

        $response->assertStatus(200);
        $response->assertSee('Employee Goals &amp; OKR Tracker', false);
        $response->assertSee('Achieve ₹10 Lakhs in Bridal POS Revenue');
        $response->assertSee('Ananya Sen');
    }

    public function test_goal_definition_studio_view_and_store_okr(): void
    {
        // Studio form
        $createResponse = $this->actingAs($this->admin)->get(route('admin.hrm.performance.goals.create'));
        $createResponse->assertStatus(200);
        $createResponse->assertSee('New Goal &amp; Milestone Studio', false);

        // Store new goal
        $payload = [
            'user_id'       => $this->staff->id,
            'title'         => 'Complete 100 Atelier Client Appointments',
            'description'   => 'Provide personalized garment fittings and styling advice.',
            'metric_type'   => 'numeric',
            'target_value'  => 100,
            'current_value' => 20,
            'unit'          => 'Appointments',
            'due_date'      => now()->addMonths(3)->toDateString(),
            'category'      => 'Atelier Hospitality',
            'weightage'     => 25,
        ];

        $storeResponse = $this->actingAs($this->admin)->post(route('admin.hrm.performance.goals.store'), $payload);

        $storeResponse->assertRedirect(route('admin.hrm.performance.goals'));
        $this->assertDatabaseHas('employee_goals', [
            'user_id'          => $this->staff->id,
            'title'            => 'Complete 100 Atelier Client Appointments',
            'progress_percent' => 20,
            'status'           => 'on_track',
        ]);
    }

    public function test_quick_update_goal_progress_recalculates_status(): void
    {
        $goal = EmployeeGoal::create([
            'user_id'          => $this->staff->id,
            'creator_id'       => $this->admin->id,
            'title'            => 'Store Inventory Stock Audit Accuracy',
            'metric_type'      => 'percentage',
            'target_value'     => 100,
            'current_value'    => 50,
            'unit'             => '%',
            'progress_percent' => 50,
            'status'           => 'on_track',
            'due_date'         => now()->addMonth()->toDateString(),
            'category'         => 'Inventory Accuracy',
            'weightage'        => 20,
        ]);

        $updateResponse = $this->actingAs($this->admin)->post(route('admin.hrm.performance.goals.update-progress', $goal), [
            'current_value' => 100,
        ]);

        $updateResponse->assertRedirect();
        $fresh = $goal->fresh();
        $this->assertEquals(100, $fresh->progress_percent);
        $this->assertEquals('completed', $fresh->status);
    }

    public function test_staff_appraisal_scoring_matrix_and_30_50_20_composite_calculation(): void
    {
        $cycle = AppraisalCycle::create([
            'title'       => 'H2 2026 Comprehensive Evaluation',
            'start_date'  => '2026-07-01',
            'cutoff_date' => '2026-12-31',
            'status'      => 'active',
        ]);

        // Appraisal creation form
        $createResponse = $this->actingAs($this->admin)->get(route('admin.hrm.performance.appraisals.create'));
        $createResponse->assertStatus(200);
        $createResponse->assertSee('Conduct Staff Performance Appraisal');

        // Store appraisal with Self=4.0, Manager=5.0, Peer=4.5
        // Expected Composite: (4.0 * 0.30) + (5.0 * 0.50) + (4.5 * 0.20) = 1.20 + 2.50 + 0.90 = 4.60
        $payload = [
            'appraisal_cycle_id'    => $cycle->id,
            'user_id'               => $this->staff->id,
            'self_rating'           => 4.0,
            'manager_rating'        => 5.0,
            'peer_rating'           => 4.5,
            'competencies'          => [
                'punctuality'         => 4.8,
                'client_hospitality'  => 5.0,
                'technical_competence'=> 4.5,
                'teamwork'            => 4.7,
            ],
            'strengths'             => 'Exemplary boutique customer engagement.',
            'areas_for_improvement' => 'Can train junior associates on inventory counting.',
            'recommendation'        => 'salary_hike',
            'hike_percentage'       => 15.0,
            'status'                => 'finalized',
        ];

        $storeResponse = $this->actingAs($this->admin)->post(route('admin.hrm.performance.appraisals.store'), $payload);

        $storeResponse->assertRedirect(route('admin.hrm.performance.appraisals'));

        $appraisal = StaffAppraisal::where('user_id', $this->staff->id)->first();
        $this->assertNotNull($appraisal);
        $this->assertEquals(4.60, (float) $appraisal->final_score);
        $this->assertEquals('salary_hike', $appraisal->recommendation);
        $this->assertEquals(15.0, (float) $appraisal->hike_percentage);

        // Verify in Appraisal directory view
        $dirResponse = $this->actingAs($this->admin)->get(route('admin.hrm.performance.appraisals'));
        $dirResponse->assertStatus(200);
        $dirResponse->assertSee('4.60');
        $dirResponse->assertSee('+15.0%');
    }

    public function test_appraisal_cycles_manager_and_store_window(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.hrm.performance.cycles'));
        $response->assertStatus(200);
        $response->assertSee('Appraisal Cycles &amp; Review Windows', false);

        $cyclePayload = [
            'title'       => 'Q1 2027 Store Growth Evaluation',
            'start_date'  => '2027-01-01',
            'cutoff_date' => '2027-03-31',
            'description' => 'Targeting retail sales and customer retention milestones.',
            'status'      => 'upcoming',
        ];

        $storeResponse = $this->actingAs($this->admin)->post(route('admin.hrm.performance.cycles.store'), $cyclePayload);
        $storeResponse->assertRedirect(route('admin.hrm.performance.cycles'));

        $this->assertDatabaseHas('appraisal_cycles', [
            'title'  => 'Q1 2027 Store Growth Evaluation',
            'status' => 'upcoming',
        ]);
    }

    public function test_360_degree_peer_feedback_and_praise_badge_wall(): void
    {
        // Store Feedback
        $feedbackPayload = [
            'to_user_id' => $this->staff->id,
            'badge'      => 'Customer Hero',
            'message'    => 'Went above and beyond assisting a VIP bridal entourage on Saturday evening!',
            'is_public'  => 1,
        ];

        $storeResponse = $this->actingAs($this->admin)->post(route('admin.hrm.performance.feedback.store'), $feedbackPayload);
        $storeResponse->assertRedirect(route('admin.hrm.performance.feedback'));

        $this->assertDatabaseHas('employee_feedbacks', [
            'to_user_id' => $this->staff->id,
            'badge'      => 'Customer Hero',
            'type'       => 'praise',
        ]);

        // View Feedback Wall
        $wallResponse = $this->actingAs($this->admin)->get(route('admin.hrm.performance.feedback'));
        $wallResponse->assertStatus(200);
        $wallResponse->assertSee('Customer Hero');
        $wallResponse->assertSee('Went above and beyond assisting a VIP bridal entourage');
    }
}
