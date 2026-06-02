<?php

namespace App\Services;

use App\Models\Document;
use App\Models\Signer;

class SigningTokenService
{
    public function generateToken(): string
    {
        return bin2hex(random_bytes(32)); // 64-char hex, cryptographically random
    }

    public function assignToken(Signer $signer): string
    {
        $token = $this->generateToken();

        // Ensure uniqueness (extremely unlikely collision but safe)
        while (Signer::where('token', $token)->exists()) {
            $token = $this->generateToken();
        }

        $signer->update(['token' => $token]);

        return $token;
    }

    public function signingUrl(Signer $signer): string
    {
        return route('sign.show', ['token' => $signer->token]);
    }

    public function validateToken(string $token): ?Signer
    {
        return Signer::where('token', $token)->with('document')->first();
    }
}
