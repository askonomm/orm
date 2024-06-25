<?php

declare(strict_types=1);

namespace Asko\Orm\Tests\Models;

use Asko\Orm\Column;

/**
 * @extends Model<User>
 */
class User extends Model
{
  protected static string $_table = "users";
  protected static string $_identifier = "id";

  #[Column]
  public int $id;

  #[Column]
  public string $name;

  #[Column]
  public string $email;
}
