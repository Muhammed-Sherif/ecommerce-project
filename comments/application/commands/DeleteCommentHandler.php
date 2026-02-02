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

        $user = auth()->user();
        if (!$user) {
            return ['success' => false, 'message' => 'Authentication required'];
        }
        if (($user->role !== 'admin' || strtolower((string) ($user->status ?? '')) !== 'active') && (int) $existing->user_id !== (int) $user->id) {
            return ['success' => false, 'message' => 'Forbidden'];
        }
        $this->repository->delete($commentId);
        return ['success' => true, 'message' => 'Comment deleted'];
    }
}
