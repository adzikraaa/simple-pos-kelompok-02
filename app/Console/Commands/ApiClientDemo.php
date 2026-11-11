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
    protected $signature = 'api:demo {--wrong-password : Login dengan password salah untuk mengamati penanganan gagal login}';
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

        $password = $this->option('wrong-password') ? 'salah' : 'password';

        $loginResponse = Http::acceptJson()->post("{$baseUrl}/login", [
            'email' => 'kasir@pos.test',
            'password' => $password,
        ]);

        if ($loginResponse->failed()) {
            $this->error('Login gagal (' . $loginResponse->status() . '): ' . $loginResponse->json('message'));
            return self::FAILURE;
        }

        $token = $loginResponse->json('token'); 
        $this->info('1. Login berhasil, token didapatkan.');

        $client = Http::withToken($token)
                    ->acceptJson()
                    ->retry(3, 100); 

        $productsResponse = $client->get("{$baseUrl}/products");
        if ($productsResponse->successful()) {
            $this->info('2. Berhasil mengambil data produk (Status 200).');
        } else {
            $this->error('Gagal mengambil data produk: ' . $productsResponse->body());
        }

        $idempotencyKey = (string) \Illuminate\Support\Str::uuid();
        $transactionData = [
            'items' => [
                [
                    'product_id' => 1,
                    'qty' => 2,
                ]
            ]
        ];

        $this->info("3. POST transaksi dengan Idempotency-Key: {$idempotencyKey}");

$response1 = $client->withHeaders(['Idempotency-Key' => $idempotencyKey])
    ->post("{$baseUrl}/transactions", $transactionData);

$this->info("   -> Response 1 Status: " . $response1->status());

$this->info("4. Replay POST transaksi dengan key yang sama...");

$response2 = $client->withHeaders(['Idempotency-Key' => $idempotencyKey])
    ->post("{$baseUrl}/transactions", $transactionData);
        
        $replayed = $response2->header('Idempotent-Replayed') ? 'true' : 'false';
        $this->info("   -> Response 2 Status: " . $response2->status() . " | replayed = {$replayed}");

        $this->info('Demo selesai!');
    }
}