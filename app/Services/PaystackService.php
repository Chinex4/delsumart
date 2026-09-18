<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class PaystackService
{
    private function client()
    {
        return Http::withToken(config('services.paystack.secret_key'))->acceptJson()->baseUrl(config('services.paystack.base_url', 'https://api.paystack.co'));
    }

    public function initialize(string $email, int $amountKobo, string $reference, string $callback): array
    {
        $r = $this->client()->post('/transaction/initialize', ['email' => $email, 'amount' => $amountKobo, 'reference' => $reference, 'callback_url' => $callback]);
        if (! $r->successful() || ! $r->json('status')) {
            throw new RuntimeException('Unable to initialize payment.');
        }

return $r->json('data');
    }

    public function verify(string $reference): array
    {
        $r = $this->client()->get('/transaction/verify/'.rawurlencode($reference));
        if (! $r->successful() || ! $r->json('status')) {
            throw new RuntimeException('Unable to verify payment.');
        }

return $r->json('data');
    }

    public function validWebhook(string $payload, ?string $signature): bool
    {
        $secret = (string) config('services.paystack.secret_key');

        return $secret !== '' && is_string($signature) && hash_equals(hash_hmac('sha512', $payload, $secret), $signature);
    }
}
