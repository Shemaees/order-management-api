<?php

namespace App\Base;

use App\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

abstract class BaseRepository implements BaseRepositoryInterface
{
    public function __construct(protected Model $model) {}

    public function all(): Collection
    {
        return ($this->model)::all();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }

    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(array $data, int $id): bool
    {
        $record = $this->find($id);

        if (! $record) {
            return false;
        }

        return $record->update($data);
    }

    public function delete(int $id): bool
    {
        $record = $this->find($id);

        if (! $record) {
            return false;
        }

        return $record->delete();
    }
}
