<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TaskSeeder extends Seeder
{
    private const TASK_TASK = [
        [
            'parent_id' => 1,
            'child_id' => 2,
        ],
        [
            'parent_id' => 1,
            'child_id' => 3,
        ],
        [
            'parent_id' => 1,
            'child_id' => 4,
        ],
        [
            'parent_id' => 2,
            'child_id' => 5,
        ],
        [
            'parent_id' => 2,
            'child_id' => 6,
        ],
        [
            'parent_id' => 4,
            'child_id' => 7,
        ],
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Task::factory(10)->create(['user_id' => 2]);
        Task::factory(10)->create(['user_id' => 3]);
        DB::table('task_task')->insert(self::TASK_TASK);
    }
}
