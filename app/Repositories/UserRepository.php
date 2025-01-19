<?php

namespace App\Repositories;

abstract class UserRepository extends BaseRepository
{
    /**
     * @param string $tmpToken
     * @return UserLmsData
     */
    public function getUserByToken($tmpToken)
    {
        $user = $this->getModelClass()::with("userData")
            ->whereHas('userData', function ($query) use ($tmpToken) {
                $query->where("temp_token", "=", $tmpToken);
            })
            ->first();

        if ($user) {
            UserData::removeTmpToken($tmpToken);
            return $user;
        }

        return null;
    }

    abstract public function findByEmail(string $email);
}
