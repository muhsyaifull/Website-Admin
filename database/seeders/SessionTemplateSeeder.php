<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SessionTemplate;
use App\Models\Tour;

class SessionTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $taman = Tour::where('slug', 'taman')->first();
        $museum = Tour::where('slug', 'museum')->first();

        // Default Taman Template (Weekday)
        $tamanDefault = SessionTemplate::create([
            'name' => 'Default Weekday',
            'tour_id' => $taman->id,
            'description' => 'Template default untuk hari kerja Taman Atsiri',
            'is_default' => true,
            'apply_days' => [1, 2, 3, 4, 5],
            'is_active' => true,
        ]);

        $tamanDefault->slots()->createMany([
            ['start_time' => '08:30', 'end_time' => '09:30', 'capacity' => 15, 'educator_id' => 1, 'sort_order' => 1],
            ['start_time' => '10:00', 'end_time' => '11:00', 'capacity' => 15, 'educator_id' => 1, 'sort_order' => 2],
            ['start_time' => '11:30', 'end_time' => '12:30', 'capacity' => 15, 'educator_id' => 2, 'sort_order' => 3],
        ]);

        // Weekend Taman Template
        $tamanWeekend = SessionTemplate::create([
            'name' => 'Weekend',
            'tour_id' => $taman->id,
            'description' => 'Template untuk akhir pekan Taman Atsiri (lebih banyak slot)',
            'is_default' => false,
            'apply_days' => [0, 6],
            'is_active' => true,
        ]);

        $tamanWeekend->slots()->createMany([
            ['start_time' => '08:00', 'end_time' => '09:00', 'capacity' => 20, 'educator_id' => 1, 'sort_order' => 1],
            ['start_time' => '09:30', 'end_time' => '10:30', 'capacity' => 20, 'educator_id' => 1, 'sort_order' => 2],
            ['start_time' => '11:00', 'end_time' => '12:00', 'capacity' => 20, 'educator_id' => 2, 'sort_order' => 3],
            ['start_time' => '13:00', 'end_time' => '14:00', 'capacity' => 20, 'educator_id' => 2, 'sort_order' => 4],
            ['start_time' => '14:30', 'end_time' => '15:30', 'capacity' => 20, 'educator_id' => 1, 'sort_order' => 5],
        ]);

        // Default Museum Template (Weekday)
        $museumDefault = SessionTemplate::create([
            'name' => 'Default Weekday',
            'tour_id' => $museum->id,
            'description' => 'Template default untuk hari kerja Museum Atsiri',
            'is_default' => true,
            'apply_days' => [1, 2, 3, 4, 5],
            'is_active' => true,
        ]);

        $museumDefault->slots()->createMany([
            ['start_time' => '09:00', 'end_time' => '10:00', 'capacity' => 12, 'educator_id' => 3, 'sort_order' => 1],
            ['start_time' => '10:30', 'end_time' => '11:30', 'capacity' => 12, 'educator_id' => 3, 'sort_order' => 2],
            ['start_time' => '13:30', 'end_time' => '14:30', 'capacity' => 12, 'educator_id' => 4, 'sort_order' => 3],
        ]);

        // Weekend Museum Template
        $museumWeekend = SessionTemplate::create([
            'name' => 'Weekend',
            'tour_id' => $museum->id,
            'description' => 'Template untuk akhir pekan Museum Atsiri',
            'is_default' => false,
            'apply_days' => [0, 6],
            'is_active' => true,
        ]);

        $museumWeekend->slots()->createMany([
            ['start_time' => '09:00', 'end_time' => '10:00', 'capacity' => 15, 'educator_id' => 3, 'sort_order' => 1],
            ['start_time' => '10:30', 'end_time' => '11:30', 'capacity' => 15, 'educator_id' => 3, 'sort_order' => 2],
            ['start_time' => '12:00', 'end_time' => '13:00', 'capacity' => 15, 'educator_id' => 4, 'sort_order' => 3],
            ['start_time' => '14:00', 'end_time' => '15:00', 'capacity' => 15, 'educator_id' => 4, 'sort_order' => 4],
            ['start_time' => '15:30', 'end_time' => '16:30', 'capacity' => 15, 'educator_id' => 3, 'sort_order' => 5],
        ]);
    }
}
