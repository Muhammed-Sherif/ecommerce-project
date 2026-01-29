<?php
namespace referment\application\commands;

use referment\domains\contracts\IRefermentRepository;

class UpdateRefermentHandler
{
    private $repository;
    private $updateReferment;

    public function __construct(IRefermentRepository $repository, UpdateReferment $updateReferment)
    {
        $this->repository = $repository;
        $this->updateReferment = $updateReferment;
    }

    public function handle($id, array $data)
    {
        $existing = $this->repository->findById($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Referment not found'];
        }
        $updated = $this->updateReferment::execute((array) $existing, $data);
        $this->repository->update($id, $updated);
        $fresh = $this->repository->findById($id);
        return ['success' => true, 'referment' => $fresh ?? $updated];
    }
}
