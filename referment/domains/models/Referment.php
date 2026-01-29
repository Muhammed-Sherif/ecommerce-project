<?php
namespace referment\domains\models;

class Referment
{
    public $id;
    public $code;
    public $userId;
    public $reward;
    public $status;
    public $expiresAt;

    public function __construct($id, string $code, $userId, float $reward, string $status, $expiresAt)
    {
        $this->id = $id;
        $this->code = $code;
        $this->userId = $userId;
        $this->reward = $reward;
        $this->status = $status;
        $this->expiresAt = $expiresAt;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['code'] ?? '',
            $data['user_id'] ?? null,
            (float) ($data['reward'] ?? 0),
            $data['status'] ?? 'active',
            $data['expires_at'] ?? null
        );
    }
}
