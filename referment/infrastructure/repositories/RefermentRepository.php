<?php
namespace referment\infrastructure\repositories;

use referment\domains\contracts\IRefermentRepository;
use Illuminate\Support\Facades\DB;

class RefermentRepository implements IRefermentRepository
{
    public function create(array $data)
    {
        return DB::table('referments')->insertGetId($data);
    }

    public function update($id, array $data)
    {
        return DB::table('referments')->where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return DB::table('referments')->where('id', $id)->delete();
    }

    public function findById($id)
    {
        return DB::table('referments')->where('id', $id)->first();
    }

    public function getAll()
    {
        return DB::table('referments')->orderBy('created_at', 'desc')->get();
    }

    public function getByUser($userId)
    {
        return DB::table('referments')->where('user_id', $userId)->orderBy('created_at', 'desc')->get();
    }

    public function findByCode($code)
    {
        return DB::table('referments')->where('code', $code)->first();
    }
}
