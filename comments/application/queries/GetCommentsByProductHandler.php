<?php
namespace comments\application\queries;

use comments\domains\contracts\ICommentRepository;

class GetCommentsByProductHandler
{
    private $repository;
    private $getAllComments;

    public function __construct(ICommentRepository $repository, GetAllComments $getAllComments)
    {
        $this->repository = $repository;
        $this->getAllComments = $getAllComments;
    }

    public function handle($productId)
    {
        $comments = $this->repository->getByProduct($productId);
        return ['success' => true, 'comments' => $this->getAllComments::execute($comments ?? [])];
    }
}
