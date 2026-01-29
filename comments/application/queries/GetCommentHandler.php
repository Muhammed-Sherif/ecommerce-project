<?php
namespace comments\application\queries;

use comments\domains\contracts\ICommentRepository;

class GetCommentHandler
{
    private $repository;
    private $getComment;

    public function __construct(ICommentRepository $repository, GetComment $getComment)
    {
        $this->repository = $repository;
        $this->getComment = $getComment;
    }

    public function handle($id)
    {
        $comment = $this->repository->findById($id);
        if (!$comment) {
            return ['success' => false, 'message' => 'Comment not found'];
        }
        return ['success' => true, 'comment' => $this->getComment::execute($comment)];
    }
}
