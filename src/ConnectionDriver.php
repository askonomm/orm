<?php

namespace Asko\Orm;

interface ConnectionDriver
{
  /**
   * 
   * @param string $sql 
   * @param array<mixed> $params 
   */
  public function execute(string $sql, array $params = []): bool;

  /**
   * @param string $sql
   * @param array<mixed> $params
   * @return array<array<string, mixed>>
   */
  public function fetch(string $sql, array $params = []): array;

  /**
   * @template T
   * @param class-string<T> $class
   * @param string $table
   * @return QueryBuilder<T>
   */
  public function queryBuilder(string $class, string $table): QueryBuilder;
}
