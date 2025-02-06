<?php

namespace Modules\Core\Repositories;

class Repository implements RepositoryInterface
{

  public function __construct(protected $model) {}

  public function get(): mixed
  {
    return $this->model->get();
  }

  public function paginate(): mixed
  {
    return $this->model->latest()->filters()->paginateOrAll();
  }

  public function show($id): mixed
  {
    return $this->model->findOrFail($id);
  }

  public function create(array $data): mixed
  {
    return $this->model->create($data);
  }

  public function update(array $data, $id): mixed
  {
    $record = $this->model->find($id);

    return $record->update($data);
  }

  public function delete($id): mixed
  {
    $record = $this->model->find($id);

    return $record->delete();
  }

  public function with(array $relations): mixed
  {
    return $this->model->with($relations);
  }

  public function setModel(mixed $model)
  {
    $this->model = $model;
  }

  public function getModel(): mixed
  {
    return $this->model;
  }
}
