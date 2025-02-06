<?php

namespace Modules\Core\Contracts;

interface Role extends \Spatie\Permission\Contracts\Role
{
  /**
   * Find or Create a permission by its name and guard name.
   *
   * @param string $name
   * @param string $label
   * @param string|null $guardName
   *
   * @return Role
   */
  public static function customFindOrCreate(string $name, string $label, ?string $guardName): self;
}
