<?php

namespace Modules\Core\Contracts;

interface Permission extends \Spatie\Permission\Contracts\Permission
{
  /**
   * Find or Create a permission by its name and guard name.
   *
   * @param string $name
   * @param string $label
   * @param string|null $guardName
   *
   * @return Permission
   */
  public static function customFindOrCreate(string $name, string $label, ?string $guardName): self;
}
