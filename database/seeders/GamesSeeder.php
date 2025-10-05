<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Game;
use App\Models\Competition;
use App\Models\Team;
use App\Models\Player;
use App\Models\Season;

class GamesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Find the current season, fallback to latest or create one
        $season = Season::where('current', true)->first();
        if (! $season) {
            $season = Season::latest('start_date')->first();
        }
        if (! $season) {
            $season = Season::create([
                'name' => 'Sample Season',
                'start_date' => now()->startOfYear()->toDateString(),
                'end_date' => now()->endOfYear()->toDateString(),
                'current' => true,
            ]);
        }

        // Ensure we have a competition attached to the current season
        $competition = Competition::firstWhere('season_id', $season->id);
        if (! $competition) {
            $competition = Competition::create([
                'season_id' => $season->id,
                'name' => $season->name . ' - Premier League',
                'type' => 'team',
            ]);
        }

        // Look for a fixtures CSV at database/seeders/fixtures/games.csv
        $fixturesPath = database_path('seeders/fixtures/games.csv');

        if (! file_exists($fixturesPath)) {
            $this->command->info('No fixtures CSV found at ' . $fixturesPath . '. Skipping game import.');
            return;
        }

        $handle = fopen($fixturesPath, 'r');
        if (! $handle) {
            $this->command->error('Unable to open fixtures CSV.');
            return;
        }

        $header = null;
        $rowCount = 0;
        while (($row = fgetcsv($handle)) !== false) {
            // Skip empty lines and comments
            $line = implode(',', $row);
            if (trim($line) === '' || str_starts_with(trim($line), '#')) {
                continue;
            }

            if (! $header) {
                $header = array_map('trim', $row);
                continue;
            }

            $data = array_combine($header, $row);
            if (! $data) continue;

            // Normalize fields
            $date = isset($data['date']) ? trim($data['date']) : null;
            $competitionName = isset($data['competition']) ? trim($data['competition']) : ($competition->name ?? null);
            $homeTeamName = isset($data['home_team']) ? trim($data['home_team']) : null;
            $awayTeamName = isset($data['away_team']) ? trim($data['away_team']) : null;
            $homeScore = isset($data['home_score']) && $data['home_score'] !== '' ? (int) $data['home_score'] : null;
            $awayScore = isset($data['away_score']) && $data['away_score'] !== '' ? (int) $data['away_score'] : null;
            $homeIndiv = isset($data['home_indiv']) ? trim($data['home_indiv']) : null;
            $awayIndiv = isset($data['away_indiv']) ? trim($data['away_indiv']) : null;

            // Find or create competition for this season
            $comp = null;
            if ($competitionName) {
                $comp = Competition::firstWhere(['name' => $competitionName, 'season_id' => $season->id]);
                if (! $comp) {
                    $comp = Competition::create(['season_id' => $season->id, 'name' => $competitionName, 'type' => 'team']);
                }
            } else {
                $comp = $competition;
            }

            // Find or create teams (attach to season)
            $homeTeam = null;
            $awayTeam = null;
            if ($homeTeamName) {
                $homeTeam = Team::firstWhere(['name' => $homeTeamName, 'season_id' => $season->id]);
                if (! $homeTeam) {
                    $homeTeam = Team::create(['name' => $homeTeamName, 'location' => null, 'season_id' => $season->id]);
                }
            }
            if ($awayTeamName) {
                $awayTeam = Team::firstWhere(['name' => $awayTeamName, 'season_id' => $season->id]);
                if (! $awayTeam) {
                    $awayTeam = Team::create(['name' => $awayTeamName, 'location' => null, 'season_id' => $season->id]);
                }
            }

            // Find or create individual players
            $homePlayer = null;
            $awayPlayer = null;
            if ($homeIndiv) {
                $homePlayer = Player::firstWhere('name', $homeIndiv);
                if (! $homePlayer) {
                    $homePlayer = Player::create(['name' => $homeIndiv, 'position' => null, 'number' => null]);
                }
            }
            if ($awayIndiv) {
                $awayPlayer = Player::firstWhere('name', $awayIndiv);
                if (! $awayPlayer) {
                    $awayPlayer = Player::create(['name' => $awayIndiv, 'position' => null, 'number' => null]);
                }
            }

            // Normalize/parse date into a standard datetime string if possible
            $parsedDate = null;
            if ($date) {
                // try generic parse first
                try {
                    $parsedDate = Carbon::parse($date)->toDateTimeString();
                } catch (\Exception $e) {
                    // try common alternative formats
                    $formats = ['d/m/Y H:i', 'd/m/Y H:i:s', 'd-m-Y H:i', 'd-m-Y H:i:s', 'd/m/Y', 'd-m-Y', 'Y-m-d H:i', 'Y-m-d H:i:s', 'Y-m-d'];
                    foreach ($formats as $fmt) {
                        try {
                            $parsedDate = Carbon::createFromFormat($fmt, $date)->toDateTimeString();
                            break;
                        } catch (\Exception $e) {
                            // continue trying
                        }
                    }
                }
            }

            $gameAttrs = [
                'competition_id' => $comp->id,
                'home_team_id' => $homeTeam?->id,
                'away_team_id' => $awayTeam?->id,
                'home_indiv_id' => $homePlayer?->id,
                'away_indiv_id' => $awayPlayer?->id,
                'home_score' => $homeScore,
                'away_score' => $awayScore,
                'date' => $parsedDate ?? $date,
            ];

            // Remove null keys to allow DB defaults
            $gameAttrs = array_filter($gameAttrs, function ($v) { return $v !== null && $v !== ''; });

            $game = new Game($gameAttrs);
            if (empty($game->getKey())) {
                $game->{$game->getKeyName()} = (string) Str::ulid();
            }
            $game->save();
            $rowCount++;
        }

        fclose($handle);
        $this->command->info("Imported {$rowCount} games from fixtures CSV.");
    }
}
