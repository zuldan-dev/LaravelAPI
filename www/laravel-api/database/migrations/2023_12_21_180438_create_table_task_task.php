<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE_NAME = 'tasks';
    private const PIVOT_TABLE_NAME = 'task_task';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable(self::PIVOT_TABLE_NAME)) {
            Schema::create(self::PIVOT_TABLE_NAME, function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('parent_id');
                $table->unsignedBigInteger('child_id');

                $table->foreign('parent_id')
                    ->references('id')
                    ->on(self::TABLE_NAME)
                    ->onDelete('cascade');
                $table->foreign('child_id')
                    ->references('id')
                    ->on(self::TABLE_NAME)
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::PIVOT_TABLE_NAME);
    }
};
