<?php
namespace copons\application\queries;

use copons\domains\contracts\ICoponRepository;

class GetCoponHandler
{
    private $repository;
    private $getCopon;

    public function __construct(ICoponRepository $repository, GetCopon $getCopon)
    {
        $this->repository = $repository;
        $this->getCopon = $getCopon;
    }

    public function handle($id)
    {
        $copon = $this->repository->findById($id);
        if (!$copon) {
            return ['success' => false, 'message' => 'Copon not found'];
        }
        return ['success' => true, 'copon' => $this->getCopon::execute($copon)];
    }
}
