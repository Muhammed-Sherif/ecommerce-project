<?php
namespace comments\application\queries;

use comments\domains\contracts\ICommentRepository;

class GetCommentsByUserHandler
{
    private $repository;
    private $getAllComments;

    public function __construct(ICommentRepository $repository, GetAllComments $getAllComments)
    {
        $this->repository = $repository;
        $this->getAllComments = $getAllComments;
    }

    public function handle($userId)
    {
        $comments = $this->repository->getByUser($userId);
        return ['success' => true, 'comments' => $this->getAllComments::execute($comments ?? [])];
    }
}
