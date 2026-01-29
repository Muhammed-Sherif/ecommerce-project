<?php
namespace referment\application\queries;

use referment\domains\contracts\IRefermentRepository;

class GetAllRefermentsHandler
{
    private $repository;
    private $getAllReferments;

    public function __construct(IRefermentRepository $repository, GetAllReferments $getAllReferments)
    {
        $this->repository = $repository;
        $this->getAllReferments = $getAllReferments;
    }

    public function handle()
    {
        $referments = $this->repository->getAll();
        return ['success' => true, 'referments' => $this->getAllReferments::execute($referments ?? [])];
    }
}
