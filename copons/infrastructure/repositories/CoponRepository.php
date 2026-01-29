<?php
namespace copons\infrastructure\repositories;

use copons\domains\contracts\ICoponRepository;
use Illuminate\Support\Facades\DB;

class CoponRepository implements ICoponRepository
{
    public function create(array $data)
    {
        return DB::table('copons')->insertGetId($data);
    }

    public function update($id, array $data)
    {
        return DB::table('copons')->where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return DB::table('copons')->where('id', $id)->delete();
    }

    public function findById($id)
    {
        return DB::table('copons')->where('id', $id)->first();
    }

    public function getAll()
    {
        return DB::table('copons')->orderBy('created_at', 'desc')->get();
    }

    public function findByCode($code)
    {
        return DB::table('copons')->where('code', $code)->first();
    }
}
