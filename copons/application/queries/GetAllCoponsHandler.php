<?php
namespace copons\application\queries;

use copons\domains\contracts\ICoponRepository;

class GetAllCoponsHandler
{
    private $repository;
    private $getAllCopons;

    public function __construct(ICoponRepository $repository, GetAllCopons $getAllCopons)
    {
        $this->repository = $repository;
        $this->getAllCopons = $getAllCopons;
    }

    public function handle()
    {
        $copons = $this->repository->getAll();
        return ['success' => true, 'copons' => $this->getAllCopons::execute($copons ?? [])];
    }
}
