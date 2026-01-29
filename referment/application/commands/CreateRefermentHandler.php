<?php
namespace referment\application\commands;

use referment\domains\contracts\IRefermentRepository;

class CreateRefermentHandler
{
    private $repository;
    private $createReferment;

    public function __construct(IRefermentRepository $repository, CreateReferment $createReferment)
    {
        $this->repository = $repository;
        $this->createReferment = $createReferment;
    }

    public function handle(array $data)
    {
        $payload = $this->createReferment::execute($data);
        $existing = $this->repository->findByCode($payload['code']);
        if ($existing) {
            return ['success' => false, 'message' => 'Referment code already exists'];
        }
        $id = $this->repository->create($payload);
        $created = $this->repository->findById($id);
        return ['success' => true, 'referment' => $created ?? $payload];
    }
}
