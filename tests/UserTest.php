<?php

declare(strict_types=1);

namespace Asko\Orm\Tests;

use Asko\Orm\Tests\Models\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
  public function setUp(): void
  {
    error_reporting(E_ALL);
  }

  public function testWhereQuery(): void
  {
    $query = (new User)->query()->select('*')->where("id", "=", 1);

    $this->assertEquals("SELECT * FROM users WHERE id = ?", $query->sql());
    $this->assertEquals([1], $query->data());
  }

  public function testModelCreation(): void
  {
    $query = (new User)->query()->select('*')->where("id", "=", 1);
    $user = $query->first();

    $this->assertInstanceOf(User::class, $user);
    $this->assertEquals(1, $user->id);
    $this->assertEquals("John Doe", $user->name);
    $this->assertEquals("john@doe.com", $user->email);
  }
}
