<?php
namespace referment\api\controllers;

use referment\application\commands\CreateRefermentHandler;
use referment\application\commands\UpdateRefermentHandler;
use referment\application\commands\DeleteRefermentHandler;
use referment\application\queries\GetAllRefermentsHandler;
use referment\application\queries\GetRefermentHandler;
use referment\application\queries\GetRefermentsByUserHandler;

class RefermentController
{
    public function index(GetAllRefermentsHandler $handler)
    {
        return $handler->handle();
    }

    public function show($id, GetRefermentHandler $handler)
    {
        return $handler->handle($id);
    }

    public function byUser($userId, GetRefermentsByUserHandler $handler)
    {
        return $handler->handle($userId);
    }

    public function store(array $data, CreateRefermentHandler $handler)
    {
        try {
            return $handler->handle($data);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function update($id, array $data, UpdateRefermentHandler $handler)
    {
        try {
            return $handler->handle($id, $data);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function destroy($id, DeleteRefermentHandler $handler)
    {
        try {
            return $handler->handle($id);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
