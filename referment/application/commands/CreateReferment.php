<?php
namespace referment\application\commands;

class CreateReferment
{
    public static function execute(array $data): array
    {
        if (empty($data['code'])) {
            throw new \InvalidArgumentException('Referment code is required');
        }
        if (empty($data['user_id'])) {
            throw new \InvalidArgumentException('User id is required');
        }
        $reward = isset($data['reward']) ? (float) $data['reward'] : 0;
        if ($reward < 0) {
            throw new \InvalidArgumentException('Reward must be zero or positive');
        }

        return [
            'code' => strtoupper(trim($data['code'])),
            'user_id' => $data['user_id'],
            'reward' => $reward,
            'status' => $data['status'] ?? 'active',
            'expires_at' => $data['expires_at'] ?? null,
        ];
    }
}
