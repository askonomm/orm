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

  public function testGet(): void
  {
    $user = new User;
    $user->id = 1;

    $this->assertEquals(1, $user->get('id'));
  }

  public function testSet(): void
  {
    $user = new User;
    $user->set('id', 1);

    $this->assertEquals(1, $user->id);
  }

  public function testFirst(): void
  {
    $query = (new User)->query()->select('*')->first();

    $this->assertInstanceOf(User::class, $query);
  }

  public function testLast(): void
  {
    $query = (new User)->query()->select('*')->last();

    $this->assertInstanceOf(User::class, $query);
  }

  public function testFind(): void
  {
    $user = (new User)->find(1);

    $this->assertInstanceOf(User::class, $user);
    $this->assertEquals(1, $user->id);
    $this->assertEquals("John Doe", $user->name);
    $this->assertEquals("john@doe.com", $user->email);
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
