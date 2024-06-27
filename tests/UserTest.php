<?php

declare(strict_types=1);

namespace Asko\Orm\Tests;

use Asko\Orm\Tests\Models\UserWithData;
use Asko\Orm\Tests\Models\UserWithoutData;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
  public function setUp(): void
  {
    error_reporting(E_ALL);
  }

  public function testGet(): void
  {
    $user = new UserWithData;
    $user->id = 1;

    $this->assertEquals(1, $user->get('id'));
  }

  public function testSet(): void
  {
    $user = new UserWithData;
    $user->set('id', 1);

    $this->assertEquals(1, $user->id);
  }

  public function testFirst(): void
  {
    $query = (new UserWithData)->query()->select('*')->first();

    $this->assertInstanceOf(UserWithData::class, $query);
  }

  public function testLast(): void
  {
    $query = (new UserWithData)->query()->select('*')->last();

    $this->assertInstanceOf(UserWithData::class, $query);
  }

  public function testFind(): void
  {
    $user = (new UserWithData)->find(1);

    $this->assertInstanceOf(UserWithData::class, $user);
    $this->assertEquals(1, $user->id);
    $this->assertEquals("John Doe", $user->name);
    $this->assertEquals("john@doe.com", $user->email);
  }

  public function testModelCreation(): void
  {
    $query = (new UserWithData)->query()->select('*')->where("id", "=", 1);
    $user = $query->first();

    $this->assertInstanceOf(UserWithData::class, $user);
    $this->assertEquals(1, $user->id);
    $this->assertEquals("John Doe", $user->name);
    $this->assertEquals("john@doe.com", $user->email);
  }

  public function testStoreNew(): void
  {
    $user = new UserWithoutData;
    $user->id = 1;
    $user->name = "Test User";
    $user->email = "test@test.com";
    $user->store();

    $this->assertFileExists(__DIR__ . '/.executed_log');
    [$sql, $params] = unserialize(file_get_contents(__DIR__ . '/.executed_log'));
    $this->assertEquals("INSERT INTO users (id,name,email) VALUES (?, ?, ?)", $sql);
    $this->assertEquals([1, "Test User", "test@test.com"], $params);
  }

  public function testStoreExisting(): void
  {
    $user = new UserWithData;
    $user->id = 1;
    $user->name = "Test User";
    $user->email = "test@test.com";
    $user->store();

    $this->assertFileExists(__DIR__ . '/.executed_log');
    [$sql, $params] = unserialize(file_get_contents(__DIR__ . '/.executed_log'));
    $this->assertEquals("UPDATE users SET id = ?,name = ?,email = ? WHERE id = ?", $sql);
    $this->assertEquals([1, "Test User", "test@test.com", 1], $params);
  }
}
