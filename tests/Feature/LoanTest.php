<?php

namespace Tests\Feature;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoanTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed('DatabaseSeeder');
    }

    public function testCreateNewLoan()
    {
        $user = User::first();
        $this->actingAs($user);

        $response = $this->postJson('/api/loan', [
            'amount' => 100000,
            'term' => 12,
        ]);

        $response->assertStatus(201)->assertJsonFragment(['amount' => 100000]);
    }

    public function testCreateNewLoanWithInvalidAmount()
    {
        $user = User::first();
        $this->actingAs($user);

        $response = $this->postJson('/api/loan', [
            'amount' => '10000USD',
            'term' => 12,
        ]);

        $response->assertStatus(422)->assertJsonFragment(['message' => 'Validation error']);
    }

    public function testCreateNewLoanWithInvalidTerm()
    {
        $user = User::first();
        $this->actingAs($user);

        $response = $this->postJson('/api/loan', [
            'amount' => 100000,
            'term' => 12.1,
        ]);

        $response->assertStatus(422)->assertJsonFragment(['message' => 'Validation error']);
    }

    public function testGetLoanDetails()
    {
        $user = User::first();
        $this->actingAs($user);

        $response = $this->postJson('/api/loan', [
            'amount' => 100000,
            'term' => 12,
        ]);

        $loan = Loan::first();

        $response = $this->getJson('/api/loan/'.$loan->id);
        $response->assertStatus(200)->assertJsonFragment(["id" => $loan->id]);
    }

    public function testAdminApproval()
    {
        $user = User::where('email', '!=', 'admin@test.com')->first();
        $this->actingAs($user);

        $response = $this->postJson('/api/loan', [
            'amount' => 100000,
            'term' => 12,
        ]);

        $loan = Loan::first();

        $user = User::where('email', 'admin@test.com')->first();
        $this->actingAs($user);

        $response = $this->postJson('/api/loan/'.$loan->id.'/approval', [
            'approval' => 1,
        ]);

        $response->assertStatus(201)->assertJsonFragment(['message' => 'Loan approval updated successfully']);
    }

    public function testAdminApprovalWithInvalidData()
    {
        $user = User::where('email', '!=', 'admin@test.com')->first();
        $this->actingAs($user);

        $response = $this->postJson('/api/loan', [
            'amount' => 100000,
            'term' => 12,
        ]);

        $loan = Loan::first();

        $user = User::where('email', 'admin@test.com')->first();
        $this->actingAs($user);

        $response = $this->postJson('/api/loan/'.$loan->id.'/approval', [
            'approval' => 'yes',
        ]);

        $response->assertStatus(422)->assertJsonFragment(['message' => 'Validation error']);
    }

    public function testAdminApprovalWithInvalidEmail()
    {
        $user = User::where('email', '!=', 'admin@test.com')->orderBy('id', 'asc')->first();
        $this->actingAs($user);

        $response = $this->postJson('/api/loan', [
            'amount' => 100000,
            'term' => 12,
        ]);

        $loan = Loan::first();

        $user = User::where('email', '!=', 'admin@test.com')->orderBy('id', 'desc')->first();
        $this->actingAs($user);

        $response = $this->postJson('/api/loan/'.$loan->id.'/approval', [
            'approval' => 1,
        ]);

        $response->assertStatus(422)->assertJsonFragment(['data' => ['error' => 'User is not admin']]);
    }

    public function testLoanWeeklyPayment()
    {
        $user = User::first();
        $this->actingAs($user);

        $response = $this->postJson('/api/loan', [
            'amount' => 100000,
            'term' => 12,
        ]);

        $loan = Loan::latest()->first();

        $user = User::where('email', 'admin@test.com')->first();
        $this->actingAs($user);

        $response = $this->postJson('/api/loan/'.$loan->id.'/approval', [
            'approval' => 1,
        ]);

        $response = $this->postJson('/api/loan/'.$loan->id.'/payment', [
            'amount' => 1000,
        ]);

        $response->assertStatus(200)->assertJsonFragment(['message' => 'Loan repaid successfully']);
    }

    public function testLoanWeeklyPaymentNotApproved()
    {
        $user = User::first();
        $this->actingAs($user);

        $response = $this->postJson('/api/loan', [
            'amount' => 100000,
            'term' => 12,
        ]);

        $loan = Loan::latest()->first();

        $response = $this->postJson('/api/loan/'.$loan->id.'/payment', [
            'amount' => 1000,
        ]);

        $response->assertStatus(422)->assertJsonFragment(['message' => 'Loan not approved yet']);
    }
}
