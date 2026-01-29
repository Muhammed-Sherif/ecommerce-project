<?php
namespace copons\application\queries;

use copons\domains\contracts\ICoponRepository;

class GetCoponByCodeHandler
{
    private $repository;
    private $getCopon;

    public function __construct(ICoponRepository $repository, GetCopon $getCopon)
    {
        $this->repository = $repository;
        $this->getCopon = $getCopon;
    }

    public function handle($code)
    {
        $copon = $this->repository->findByCode($code);
        if (!$copon) {
            return ['success' => false, 'message' => 'Copon not found'];
        }
        return ['success' => true, 'copon' => $this->getCopon::execute($copon)];
    }
}
