<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Season;

class SeasonsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seasons = [
            [
                'name' => 'Spring 2026',
                'start_date' => '2026-03-01',
                'end_date' => '2026-05-31',
            ],
            [
                'name' => 'Summer 2026',
                'start_date' => '2026-06-01',
                'end_date' => '2026-08-31',
                'current' => true,
            ],
            [
                'name' => 'Fall 2026',
                'start_date' => '2026-09-01',
                'end_date' => '2026-11-30',
            ],
        ];

        foreach ($seasons as $data) {
            $season = new Season($data);
            if (empty($season->getKey())) {
                $season->{$season->getKeyName()} = (string) \Illuminate\Support\Str::ulid();
            }
            $season->save();
        }
    }
}
