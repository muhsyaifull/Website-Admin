<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SessionTemplate;
use Carbon\Carbon;

class TourSessionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Auto-generates sessions from templates for today and upcoming days.
     */
    public function run(): void
    {
        // Generate sessions for today + next 3 days from templates
        for ($i = 0; $i <= 3; $i++) {
            SessionTemplate::ensureSessionsForDate(Carbon::today()->addDays($i));
        }
    }
}