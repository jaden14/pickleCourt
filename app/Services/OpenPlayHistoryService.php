<?php

namespace App\Services;

use App\Models\OpenPlaySession;
use App\Models\OpenPlaySessionPlayer;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OpenPlayHistoryService
{
    public function archive(User $user, array $data): OpenPlaySession
    {
        return DB::transaction(function () use ($user, $data): OpenPlaySession {
            $key = $this->sessionKey($data);
            $session = OpenPlaySession::query()->updateOrCreate(
                ['user_id' => $user->id, 'session_key' => $key],
                [
                    'name' => (string) ($data['name'] ?? 'Open Play'),
                    'mode' => in_array($data['mode'] ?? null, ['balanced', 'winners', 'team'], true) ? $data['mode'] : 'balanced',
                    'event_date' => $this->eventDate($data['eventDate'] ?? null),
                    'team_count' => ($data['mode'] ?? null) === 'team' ? $this->teamCount($data['teamCount'] ?? null) : null,
                    'team_names' => ($data['mode'] ?? null) === 'team' ? $this->teamNames($data['teamNames'] ?? [], $data['teamCount'] ?? null) : null,
                    'games_per_player' => $this->gamesPerPlayer($data['gamesPerPlayer'] ?? null),
                    'courts' => max(1, (int) ($data['courts'] ?? count($data['matches'] ?? []) ?: 1)),
                    'points' => isset($data['points']) ? (int) $data['points'] : null,
                    'points_on' => (bool) ($data['pointsOn'] ?? true),
                    'timer' => (bool) ($data['timer'] ?? false),
                    'round' => max(1, (int) ($data['round'] ?? 1)),
                    'matches_played' => (int) ($data['played'] ?? count($data['completedMatches'] ?? [])),
                    'started_at' => $data['startedAt'] ?? now(),
                    'ended_at' => $data['endedAt'] ?? now(),
                ],
            );

            $session->matches()->delete();
            $session->players()->delete();
            $playersByName = [];

            foreach ($data['players'] ?? [] as $playerData) {
                $name = trim((string) ($playerData['name'] ?? 'Player')) ?: 'Player';
                $nameKey = Str::lower($name);
                if ($existing = $playersByName[$nameKey] ?? null) {
                    $existing->update([
                        'games_played' => max($existing->games_played, (int) ($playerData['gamesPlayed'] ?? 0)),
                        'wins' => max($existing->wins, (int) ($playerData['wins'] ?? 0)),
                        'losses' => max($existing->losses, (int) ($playerData['losses'] ?? 0)),
                        'team_index' => isset($playerData['teamIndex']) && is_numeric($playerData['teamIndex']) ? max(0, (int) $playerData['teamIndex']) : null,
                    ]);

                    continue;
                }

                $player = $session->players()->create([
                    'player_key' => $playerData['id'] ?? null,
                    'name' => $name,
                    'rating' => min(5, max(1, (int) ($playerData['rating'] ?? $playerData['skill'] ?? 4))),
                    'status' => (string) ($playerData['status'] ?? 'ready'),
                    'games_played' => (int) ($playerData['gamesPlayed'] ?? 0),
                    'wins' => (int) ($playerData['wins'] ?? 0),
                    'losses' => (int) ($playerData['losses'] ?? 0),
                    'partner_key' => $playerData['partnerId'] ?? null,
                    'team_index' => isset($playerData['teamIndex']) && is_numeric($playerData['teamIndex']) ? max(0, (int) $playerData['teamIndex']) : null,
                    'queued_at' => $playerData['queuedAt'] ?? null,
                ]);
                $playersByName[$nameKey] = $player;
            }

            foreach ($data['completedMatches'] ?? [] as $matchData) {
                $match = $session->matches()->create([
                    'court' => max(1, (int) ($matchData['court'] ?? 1)),
                    'round' => max(1, (int) ($matchData['round'] ?? 1)),
                    'score_a' => max(0, (int) ($matchData['scoreA'] ?? 0)),
                    'score_b' => max(0, (int) ($matchData['scoreB'] ?? 0)),
                    'winner' => in_array($matchData['winner'] ?? null, ['a', 'b'], true) ? $matchData['winner'] : null,
                    'started_at' => $matchData['startedAt'] ?? null,
                    'completed_at' => $matchData['completedAt'] ?? null,
                ]);
                $participantIds = [];

                foreach (['a' => $matchData['teamA'] ?? [], 'b' => $matchData['teamB'] ?? []] as $team => $names) {
                    foreach (array_values($names) as $position => $name) {
                        $player = $playersByName[Str::lower((string) $name)] ?? null;
                        if (! $player) {
                            $player = $session->players()->create(['name' => (string) $name, 'rating' => 4]);
                            $playersByName[Str::lower($player->name)] = $player;
                        }
                        if (isset($participantIds[$player->id])) {
                            continue;
                        }
                        $participantIds[$player->id] = true;
                        $match->participants()->create([
                            'open_play_session_player_id' => $player->id,
                            'team' => $team,
                            'position' => $position,
                        ]);
                    }
                }
            }

            return $session;
        });
    }

    public function history(User $user): array
    {
        return OpenPlaySession::query()
            ->whereBelongsTo($user)
            ->with(['players', 'matches.participants.player'])
            ->latest('started_at')
            ->get()
            ->map(fn (OpenPlaySession $session): array => $this->toFrontend($session))
            ->all();
    }

    public function migrateLegacy(User $user, array $state): array
    {
        foreach ($state['history'] ?? [] as $session) {
            if (is_array($session)) {
                $this->archive($user, $session);
            }
        }

        Arr::forget($state, 'history');

        return $state;
    }

    private function sessionKey(array $data): string
    {
        return (string) ($data['id'] ?? hash('sha256', ($data['startedAt'] ?? '').'|'.($data['name'] ?? 'Open Play')));
    }

    private function toFrontend(OpenPlaySession $session): array
    {
        return [
            'id' => $session->session_key,
            'name' => $session->name,
            'mode' => $session->mode,
            'eventDate' => $session->event_date?->toDateString(),
            'teamCount' => $session->team_count,
            'teamNames' => $session->team_names ?? [],
            'gamesPerPlayer' => $session->games_per_player,
            'courts' => $session->courts,
            'points' => $session->points,
            'pointsOn' => $session->points_on,
            'timer' => $session->timer,
            'round' => $session->round,
            'played' => $session->matches_played,
            'startedAt' => $session->started_at?->toISOString(),
            'endedAt' => $session->ended_at?->toISOString(),
            'players' => $session->players->map(fn (OpenPlaySessionPlayer $player): array => [
                'id' => $player->player_key ?? (string) $player->id,
                'name' => $player->name,
                'rating' => $player->rating,
                'status' => $player->status,
                'gamesPlayed' => $player->games_played,
                'wins' => $player->wins,
                'losses' => $player->losses,
                'partnerId' => $player->partner_key,
                'teamIndex' => $player->team_index,
                'queuedAt' => $player->queued_at?->toISOString(),
            ])->values()->all(),
            'completedMatches' => $session->matches->map(function ($match): array {
                $participants = $match->participants->sortBy('position');

                return [
                    'court' => $match->court,
                    'round' => $match->round,
                    'teamA' => $participants->where('team', 'a')->pluck('player.name')->values()->all(),
                    'teamB' => $participants->where('team', 'b')->pluck('player.name')->values()->all(),
                    'scoreA' => $match->score_a,
                    'scoreB' => $match->score_b,
                    'winner' => $match->winner,
                    'startedAt' => $match->started_at?->toISOString(),
                    'completedAt' => $match->completed_at?->toISOString(),
                ];
            })->values()->all(),
        ];
    }

    private function eventDate(mixed $value): ?string
    {
        if (! is_string($value) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
            return null;
        }

        return $value;
    }

    private function teamCount(mixed $value): ?int
    {
        $count = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 2, 'max_range' => 64]]);

        return $count === false ? null : $count;
    }

    private function teamNames(mixed $value, mixed $teamCount): array
    {
        if (! is_array($value)) {
            return [];
        }

        $count = $this->teamCount($teamCount) ?? 0;

        return array_map(
            fn ($name) => mb_substr(trim((string) $name), 0, 40),
            array_slice($value, 0, $count),
        );
    }

    private function gamesPerPlayer(mixed $value): int
    {
        $games = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 20]]);

        return $games === false ? 4 : $games;
    }
}
