<?php

use App\Enums\TaskStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const TABLE_NAME = 'tasks';
    private const TABLE_USERS = 'users';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable(self::TABLE_NAME)) {
            Schema::create(self::TABLE_NAME, function (Blueprint $table) {
                $table->id();
                $table->enum('status', array_column(TaskStatusEnum::cases(), 'value'))
                    ->default(TaskStatusEnum::todo);
                $table->enum('priority', range(1, 5))->default(1);
                $table->string('title', 255);
                $table->text('description')->nullable();
                $table->unsignedBigInteger('user_id');
                $table->timestamps();
                $table->timestamp('completed_at')->nullable();

                $table->foreign('user_id')
                    ->references('id')
                    ->on(self::TABLE_USERS)
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
