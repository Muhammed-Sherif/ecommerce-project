<?php   
namespace framwork\shared;
use accounts\domains\contracts\ISanctumToken;
use App\Models\User;
/**
 * Sanctum Token Implementation
 * Wraps Sanctum functionality to keep framework in shared layer
 */
class SanctumTokenImp implements ISanctumToken
{ 
    /**
     * Create a new API token for a user
     */
    public function createToken($userId, $tokenName = 'api-token', array $abilities )
    {   
        return User::find($userId)->createToken($tokenName, $abilities)->plainTextToken;
    }
    public function revokeToken($userId, $tokenId)
    {
       User::find($userId)->tokens()->where('id', $tokenId)->delete();
    }
    public function revokeAllTokens($userId)
    {
        User::find($userId)->tokens()->delete();
    }
}
