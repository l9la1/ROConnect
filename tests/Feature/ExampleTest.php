<?php

<<<<<<< HEAD
test('returns a successful response', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
});
=======
namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
>>>>>>> 9c8a8b2 (Initialize Laravel WebRTC Video Call project)
