<?php

namespace App\Http\Controllers;

use App\Models\SavedPlan;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SavedPlanController extends Controller
{
    public function index(Request $request): View
    {
        $plans = SavedPlan::query()
            ->with('island')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return view('marketplace.plans.index', [
            'plans' => $plans,
        ]);
    }

    public function show(Request $request, SavedPlan $savedPlan): View
    {
        abort_unless($savedPlan->user_id === $request->user()->id, 403);

        $savedPlan->load('island');

        return view('marketplace.plans.show', [
            'savedPlan' => $savedPlan,
        ]);
    }
}
