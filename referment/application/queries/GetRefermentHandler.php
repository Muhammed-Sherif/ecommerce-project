<?php
namespace referment\application\queries;

use referment\domains\contracts\IRefermentRepository;

class GetRefermentHandler
{
    private $repository;
    private $getReferment;

    public function __construct(IRefermentRepository $repository, GetReferment $getReferment)
    {
        $this->repository = $repository;
        $this->getReferment = $getReferment;
    }

    public function handle($id)
    {
        $referment = $this->repository->findById($id);
        if (!$referment) {
            return ['success' => false, 'message' => 'Referment not found'];
        }
        return ['success' => true, 'referment' => $this->getReferment::execute($referment)];
    }
}
