<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiExpiredTokenTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Endpoint API yang dilindungi autentikasi Sanctum.
     */
    private string $endpoint = '/api/v1/products';

    /**
     * Menyiapkan data kategori dan produk untuk pengujian respon sukses (200 OK).
     */
    private function createSampleProduct(): Product
    {
        $category = Category::forceCreate([
            'name' => 'Elektronik',
        ]);

        return Product::create([
            'category_id' => $category->id,
            'name' => 'Mouse Wireless',
            'price' => 150000,
            'stock' => 10,
        ]);
    }

    /**
     * Uji bahwa token dengan masa berlaku lampau (sudah kedaluwarsa) ditolak dengan HTTP 401.
     */
    public function test_expired_token_is_rejected_with_401_unauthorized(): void
    {
        $user = User::factory()->create();

        // 1. Buat token Sanctum dengan expires_at di masa lalu (misal: 1 jam yang lalu)
        $expiredToken = $user->createToken(
            'expired-test-token',
            ['products:read'],
            now()->subHour()
        )->plainTextToken;

        // Pastikan token benar-benar tersimpan di database dan memiliki waktu expires_at lampau
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'name' => 'expired-test-token',
        ]);

        // 2. Akses endpoint terproteksi menggunakan token kedaluwarsa
        $response = $this->withToken($expiredToken)->getJson($this->endpoint);

        // 3. API menolak token kedaluwarsa dengan HTTP 401 Unauthorized
        $response->assertUnauthorized();
    }

    /**
     * Uji pembanding (baseline): token yang masih aktif (belum kedaluwarsa) berhasil mengakses endpoint dengan HTTP 200.
     * Ini membuktikan bahwa penolakan pada tes kedaluwarsa bukan karena token invalid atau kesalahan hak akses.
     */
    public function test_valid_unexpired_token_can_access_protected_endpoint(): void
    {
        $this->createSampleProduct();
        $user = User::factory()->create();

        // Buat token yang valid dengan masa berlaku di masa depan (1 hari ke depan)
        $validToken = $user->createToken(
            'valid-test-token',
            ['products:read'],
            now()->addDay()
        )->plainTextToken;

        $response = $this->withToken($validToken)->getJson($this->endpoint);

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'price', 'category'],
                ],
            ]);
    }

    /**
     * Uji siklus hidup token: token yang awalnya aktif menjadi tidak sah setelah melewati waktu kedaluwarsa (time travel).
     */
    public function test_token_becomes_unauthorized_after_expiration_time_passes(): void
    {
        $this->createSampleProduct();
        $user = User::factory()->create();

        // Token berlaku selama 30 menit ke depan
        $token = $user->createToken(
            'lifecycle-test-token',
            ['products:read'],
            now()->addMinutes(30)
        )->plainTextToken;

        // Sebelum kedaluwarsa: harus berhasil (HTTP 200)
        $this->withToken($token)->getJson($this->endpoint)
            ->assertOk();

        // Majukan waktu melewati masa berlaku (misal: maju 31 menit)
        $this->travel(31)->minutes();

        // Reset state auth guard agar request berikutnya mengevaluasi ulang token
        auth()->forgetGuards();

        // Setelah kedaluwarsa: harus ditolak (HTTP 401 Unauthorized)
        $this->withToken($token)->getJson($this->endpoint)
            ->assertUnauthorized();
    }

    /**
     * Uji kedaluwarsa berdasarkan konfigurasi global `sanctum.expiration` (dalam menit).
     */
    public function test_token_is_rejected_when_exceeding_sanctum_config_expiration(): void
    {
        $this->createSampleProduct();
        $user = User::factory()->create();

        // Atur masa kedaluwarsa Sanctum secara dinamis pada sesi pengujian (60 menit)
        config(['sanctum.expiration' => 60]);
        auth()->forgetGuards();

        // Buat token tanpa menentukan expires_at per-token (mengandalkan config global)
        $token = $user->createToken(
            'global-config-token',
            ['products:read']
        )->plainTextToken;

        // Akses awal sebelum 60 menit: berhasil (HTTP 200)
        $this->withToken($token)->getJson($this->endpoint)
            ->assertOk();

        // Majukan waktu 61 menit
        $this->travel(61)->minutes();
        auth()->forgetGuards();

        // Setelah batas config terlampaui: ditolak (HTTP 401 Unauthorized)
        $this->withToken($token)->getJson($this->endpoint)
            ->assertUnauthorized();
    }
}
