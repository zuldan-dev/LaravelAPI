<?php

namespace Tests\Feature;

use App\Enums\TaskStatusEnum;
use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ApiTaskViewTest extends TestCase
{
    use DatabaseTransactions;

    protected const TEST_TASKS_COUNT = 5;
    protected const WRONG_TASK_ID = 9999;

    protected User $testUser;

    public function setUp(): void
    {
        parent::setUp();

        // Test user creating
        $this->testUser = User::factory()->create([
            'email' => config('services.test_user.email'),
            'password' => Hash::make(config('services.test_user.password')),
        ]);

        // Test tasks creating
        Task::factory(self::TEST_TASKS_COUNT)->create(['user_id' => $this->testUser->id]);

        // Test Done task creating
        Task::factory()->create([
            'user_id' => $this->testUser->id,
            'status' => TaskStatusEnum::done,
        ]);
    }

    public function tearDown(): void
    {
        Task::where('user_id', $this->testUser->id)->delete();

        parent::tearDown();
    }

    public function testShowTasksSuccess(): void
    {
        // Get all tasks in list mode
        $response = $this->actingAs($this->testUser)->get('api/tasks');
        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(self::TEST_TASKS_COUNT + 1, $response->json()['tasks']);

        // Filter tasks by status
        $response = $this->actingAs($this->testUser)->get('api/tasks?status=' . TaskStatusEnum::done->value);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(1, $response->json()['tasks']);

        // Sort task by priority
        $response = $this->actingAs($this->testUser)->get('api/tasks?sort_fields[]=priority&directions[]=desc');
        $response->assertStatus(Response::HTTP_OK);
        $firstElement = reset($response->json()['tasks']);
        $lastElement = end($response->json()['tasks']);
        $this->assertLessThanOrEqual($firstElement['priority'], $lastElement['priority']);
    }

    public function testShowTaskSuccess(): void
    {
        $lastTaskId = Task::latest()->first()->id;
        $response = $this->actingAs($this->testUser)->get('api/tasks/' . $lastTaskId);
        $response->assertStatus(Response::HTTP_OK)->assertJson(['tasks' => ['id' => $lastTaskId]]);
    }

    public function testShowWrongTask(): void
    {
        $response = $this->actingAs($this->testUser)->get('api/tasks/' . self::WRONG_TASK_ID);
        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $this->assertEmpty($response->json()['tasks']);
    }

    public function testShowTasksTreeSuccess(): void
    {
        $response = $this->actingAs($this->testUser)->get('api/tasks/tree');
        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(self::TEST_TASKS_COUNT + 1, $response->json()['tasks']);
    }
}
