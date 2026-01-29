<?php
namespace comments\application\queries;

use comments\domains\contracts\ICommentRepository;

class GetAllCommentsHandler
{
    private $repository;
    private $getAllComments;

    public function __construct(ICommentRepository $repository, GetAllComments $getAllComments)
    {
        $this->repository = $repository;
        $this->getAllComments = $getAllComments;
    }

    public function handle()
    {
        $comments = $this->repository->getAll();
        return ['success' => true, 'comments' => $this->getAllComments::execute($comments ?? [])];
    }
}
