<?php

namespace App\Http\Controllers;

use App\Models\OpenPlayAccessRequest;
use App\Models\OpenPlaySession;
use App\Models\OpenPlayState;
use App\Services\OpenPlayHistoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OpenPlayController extends Controller
{
    public function show(Request $request, OpenPlayHistoryService $historyService): View
    {
        $user = $request->user();

        if (! $user->canUseOpenPlay()) {
            return view('open-play-access', [
                'accessRequest' => $user->openPlayAccessRequest,
            ]);
        }

        $stateModel = $user->openPlayState;
        $state = $stateModel?->state ?? [];

        if (array_key_exists('history', $state)) {
            $state = $historyService->migrateLegacy($user, $state);
            $stateModel?->update(['state' => $state]);
        }

        $state['history'] = $historyService->history($user);

        return view('open-play', ['openPlayState' => $state]);
    }

    public function requestAccess(Request $request): RedirectResponse
    {
        abort_if($request->user()->role === 'admin', 403);

        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:1000'],
        ]);

        OpenPlayAccessRequest::query()->updateOrCreate(
            ['user_id' => $request->user()->id],
            [
                'status' => 'pending',
                'message' => $validated['message'] ?? null,
                'reviewed_by' => null,
                'reviewed_at' => null,
            ],
        );

        return back()->with('status', 'Your Open Play access request was sent to an administrator.');
    }

    public function saveState(Request $request, OpenPlayHistoryService $historyService): JsonResponse
    {
        abort_unless($request->user()->canUseOpenPlay(), 403);

        $validated = $request->validate([
            'state' => ['required', 'array'],
        ]);

        $state = $validated['state'];
        foreach ($state['history'] ?? [] as $legacySession) {
            if (is_array($legacySession)) {
                $historyService->archive($request->user(), $legacySession);
            }
        }
        unset($state['history']);

        OpenPlayState::query()->updateOrCreate(
            ['user_id' => $request->user()->id],
            ['state' => $state],
        );

        return response()->json(['saved' => true]);
    }

    public function archiveSession(Request $request, OpenPlayHistoryService $historyService): JsonResponse
    {
        abort_unless($request->user()->canUseOpenPlay(), 403);
        $validated = $request->validate(['session' => ['required', 'array']]);
        $session = $historyService->archive($request->user(), $validated['session']);

        return response()->json(['saved' => true, 'session_key' => $session->session_key]);
    }

    public function clearData(Request $request): JsonResponse
    {
        abort_unless($request->user()->canUseOpenPlay(), 403);
        OpenPlaySession::query()->whereBelongsTo($request->user())->delete();
        OpenPlayState::query()->whereBelongsTo($request->user())->delete();

        return response()->json(['cleared' => true]);
    }
}
