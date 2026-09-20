<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class PaystackService
{
    private function client()
    {
        return Http::withToken(config('services.paystack.secret_key'))
            ->acceptJson()
            ->baseUrl(
                config('services.paystack.base_url', 'https://api.paystack.co'),
            );
    }

    public function initialize(
        string $email,
        int $amountKobo,
        string $reference,
        string $callback,
    ): array {
        $r = $this->client()->post('/transaction/initialize', [
            'email' => $email,
            'amount' => $amountKobo,
            'reference' => $reference,
            'callback_url' => $callback,
        ]);
        if (! $r->successful() || ! $r->json('status')) {
            throw new RuntimeException('Unable to initialize payment.');
        }

        return $r->json('data');
    }

    public function verify(string $reference): array
    {
        $r = $this->client()->get(
            '/transaction/verify/'.rawurlencode($reference),
        );
        if (! $r->successful() || ! $r->json('status')) {
            throw new RuntimeException('Unable to verify payment.');
        }

        return $r->json('data');
    }

    public function banks(): array
    {
        $r = $this->client()->get('/bank', [
            'country' => 'nigeria',
            'currency' => 'NGN',
            'perPage' => 100,
        ]);
        if (! $r->successful() || ! $r->json('status')) {
            throw new RuntimeException('Unable to load Nigerian banks.');
        }

        return collect($r->json('data', []))
            ->filter(
                fn ($bank) => ($bank['active'] ?? false) &&
                    ! ($bank['is_deleted'] ?? false),
            )
            ->sortBy('name')
            ->values()
            ->all();
    }

    public function resolveAccount(
        string $accountNumber,
        string $bankCode,
    ): array {
        $r = $this->client()->get('/bank/resolve', [
            'account_number' => $accountNumber,
            'bank_code' => $bankCode,
        ]);
        if (! $r->successful() || ! $r->json('status')) {
            throw new RuntimeException(
                'We could not verify that bank account.',
            );
        }

        return $r->json('data');
    }

    public function createTransferRecipient(
        string $name,
        string $accountNumber,
        string $bankCode,
    ): array {
        $r = $this->client()->post('/transferrecipient', [
            'type' => 'nuban',
            'name' => $name,
            'account_number' => $accountNumber,
            'bank_code' => $bankCode,
            'currency' => 'NGN',
        ]);
        if (! $r->successful() || ! $r->json('status')) {
            throw new RuntimeException(
                'Unable to prepare this account for payouts.',
            );
        }

        return $r->json('data');
    }

    public function validWebhook(string $payload, ?string $signature): bool
    {
        $secret = (string) config('services.paystack.secret_key');

        return $secret !== '' &&
            is_string($signature) &&
            hash_equals(hash_hmac('sha512', $payload, $secret), $signature);
    }
}
