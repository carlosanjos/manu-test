<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function testCustomerChunkedUpdated(): void
    {
        $customers = \App\Models\Customer::factory(1000)->create();

        $this->assertCount(1000, $customers);

        $customersChunk = $customers->chunk(100);

        foreach ($customersChunk as $customer) {
            $customer->each->update(['name' => 'pong']);
        }

        $this->assertCount(1000, $customers);
        $this->assertDatabaseHas('customers', ['name' => 'pong']);
    }

    public function testCustomerChunkedUpdatedClosure(): void
    {
        $customers = \App\Models\Customer::factory(1000)->create();

        $this->assertCount(1000, $customers);

        $customers->chunk(100)->map(function ($customerChunk) {
            $customerChunk->each->update(["name"=> "pong"]);
        });

        $this->assertCount(1000, $customers);
        $this->assertDatabaseHas('customers', ['name' => 'pong']);
    }

    public function testCustomerChunkedUpdatedByQuery(): void
    {
        $customers = \App\Models\Customer::factory(1000)->create();

        $this->assertCount(1000, $customers);

        \App\Models\Customer::where('name', 'ping')->chunk(100, fn ($customerChunk) => $customerChunk->each->update(['name' => 'pong']));

        $this->assertCount(1000, $customers);
        $this->assertDatabaseHas('customers', ['name' => 'pong']);
    }
}
