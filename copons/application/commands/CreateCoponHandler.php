<?php
namespace copons\application\commands;

use copons\domains\contracts\ICoponRepository;

class CreateCoponHandler
{
    private $repository;
    private $createCopon;

    public function __construct(ICoponRepository $repository, CreateCopon $createCopon)
    {
        $this->repository = $repository;
        $this->createCopon = $createCopon;
    }

    public function handle(array $data)
    {
        $payload = $this->createCopon::execute($data);
        $existing = $this->repository->findByCode($payload['code']);
        if ($existing) {
            return ['success' => false, 'message' => 'Copon code already exists'];
        }
        $id = $this->repository->create($payload);
        $created = $this->repository->findById($id);
        return ['success' => true, 'copon' => $created ?? $payload];
    }
}
