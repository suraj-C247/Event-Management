<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlanRequest;
use App\Http\Services\PlanService;
use Illuminate\Support\Facades\Lang;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PlanController extends Controller
{   
    protected $planService;

    /**
     * Constructor to inject PlanService dependency.
     
     */
    public function __construct(PlanService $planService)
    {
        $this->planService = $planService;
    }

    /**
     * Show the list of plan for the authenticated user.
     */
    public function index(): View
    {

        $plans = $this->planService->listPlans();
        return view('admin.plans.index', compact('plans'));
    }

    /**
     * Show the form to create a new plan.
     */
    public function create(): View
    {
        return view('admin.plans.create');
    }

    /**
     * Store a newly created plan in the database.
     */
    public function store(PlanRequest $request): RedirectResponse
    {   
        // Delegate plan creation to PlanService
        $this->planService->createPlan($request);                                               

        return redirect()->route('admin.plans')->with('success', Lang::get('plan_created'));
    }
}

