<?php
namespace comments\application\commands;

use comments\domains\contracts\ICommentRepository;

class CreateCommentHandler
{
    private $repository;
    private $createComment;

    public function __construct(ICommentRepository $repository, CreateComment $createComment)
    {
        $this->repository = $repository;
        $this->createComment = $createComment;
    }

    public function handle(array $data)
    {
        $commentData = $this->createComment::execute($data);
        $id = $this->repository->create($commentData);
        $created = $this->repository->findById($id);

        return [
            'success' => true,
            'comment' => $created ?? $commentData,
        ];
    }
}
