<?php
namespace comments\infrastructure\controllers;

use comments\application\commands\CreateCommentHandler;
use comments\application\commands\UpdateCommentHandler;
use comments\application\commands\DeleteCommentHandler;
use comments\application\queries\GetAllCommentsHandler;
use comments\application\queries\GetCommentHandler;
use comments\application\queries\GetCommentsByProductHandler;
use comments\application\queries\GetCommentsByUserHandler;

class CommentController
{
    public function index(GetAllCommentsHandler $handler)
    {
        return $handler->handle();
    }

    public function show($id, GetCommentHandler $handler)
    {
        return $handler->handle($id);
    }

    public function byProduct($productId, GetCommentsByProductHandler $handler)
    {
        return $handler->handle($productId);
    }

    public function byUser($userId, GetCommentsByUserHandler $handler)
    {
        return $handler->handle($userId);
    }

    public function store(array $data, CreateCommentHandler $handler)
    {
        try {
            return $handler->handle($data);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function update($id, array $data, UpdateCommentHandler $handler)
    {
        try {
            return $handler->handle($id, $data);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function destroy($id, DeleteCommentHandler $handler)
    {
        try {
            return $handler->handle($id);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
