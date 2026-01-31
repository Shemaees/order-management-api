<?php

namespace App\Modules\Auth\Repositories;

use App\Base\BaseRepository;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class UserRepository extends BaseRepository
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function findByEmail(string $email): User|Model|null
    {
        return $this->model
            ->where('email', $email)
            ->first();
    }
}
