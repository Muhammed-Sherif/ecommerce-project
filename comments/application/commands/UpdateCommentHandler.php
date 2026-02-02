<?php
namespace comments\application\commands;

use comments\domains\contracts\ICommentRepository;

class UpdateCommentHandler
{
    private $repository;
    private $updateComment;

    public function __construct(ICommentRepository $repository, UpdateComment $updateComment)
    {
        $this->repository = $repository;
        $this->updateComment = $updateComment;
    }

    public function handle($id, array $data)
    {
        $existing = $this->repository->findById($id);
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

        $updated = $this->updateComment::execute((array) $existing, $data);
        $this->repository->update($id, $updated);
        $fresh = $this->repository->findById($id);

        return ['success' => true, 'comment' => $fresh ?? $updated];
    }
}
