<?php

namespace Modules\Core\Repositories;

interface RepositoryInterface
{

  public function __construct($model);

  public function get();

  public function paginate();

  public function show($id);

  public function create(array $data);

  public function update(array $data, $id);

  public function delete($id);
}
