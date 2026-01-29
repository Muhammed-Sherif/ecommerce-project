<?php
namespace referment\application\commands;

class UpdateReferment
{
    public static function execute(array $existing, array $data): array
    {
        $updated = $existing;

        if (isset($data['code'])) {
            $updated['code'] = strtoupper(trim($data['code']));
        }
        if (isset($data['reward'])) {
            $reward = (float) $data['reward'];
            if ($reward < 0) {
                throw new \InvalidArgumentException('Reward must be zero or positive');
            }
            $updated['reward'] = $reward;
        }
        if (isset($data['status'])) {
            $updated['status'] = $data['status'];
        }
        if (array_key_exists('expires_at', $data)) {
            $updated['expires_at'] = $data['expires_at'];
        }

        return $updated;
    }
}
