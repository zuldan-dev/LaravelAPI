<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    private const SEEDER_ENV = 'local';

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (env('APP_ENV') === self::SEEDER_ENV) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            DB::table('task_task')->truncate();
            DB::table('tasks')->truncate();
            DB::table('users')->truncate();
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            $this->call(UserSeeder::class);
            $this->call(TaskSeeder::class);
        }
    }
}
