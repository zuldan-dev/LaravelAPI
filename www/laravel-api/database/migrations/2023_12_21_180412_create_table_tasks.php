<?php

use App\Enums\TaskStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const TABLE_NAME = 'tasks';
    private const TABLE_USERS = 'users';
    private const FULLTEXT_INDEXES = [
        'title' => 'title_fulltext_index',
        'description' => 'description_fulltext_index',
    ];

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
                $table->string('description', 512)->nullable();
                $table->unsignedBigInteger('user_id');
                $table->timestamps();
                $table->timestamp('completed_at')->nullable();

                $table->foreign('user_id')
                    ->references('id')
                    ->on(self::TABLE_USERS)
                    ->onDelete('cascade');

                $table->engine = 'InnoDB';
            });

            foreach (self::FULLTEXT_INDEXES as $field => $index) {
                DB::statement('ALTER TABLE ' . self::TABLE_NAME . ' ADD FULLTEXT INDEX ' . $index . ' (' . $field . ')');
            }
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
