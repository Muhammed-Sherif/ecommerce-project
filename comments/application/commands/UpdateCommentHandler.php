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

        $updated = $this->updateComment::execute((array) $existing, $data);
        $this->repository->update($id, $updated);
        $fresh = $this->repository->findById($id);

        return ['success' => true, 'comment' => $fresh ?? $updated];
    }
}
