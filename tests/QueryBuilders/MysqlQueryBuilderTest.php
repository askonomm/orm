<?php

use Asko\Orm\Tests\Models\UserWithData as User;
use PHPUnit\Framework\TestCase;

class MysqlQueryBuilderTest extends TestCase
{
  public function testWhereQuery(): void
  {
    $query = (new User)->query()->where("id", "=", 1);
    $query->get();

    $this->assertEquals("SELECT * FROM users WHERE id = ?", $query->sql());
    $this->assertEquals([1], $query->data());
  }

  public function testAndWhereQuery(): void
  {
    $query = (new User)->query()->where("id", "=", 1)->andWhere("name", "=", "John Doe");
    $query->get();

    $this->assertEquals("SELECT * FROM users WHERE id = ? AND name = ?", $query->sql());
    $this->assertEquals([1, "John Doe"], $query->data());
  }

  public function testOrWhereQuery(): void
  {
    $query = (new User)->query()->where("id", "=", 1)->orWhere("name", "=", "John Doe");
    $query->get();

    $this->assertEquals("SELECT * FROM users WHERE id = ? OR name = ?", $query->sql());
    $this->assertEquals([1, "John Doe"], $query->data());
  }

  public function testOrderByQuery(): void
  {
    $query = (new User)->query()->orderBy("name", "desc");
    $query->get();

    $this->assertEquals("SELECT * FROM users ORDER BY name desc", $query->sql());
    $this->assertEquals([], $query->data());
  }

  public function testLimitQuery(): void
  {
    $query = (new User)->query()->limit(10);
    $query->get();

    $this->assertEquals("SELECT * FROM users LIMIT 10", $query->sql());
    $this->assertEquals([], $query->data());
  }

  public function testOffsetQuery(): void
  {
    $query = (new User)->query()->offset(10);
    $query->get();

    $this->assertEquals("SELECT * FROM users OFFSET 10", $query->sql());
    $this->assertEquals([], $query->data());
  }

  public function testRawQuery(): void
  {
    $query = (new User)->query()->raw("WHERE id = ?", [1]);
    $query->get();

    $this->assertEquals("SELECT * FROM users WHERE id = ?", $query->sql());
    $this->assertEquals([1], $query->data());
  }

  public function testUpdate(): void
  {
    (new User)->query()->update("id", [
      "id" => 1,
      "name" => "Test User",
      "email" => "test@test.com"
    ]);

    $this->assertFileExists(__DIR__ . '/../.executed_log');
    [$sql, $params] = unserialize(file_get_contents(__DIR__ . '/../.executed_log'));
    $this->assertEquals("UPDATE users SET id = ?,name = ?,email = ? WHERE id = ?", $sql);
    $this->assertEquals([1, "Test User", "test@test.com", 1], $params);

    unlink(__DIR__ . '/../.executed_log');
  }

  public function testInsert(): void
  {
    (new User)->query()->insert([
      "id" => 1,
      "name" => "Test User",
      "email" => "test@user.com"
    ]);

    $this->assertFileExists(__DIR__ . '/../.executed_log');
    [$sql, $params] = unserialize(file_get_contents(__DIR__ . '/../.executed_log'));
    $this->assertEquals("INSERT INTO users (id,name,email) VALUES (?, ?, ?)", $sql);
    $this->assertEquals([1, "Test User", "test@user.com"], $params);

    unlink(__DIR__ . '/../.executed_log');
  }

  public function testDelete(): void
  {
    $user = (new User)->find(1);
    $user->delete();

    $this->assertFileExists(__DIR__ . '/../.executed_log');
    [$sql, $params] = unserialize(file_get_contents(__DIR__ . '/../.executed_log'));
    $this->assertEquals("DELETE FROM users WHERE id = ?", $sql);
    $this->assertEquals([1], $params);

    unlink(__DIR__ . '/../.executed_log');
  }

  public function testJoin(): void
  {
    $query = (new User)->query()->join('posts', 'posts.user_id', '=', 'users.id');
    $query->get();

    $this->assertEquals("SELECT * FROM users JOIN posts ON posts.user_id = users.id", $query->sql());
    $this->assertEquals([], $query->data());
  }

  public function testLeftJoin(): void
  {
    $query = (new User)->query()->leftJoin('posts', 'posts.user_id', '=', 'users.id');
    $query->get();

    $this->assertEquals("SELECT * FROM users LEFT JOIN posts ON posts.user_id = users.id", $query->sql());
    $this->assertEquals([], $query->data());
  }

  public function testRightJoin(): void
  {
    $query = (new User)->query()->rightJoin('posts', 'posts.user_id', '=', 'users.id');
    $query->get();

    $this->assertEquals("SELECT * FROM users RIGHT JOIN posts ON posts.user_id = users.id", $query->sql());
    $this->assertEquals([], $query->data());
  }

  public function testInnerJoin(): void
  {
    $query = (new User)->query()->innerJoin('posts', 'posts.user_id', '=', 'users.id');
    $query->get();

    $this->assertEquals("SELECT * FROM users INNER JOIN posts ON posts.user_id = users.id", $query->sql());
    $this->assertEquals([], $query->data());
  }

  public function testOuterJoin(): void
  {
    $query = (new User)->query()->outerJoin('posts', 'posts.user_id', '=', 'users.id');
    $query->get();

    $this->assertEquals("SELECT * FROM users OUTER JOIN posts ON posts.user_id = users.id", $query->sql());
    $this->assertEquals([], $query->data());
  }
}
