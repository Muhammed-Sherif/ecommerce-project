<?php
namespace accounts\domains\contracts;

/**
 * Port for issuing and revoking API tokens.
 * Implementations live in outer layers (infrastructure/framework).
 */
interface ISanctumToken
{
    public function createToken($userId, $tokenName, array $abilities);
    public function revokeToken($userId, $tokenId);
    public function revokeAllTokens($userId);
}
