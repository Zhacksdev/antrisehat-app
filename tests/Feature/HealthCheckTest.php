<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    use RefreshDatabase;

    /** Test #1: /health mengembalikan 200 dan status database. */
    public function test_health_mengembalikan_200_ketika_database_terhubung(): void
    {
        $this->getJson('/health')
            ->assertOk()
            ->assertJson(['status' => 'ok', 'database' => 'connected'])
            ->assertJsonStructure(['status', 'database', 'timestamp']);
    }

    public function test_health_mengembalikan_503_ketika_database_tidak_terjangkau(): void
    {
        config(['database.connections.rusak' => [
            'driver' => 'sqlite',
            'database' => '/path/yang/tidak/ada/antrisehat.sqlite',
            'foreign_key_constraints' => false,
        ]]);
        $asli = config('database.default');
        config(['database.default' => 'rusak']);
        DB::purge('rusak');

        try {
            $this->getJson('/health')
                ->assertStatus(503)
                ->assertJson(['status' => 'error', 'database' => 'disconnected']);
        } finally {
            // Kembalikan koneksi agar RefreshDatabase bisa rollback dengan normal.
            config(['database.default' => $asli]);
        }
    }
}
