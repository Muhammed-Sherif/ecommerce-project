<?php
namespace copons\api\controllers;

use copons\application\commands\CreateCoponHandler;
use copons\application\commands\UpdateCoponHandler;
use copons\application\commands\DeleteCoponHandler;
use copons\application\queries\GetAllCoponsHandler;
use copons\application\queries\GetCoponHandler;
use copons\application\queries\GetCoponByCodeHandler;

class CoponController
{
    public function index(GetAllCoponsHandler $handler)
    {
        return $handler->handle();
    }

    public function show($id, GetCoponHandler $handler)
    {
        return $handler->handle($id);
    }

    public function byCode($code, GetCoponByCodeHandler $handler)
    {
        return $handler->handle($code);
    }

    public function store(array $data, CreateCoponHandler $handler)
    {
        try {
            return $handler->handle($data);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function update($id, array $data, UpdateCoponHandler $handler)
    {
        try {
            return $handler->handle($id, $data);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function destroy($id, DeleteCoponHandler $handler)
    {
        try {
            return $handler->handle($id);
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
