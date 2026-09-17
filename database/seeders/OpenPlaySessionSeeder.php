<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\OpenPlayHistoryService;
use Illuminate\Database\Seeder;

class OpenPlaySessionSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->firstOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'),
            ],
        );

        app(OpenPlayHistoryService::class)->archive($user, [
            'id' => 'seeded-open-play-session',
            'name' => 'Friday Open Play',
            'mode' => 'team',
            'eventDate' => now()->toDateString(),
            'courts' => 2,
            'teamCount' => 4,
            'teamNames' => ['Red Team', 'Orange Team', 'Yellow Team', 'Blue Team'],
            'teamColors' => ['#dc2626', '#ea580c', '#ca8a04', '#2563eb'],
            'gamesPerPlayer' => 4,
            'points' => 11,
            'pointsOn' => true,
            'timer' => false,
            'round' => 2,
            'played' => 1,
            'startedAt' => now()->subHour()->toIso8601String(),
            'endedAt' => now()->toIso8601String(),
            'players' => [
                ['id' => 'seed-player-1', 'name' => 'Alex', 'teamIndex' => 0, 'gamesPlayed' => 1, 'wins' => 1, 'losses' => 0],
                ['id' => 'seed-player-2', 'name' => 'Bea', 'teamIndex' => 0, 'gamesPlayed' => 1, 'wins' => 1, 'losses' => 0],
                ['id' => 'seed-player-3', 'name' => 'Chris', 'teamIndex' => 1, 'gamesPlayed' => 1, 'wins' => 0, 'losses' => 1],
                ['id' => 'seed-player-4', 'name' => 'Dana', 'teamIndex' => 1, 'gamesPlayed' => 1, 'wins' => 0, 'losses' => 1],
            ],
            'completedMatches' => [[
                'court' => 1,
                'round' => 1,
                'teamA' => ['Alex', 'Bea'],
                'teamB' => ['Chris', 'Dana'],
                'scoreA' => 11,
                'scoreB' => 7,
                'winner' => 'a',
            ]],
        ]);
    }
}
