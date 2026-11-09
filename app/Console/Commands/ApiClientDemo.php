<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ApiClientDemo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:demo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Klien HTTP demo untuk konsumsi API (Langkah 5 & 8)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $baseUrl = 'http://127.0.0.1:8000/api';
        $this->info('Memulai Klien HTTP Demo...');

        // 1. Endpoint Login (Pastikan Adzikra sudah menyelesaikan L1 & L2)
        $loginResponse = Http::acceptJson()->post("{$baseUrl}/login", [
            'email' => 'kasir@pos.test',
            'password' => 'password',
        ]);

        if ($loginResponse->failed()) {
            $this->error('Login gagal: ' . $loginResponse->body());
            return;
        }

        // Ambil token dari response JSON (sesuaikan key-nya jika struktur JSON dari Adzikra berbeda)
        $token = $loginResponse->json('token'); 
        $this->info('1. Login berhasil, token didapatkan.');

        // 2. Setup HTTP Client dengan Token dan Retry (Langkah 8)
        $client = Http::withToken($token)
                    ->acceptJson()
                    ->retry(3, 100); // Coba ulang 3 kali dengan jeda 100ms jika terjadi error server

        // 3. GET /products (Langkah 5)
        $productsResponse = $client->get("{$baseUrl}/products");
        if ($productsResponse->successful()) {
            $this->info('2. Berhasil mengambil data produk (Status 200).');
        } else {
            $this->error('Gagal mengambil data produk: ' . $productsResponse->body());
        }

        // 4. POST /transactions dengan Idempotency Key (Langkah 8)
        $idempotencyKey = (string) Str::uuid();
        $this->info("3. Mengirim transaksi POST dengan Idempotency-Key: {$idempotencyKey}");

        $transactionData = [
            'product_id' => 1, // Pastikan ID ini ada di database seed
            'quantity' => 2,
        ];

        // Request Pertama (Seharusnya menghasilkan 201 Created)
        $response1 = $client->withHeaders([
            'Idempotency-Key' => $idempotencyKey
        ])->post("{$baseUrl}/transactions", $transactionData);

        $this->info('   -> Response 1 Status: ' . $response1->status());

        // Request Kedua (Replay - Seharusnya menghasilkan 201 tapi dengan replayed=true)
        $this->info('4. Mengirim ulang (replay) transaksi dengan key yang sama...');
        $response2 = $client->withHeaders([
            'Idempotency-Key' => $idempotencyKey
        ])->post("{$baseUrl}/transactions", $transactionData);

        $this->info('   -> Response 2 Status: ' . $response2->status());
        
        // Mengecek header Idempotent-Replayed
        if ($response2->header('Idempotent-Replayed') === 'true') {
            $this->info('   -> Header Idempotent-Replayed terdeteksi: true');
        }

        $this->info('Demo selesai!');
    }
}