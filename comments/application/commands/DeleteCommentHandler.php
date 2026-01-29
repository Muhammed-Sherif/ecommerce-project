<?php
namespace comments\application\commands;

use comments\domains\contracts\ICommentRepository;

class DeleteCommentHandler
{
    private $repository;
    private $deleteComment;

    public function __construct(ICommentRepository $repository, DeleteComment $deleteComment)
    {
        $this->repository = $repository;
        $this->deleteComment = $deleteComment;
    }

    public function handle($id)
    {
        $commentId = $this->deleteComment::execute($id);
        $existing = $this->repository->findById($commentId);
        if (!$existing) {
            return ['success' => false, 'message' => 'Comment not found'];
        }
        $this->repository->delete($commentId);
        return ['success' => true, 'message' => 'Comment deleted'];
    }
}
