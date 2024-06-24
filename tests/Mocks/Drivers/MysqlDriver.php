<?php

namespace Asko\Orm\Tests\Mocks\Drivers;

use Asko\Orm\ConnectionDriver;
use Asko\Orm\MysqlQueryBuilder;
use Asko\Orm\QueryBuilder;

class MysqlDriver implements ConnectionDriver
{
  public function __construct()
  {
    // Do nothing, this is a mock.
  }

  public function execute(string $sql, array $params = []): bool
  {
    return true;
  }

  public function fetch(string $sql, array $params = [], int $mode = \PDO::FETCH_ASSOC): array
  {
    return [
      [
        "id" => 1,
        "name" => "John Doe",
        "email" => "john@doe.com"
      ]
    ];
  }

  /**
   * @template T
   * @param class-string<T> $class 
   * @param string $table 
   * @return QueryBuilder<T>
   */
  public function queryBuilder(string $class, string $table): QueryBuilder
  {
    return new MysqlQueryBuilder(
      class: $class,
      table: $table,
      connection: $this
    );
  }
}
