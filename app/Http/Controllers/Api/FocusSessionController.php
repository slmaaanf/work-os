<?php

namespace App\Http\Controllers\Api;

use App\Enums\FocusSessionDecision;
use App\Enums\FocusSessionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\FinishFocusSessionRequest;
use App\Http\Resources\FocusSessionResource;
use App\Models\DailyPlanActivity;
use App\Models\FocusSession;
use App\Services\FocusSessionService;
use Illuminate\Http\Request;

class FocusSessionController extends Controller
{
    public function __construct(private FocusSessionService $focusSessionService) {}

    /**
     * Endpoint Hydration: Mengambil sesi aktif untuk user saat ini.
     */
    public function active(Request $request)
    {
        $user = $request->user();

        $activeSession = FocusSession::whereHas('dailyPlanActivity.dailyPlan', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->where('status', FocusSessionStatus::ACTIVE)
        ->with('dailyPlanActivity.activity.project') // Load relasi untuk dirender di UI Focus Mode
        ->first();

        if (!$activeSession) {
            return response()->json(['data' => null]);
        }

        return new FocusSessionResource($activeSession);
    }

    public function start(Request $request, DailyPlanActivity $dailyPlanActivity)
    {
        $session = $this->focusSessionService->start($dailyPlanActivity, $request->user());
        return new FocusSessionResource($session);
    }

    public function pause(Request $request, FocusSession $focusSession)
    {
        $this->focusSessionService->pause($focusSession->id, $request->user());
        return response()->json(['data' => ['message' => 'Session paused.']]);
    }

    public function resume(Request $request, FocusSession $focusSession)
    {
        $this->focusSessionService->resume($focusSession->id, $request->user());
        return response()->json(['data' => ['message' => 'Session resumed.']]);
    }

    public function finish(FinishFocusSessionRequest $request, FocusSession $focusSession)
    {
        $this->focusSessionService->finish(
            $focusSession->id,
            $request->user(),
            FocusSessionDecision::from($request->validated('decision')),
            $request->validated('result')
        );

        return response()->json(['data' => ['message' => 'Focus session finished successfully.']]);
    }

    public function abandon(Request $request, FocusSession $focusSession)
    {
        $this->focusSessionService->abandon($focusSession->id, $request->user());
        return response()->json(['data' => ['message' => 'Focus session abandoned.']]);
    }
}