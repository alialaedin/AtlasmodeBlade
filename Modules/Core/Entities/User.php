<?php

namespace Modules\Core\Entities;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\App;
use Laravel\Passport\ClientRepository;

abstract class User extends Authenticatable
{
  public function createTokenHelper($name)
  {
    App::clearResolvedInstance(ClientRepository::class);
    app()->singleton(ClientRepository::class, function () {
      return new ClientRepository(static::CLIENT_ID, null);
    });

    return $this->createToken($name);
  }
}
