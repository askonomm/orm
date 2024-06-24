<?php

declare(strict_types=1);

namespace Asko\Orm\Tests\Models;

/**
 * @extends Model<User>
 */
class User extends Model
{
  protected static string $_table = "users";
  protected static string $_identifier = "id";

  public int $id;
  public string $name;
  public string $email;
}
