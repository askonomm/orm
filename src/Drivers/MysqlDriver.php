<?php

namespace Asko\Orm\Drivers;

use Asko\Orm\ConnectionDriver;
use Asko\Orm\MysqlQueryBuilder;
use Asko\Orm\QueryBuilder;

class MysqlDriver implements ConnectionDriver
{
  private \PDO $instance;

  public function __construct(
    string $host,
    string $name,
    string $user,
    string $password,
    string $port
  ) {
    $this->instance = new \PDO(
      "mysql:host={$host};dbname={$name};port={$port}",
      $user,
      $password
    );
  }

  public function execute(string $sql, array $params = []): bool
  {
    return $this->instance->prepare($sql)->execute($params);
  }

  public function fetch(string $sql, array $params = [], int $mode = \PDO::FETCH_ASSOC): array
  {
    $stmt = $this->instance->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchAll($mode);
  }

  public function queryBuilder(string $class, string $table): QueryBuilder
  {
    return new MysqlQueryBuilder(
      class: $class,
      table: $table,
      connection: $this
    );
  }
}
