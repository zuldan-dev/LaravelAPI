<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class ApiAuthTest extends TestCase
{
    use DatabaseTransactions;

    protected const INCORRECT_USER = [
        'email' => 'wrong@user.test',
        'password' => '****',
    ];

    protected User $testUser;

    /**
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->testUser = User::factory()->create([
            'email' => config('services.test_user.email'),
            'password' => Hash::make(config('services.test_user.password')),
        ]);
    }

    public function tearDown(): void
    {
        $this->testUser->delete();

        parent::tearDown();
    }

    /**
     * Success login test
     * @return void
     */
    public function testApiLoginSuccess(): void
    {
        $response = $this->postJson('api/login', config('services.test_user'));
        $response->assertStatus(Response::HTTP_OK)->assertJsonStructure(['token']);
    }

    /**
     * Failed login test
     * @return void
     */
    public function testApiLoginFail(): void
    {
        $response = $this->postJson('api/login', self::INCORRECT_USER);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED)->assertExactJson([
            'error' => User::LOGIN_ERROR_MESSAGE,
        ]);
    }

    /**
     * Success logout test
     * @return void
     */
    public function testApiLogoutSuccess(): void
    {
        $response = $this->actingAs($this->testUser)->postJson('/api/logout');
        $response->assertStatus(200)->assertExactJson(['message' => User::LOGOUT_MESSAGE]);
        $this->assertNull($this->testUser->tokens->first());
    }
}
