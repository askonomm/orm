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

  public function testAndWhereQuery(): void
  {
    $query = (new User)->query()->select('*')->where("id", "=", 1)->andWhere("name", "=", "John Doe");

    $this->assertEquals("SELECT * FROM users WHERE id = ? AND name = ?", $query->sql());
    $this->assertEquals([1, "John Doe"], $query->data());
  }

  public function testOrWhereQuery(): void
  {
    $query = (new User)->query()->select('*')->where("id", "=", 1)->orWhere("name", "=", "John Doe");

    $this->assertEquals("SELECT * FROM users WHERE id = ? OR name = ?", $query->sql());
    $this->assertEquals([1, "John Doe"], $query->data());
  }

  public function testOrderByQuery(): void
  {
    $query = (new User)->query()->select('*')->orderBy("name", "desc");

    $this->assertEquals("SELECT * FROM users ORDER BY name desc", $query->sql());
    $this->assertEquals([], $query->data());
  }

  public function testLimitQuery(): void
  {
    $query = (new User)->query()->select('*')->limit(10);

    $this->assertEquals("SELECT * FROM users LIMIT 10", $query->sql());
    $this->assertEquals([], $query->data());
  }

  public function testOffsetQuery(): void
  {
    $query = (new User)->query()->select('*')->offset(10);

    $this->assertEquals("SELECT * FROM users OFFSET 10", $query->sql());
    $this->assertEquals([], $query->data());
  }

  public function testRawQuery(): void
  {
    $query = (new User)->query()->raw("SELECT * FROM users WHERE id = ?", [1]);

    $this->assertEquals("SELECT * FROM users WHERE id = ?", $query->sql());
    $this->assertEquals([1], $query->data());
  }

  public function testUpdate(): void
  {
    $user = new User;
    $user->id = 1;
    $user->name = "Test User";
    $user->email = "test@test.com";
    $user->store();

    $this->assertFileExists(__DIR__ . '/.executed_log');
    [$sql, $params] = unserialize(file_get_contents(__DIR__ . '/.executed_log'));
    $this->assertEquals("UPDATE users SET id = ?,name = ?,email = ? WHERE id = ?", $sql);
    $this->assertEquals([1, "Test User", "test@test.com", 1], $params);

    unlink(__DIR__ . '/.executed_log');
  }

  public function testInsert(): void
  {
    (new User)->query()->insert([
      "id" => 1,
      "name" => "Test User",
      "email" => "test@user.com"
    ]);

    $this->assertFileExists(__DIR__ . '/.executed_log');
    [$sql, $params] = unserialize(file_get_contents(__DIR__ . '/.executed_log'));
    $this->assertEquals("INSERT INTO users (id,name,email) VALUES (?, ?, ?)", $sql);
    $this->assertEquals([1, "Test User", "test@user.com"], $params);

    unlink(__DIR__ . '/.executed_log');
  }

  public function testDelete(): void
  {
    $user = (new User)->find(1);
    $user->delete();

    $this->assertFileExists(__DIR__ . '/.executed_log');
    [$sql, $params] = unserialize(file_get_contents(__DIR__ . '/.executed_log'));
    $this->assertEquals("DELETE FROM users WHERE id = ?", $sql);
    $this->assertEquals([1], $params);

    unlink(__DIR__ . '/.executed_log');
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
