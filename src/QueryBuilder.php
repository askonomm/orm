<?php

namespace Asko\Orm;

use Asko\Collection\Collection;

/**
 * @template T
 */
interface QueryBuilder
{
  public function where(string $col, string $op = "=", mixed $value = null): static;

  public function andWhere(string $col, string $op = "=", mixed $value = null): static;

  public function orWhere(string $col, string $op = "=", mixed $value = null): static;

  public function orderBy(string $col, string $order = "asc"): static;

  public function limit(int $limit): static;

  public function offset(int $offset): static;

  public function join(string $table, string $col1, string $op, string $col2): static;

  public function leftJoin(string $table, string $col1, string $op, string $col2): static;

  public function rightJoin(string $table, string $col1, string $op, string $col2): static;

  public function innerJoin(string $table, string $col1, string $op, string $col2): static;

  public function outerJoin(string $table, string $col1, string $op, string $col2): static;

  /**
   * @param array<string, mixed> $data 
   */
  public function insert(array $data): void;

  /**
   * @param string $identifier 
   * @param array<string, mixed> $data 
   */
  public function update(string $identifier, array $data): void;

  public function delete(): void;

  /**
   * @param string $sql
   * @param array<mixed> $params
   */
  public function raw(string $sql, array $params = []): static;

  public function execute(): void;

  /**
   * @return Collection<T>
   */
  public function get(): Collection;

  /**
   * @return T|null 
   */
  public function first(): mixed;

  /**
   * @return T|null 
   */
  public function last(): mixed;

  public function sql(): string;

  /**
   * @return array<mixed>
   */
  public function data(): array;
}
