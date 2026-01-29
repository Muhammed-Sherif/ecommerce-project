<?php
namespace referment\application\queries;

use referment\domains\contracts\IRefermentRepository;

class GetRefermentsByUserHandler
{
    private $repository;
    private $getAllReferments;

    public function __construct(IRefermentRepository $repository, GetAllReferments $getAllReferments)
    {
        $this->repository = $repository;
        $this->getAllReferments = $getAllReferments;
    }

    public function handle($userId)
    {
        $referments = $this->repository->getByUser($userId);
        return ['success' => true, 'referments' => $this->getAllReferments::execute($referments ?? [])];
    }
}
