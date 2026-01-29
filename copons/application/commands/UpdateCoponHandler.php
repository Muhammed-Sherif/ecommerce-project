<?php
namespace copons\application\commands;

use copons\domains\contracts\ICoponRepository;

class UpdateCoponHandler
{
    private $repository;
    private $updateCopon;

    public function __construct(ICoponRepository $repository, UpdateCopon $updateCopon)
    {
        $this->repository = $repository;
        $this->updateCopon = $updateCopon;
    }

    public function handle($id, array $data)
    {
        $existing = $this->repository->findById($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Copon not found'];
        }
        $updated = $this->updateCopon::execute((array) $existing, $data);
        $this->repository->update($id, $updated);
        $fresh = $this->repository->findById($id);
        return ['success' => true, 'copon' => $fresh ?? $updated];
    }
}
