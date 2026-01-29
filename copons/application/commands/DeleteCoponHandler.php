<?php
namespace copons\application\commands;

use copons\domains\contracts\ICoponRepository;

class DeleteCoponHandler
{
    private $repository;
    private $deleteCopon;

    public function __construct(ICoponRepository $repository, DeleteCopon $deleteCopon)
    {
        $this->repository = $repository;
        $this->deleteCopon = $deleteCopon;
    }

    public function handle($id)
    {
        $coponId = $this->deleteCopon::execute($id);
        $existing = $this->repository->findById($coponId);
        if (!$existing) {
            return ['success' => false, 'message' => 'Copon not found'];
        }
        $this->repository->delete($coponId);
        return ['success' => true, 'message' => 'Copon deleted'];
    }
}
