<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserRepositoryMySQL extends UserRepository
{
    protected function getModelClass(): string
    {
        return User::class;
    }

    public function find($id)
    {
        return User::query()
            ->select('*')
            ->addSelect($this->getDecryptDBRaw())
            ->find($id);
    }

    /**
     * @param string $email
     *
     * @return User|null
     */
    public function findByEmail(string $email)
    {
        /** @var User $user */
        $user = User::query()
            ->select('*')
            ->addSelect($this->getDecryptDBRaw())
            ->whereRaw('email = ' . $this->encrypt($email))
            ->first();

        return $user;
    }

    /**
     * @param array $options
     * @return Collection
     */
    public function get(array $options = []): Collection
    {
        /** @var Builder $query */
        $query = ($this->getModelClass())::query();
        $query->select('*')->addSelect($this->getDecryptDBRaw());
        $this->applyOptions($query, $options);

        return $query->get();
    }

    private function getDecryptDBRaw()
    {
        $key = getAppKey();
        return DB::raw("AES_DECRYPT(name, '$key') as name, AES_DECRYPT(password, '$key') as password, AES_DECRYPT(email, '$key') as email");
    }

    private function encrypt($value)
    {
        $key = getAppKey();
        return DB::raw("AES_ENCRYPT('$value', '$key')");
    }
}
