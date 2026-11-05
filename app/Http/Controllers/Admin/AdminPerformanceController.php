<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AppraisalCycle;
use App\Models\EmployeeFeedback;
use App\Models\EmployeeGoal;
use App\Models\StaffAppraisal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminPerformanceController extends Controller
{
    protected function getStaffMembers()
    {
        $staff = User::query()
            ->where(function ($q) {
                $q->whereIn('role', ['admin', 'vendor'])
                    ->orWhere('role', '!=', 'customer');
            })
            ->whereNull('deactivated_at')
            ->orderBy('name')
            ->get();

        if ($staff->isEmpty()) {
            $staff = User::whereNull('deactivated_at')->get();
        }

        return $staff;
    }

    /**
     * Job 19: Employee Goals & OKR Progress Tracker.
     */
    public function goals(Request $request)
    {
        $query = EmployeeGoal::with(['user', 'creator'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        $goals = $query->paginate(15)->withQueryString();

        // KPI Summary
        $totalGoals = EmployeeGoal::count();
        $completedGoals = EmployeeGoal::where('status', 'completed')->count();
        $onTrackGoals = EmployeeGoal::where('status', 'on_track')->count();
        $atRiskGoals = EmployeeGoal::where('status', 'at_risk')->count();
        $avgProgress = (float) EmployeeGoal::avg('progress_percent') ?: 0;

        $staffMembers = $this->getStaffMembers();

        return view('admin.hrm.performance.goals', compact(
            'goals',
            'totalGoals',
            'completedGoals',
            'onTrackGoals',
            'atRiskGoals',
            'avgProgress',
            'staffMembers'
        ));
    }

    /**
     * Job 20: New Goal & Milestone Definition Studio.
     */
    public function createGoal()
    {
        $staffMembers = $this->getStaffMembers();

        return view('admin.hrm.performance.goals-create', compact('staffMembers'));
    }

    /**
     * Store new employee goal / OKR.
     */
    public function storeGoal(Request $request)
    {
        $validated = $request->validate([
            'user_id'       => 'required|exists:users,id',
            'title'         => 'required|string|max:255',
            'description'   => 'nullable|string',
            'metric_type'   => 'required|in:numeric,revenue,percentage,boolean',
            'target_value'  => 'required|numeric|min:0.01',
            'current_value' => 'nullable|numeric|min:0',
            'unit'          => 'required|string|max:50',
            'due_date'      => 'required|date',
            'category'      => 'required|string|max:100',
            'weightage'     => 'required|integer|min:1|max:100',
        ]);

        $current = (float) ($validated['current_value'] ?? 0);
        $target = (float) $validated['target_value'];
        $progress = min(100, (int) round(($current / $target) * 100));

        $status = 'not_started';
        if ($progress >= 100) {
            $status = 'completed';
        } elseif ($progress > 0) {
            $status = 'on_track';
        }

        EmployeeGoal::create([
            'user_id'          => $validated['user_id'],
            'creator_id'       => Auth::id(),
            'title'            => $validated['title'],
            'description'      => $validated['description'] ?? null,
            'metric_type'      => $validated['metric_type'],
            'target_value'     => $target,
            'current_value'    => $current,
            'unit'             => $validated['unit'],
            'progress_percent' => $progress,
            'status'           => $status,
            'due_date'         => $validated['due_date'],
            'category'         => $validated['category'],
            'weightage'        => $validated['weightage'],
        ]);

        return redirect()->route('admin.hrm.performance.goals')
            ->with('success', 'Employee OKR milestone created successfully.');
    }

    /**
     * Inline or Quick Update Goal Progress.
     */
    public function updateGoalProgress(Request $request, EmployeeGoal $goal)
    {
        $validated = $request->validate([
            'current_value' => 'required|numeric|min:0',
        ]);

        $current = (float) $validated['current_value'];
        $target = (float) $goal->target_value;
        $progress = min(100, (int) round(($current / $target) * 100));

        $status = $progress >= 100 ? 'completed' : ($progress > 0 ? 'on_track' : 'not_started');

        $goal->update([
            'current_value'    => $current,
            'progress_percent' => $progress,
            'status'           => $status,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success'          => true,
                'progress_percent' => $progress,
                'status'           => $status,
            ]);
        }

        return redirect()->back()->with('success', "Goal progress updated to {$progress}%.");
    }

    /**
     * Job 21: Staff Appraisal Directory & Scoring Matrix.
     */
    public function appraisals(Request $request)
    {
        $query = StaffAppraisal::with(['user', 'reviewer', 'cycle'])->latest();

        if ($request->filled('cycle_id')) {
            $query->where('appraisal_cycle_id', $request->cycle_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $appraisals = $query->paginate(15)->withQueryString();
        $cycles = AppraisalCycle::orderBy('start_date', 'desc')->get();

        return view('admin.hrm.performance.appraisals', compact('appraisals', 'cycles'));
    }

    /**
     * Create Appraisal Evaluation Form.
     */
    public function createAppraisal()
    {
        $cycles = AppraisalCycle::where('status', 'active')->get();
        if ($cycles->isEmpty()) {
            $cycles = AppraisalCycle::latest()->take(5)->get();
        }

        $staffMembers = $this->getStaffMembers();

        return view('admin.hrm.performance.appraisals-create', compact('cycles', 'staffMembers'));
    }

    /**
     * Store Staff Appraisal & Compute 30-50-20 Composite Score.
     */
    public function storeAppraisal(Request $request)
    {
        $validated = $request->validate([
            'appraisal_cycle_id'      => 'required|exists:appraisal_cycles,id',
            'user_id'                 => 'required|exists:users,id',
            'self_rating'             => 'required|numeric|min:1|max:5',
            'manager_rating'          => 'required|numeric|min:1|max:5',
            'peer_rating'             => 'required|numeric|min:1|max:5',
            'competencies'            => 'nullable|array',
            'strengths'               => 'nullable|string',
            'areas_for_improvement'   => 'nullable|string',
            'recommendation'          => 'required|in:salary_hike,promotion,bonus,training_pip,retain',
            'hike_percentage'         => 'nullable|numeric|min:0|max:100',
            'status'                  => 'required|in:draft,submitted,reviewed,finalized',
        ]);

        $self = (float) $validated['self_rating'];
        $manager = (float) $validated['manager_rating'];
        $peer = (float) $validated['peer_rating'];
        $composite = round(($self * 0.30) + ($manager * 0.50) + ($peer * 0.20), 2);

        StaffAppraisal::create([
            'appraisal_cycle_id'    => $validated['appraisal_cycle_id'],
            'user_id'               => $validated['user_id'],
            'reviewer_id'           => Auth::id(),
            'self_rating'           => $self,
            'manager_rating'        => $manager,
            'peer_rating'           => $peer,
            'final_score'           => $composite,
            'competency_scores'     => $validated['competencies'] ?? [
                'punctuality'         => 4.5,
                'client_hospitality'  => 4.8,
                'technical_competence'=> 4.2,
                'teamwork'            => 4.7,
            ],
            'strengths'             => $validated['strengths'] ?? null,
            'areas_for_improvement' => $validated['areas_for_improvement'] ?? null,
            'recommendation'        => $validated['recommendation'],
            'hike_percentage'       => $validated['hike_percentage'] ?? 0,
            'status'                => $validated['status'],
        ]);

        return redirect()->route('admin.hrm.performance.appraisals')
            ->with('success', "Staff appraisal created with composite score of {$composite}/5.00.");
    }

    /**
     * Job 22: Evaluation Windows & Cycles Manager.
     */
    public function cycles(Request $request)
    {
        $cycles = AppraisalCycle::withCount('appraisals')->latest()->get();

        return view('admin.hrm.performance.cycles', compact('cycles'));
    }

    /**
     * Store new Appraisal Cycle.
     */
    public function storeCycle(Request $request)
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:150',
            'start_date'  => 'required|date',
            'cutoff_date' => 'required|date|after_or_equal:start_date',
            'description' => 'nullable|string',
            'status'      => 'required|in:upcoming,active,evaluation,closed',
        ]);

        AppraisalCycle::create($validated);

        return redirect()->route('admin.hrm.performance.cycles')
            ->with('success', "Appraisal review cycle '{$validated['title']}' configured successfully.");
    }

    /**
     * Job 23: 360-Degree Peer Feedback & Praise Badges Wall.
     */
    public function feedback(Request $request)
    {
        $query = EmployeeFeedback::with(['sender', 'recipient'])->latest();

        if ($request->filled('badge')) {
            $query->where('badge', $request->badge);
        }

        if ($request->filled('user_id')) {
            $query->where('to_user_id', $request->user_id);
        }

        $feedbacks = $query->paginate(20)->withQueryString();

        $staffMembers = $this->getStaffMembers();

        $availableBadges = [
            'Customer Hero'      => ['icon' => '🌟', 'color' => 'bg-amber-100 text-amber-800 border-amber-300'],
            'Inventory Wizard'   => ['icon' => '📦', 'color' => 'bg-blue-100 text-blue-800 border-blue-300'],
            'Problem Solver'     => ['icon' => '⚡', 'color' => 'bg-purple-100 text-purple-800 border-purple-300'],
            'Team Player'        => ['icon' => '🤝', 'color' => 'bg-emerald-100 text-emerald-800 border-emerald-300'],
            'Atelier Excellence' => ['icon' => '✂️', 'color' => 'bg-rose-100 text-rose-800 border-rose-300'],
        ];

        return view('admin.hrm.performance.feedback', compact('feedbacks', 'staffMembers', 'availableBadges'));
    }

    /**
     * Post new peer feedback or praise badge.
     */
    public function storeFeedback(Request $request)
    {
        $validated = $request->validate([
            'to_user_id' => 'required|exists:users,id',
            'badge'      => 'nullable|string|max:50',
            'message'    => 'required|string|max:1000',
            'is_public'  => 'nullable|boolean',
        ]);

        EmployeeFeedback::create([
            'from_user_id' => Auth::id() ?? 1,
            'to_user_id'   => $validated['to_user_id'],
            'type'         => !empty($validated['badge']) ? 'praise' : 'peer_review',
            'badge'        => $validated['badge'] ?? null,
            'message'      => $validated['message'],
            'is_public'    => (bool) ($request->input('is_public', 1)),
        ]);

        return redirect()->route('admin.hrm.performance.feedback')
            ->with('success', 'Praise badge and peer recognition posted to the company wall!');
    }
}
