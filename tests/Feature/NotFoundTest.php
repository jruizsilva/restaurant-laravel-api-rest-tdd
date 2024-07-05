<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class NotFoundTest extends TestCase
{
    #[Test]
    public function throw_not_found_exception_when_route_does_not_exist(): void
    {
        $response = $this->get('/sdasdsadasdsa');
        dd($response->json());
        $response->assertStatus(200);
    }
}