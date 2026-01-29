<?php

namespace cms\api\controllers;

use Illuminate\Http\Request;
use cms\infrastructure\repositories\SettingRepository;

class SettingController
{
    protected $repository;

    public function __construct(SettingRepository $repository)
    {
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $group = $request->query('group');
        if ($group) {
            return response()->json($this->repository->getByGroup($group));
        }
        return response()->json($this->repository->getAll());
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'settings' => 'required|array',
        ]);

        $this->repository->updateBulk($data['settings']);

        return response()->json(['success' => true, 'message' => 'Settings updated successfully']);
    }
}
