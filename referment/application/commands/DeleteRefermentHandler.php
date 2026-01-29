<?php
namespace referment\application\commands;

use referment\domains\contracts\IRefermentRepository;

class DeleteRefermentHandler
{
    private $repository;
    private $deleteReferment;

    public function __construct(IRefermentRepository $repository, DeleteReferment $deleteReferment)
    {
        $this->repository = $repository;
        $this->deleteReferment = $deleteReferment;
    }

    public function handle($id)
    {
        $refermentId = $this->deleteReferment::execute($id);
        $existing = $this->repository->findById($refermentId);
        if (!$existing) {
            return ['success' => false, 'message' => 'Referment not found'];
        }
        $this->repository->delete($refermentId);
        return ['success' => true, 'message' => 'Referment deleted'];
    }
}
