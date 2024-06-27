<?php

namespace Asko\Orm;

use Asko\Collection\Collection;
use PDOException;

/** 
 * @template T
 * @implements QueryBuilder<T>
 */
class MysqlQueryBuilder implements QueryBuilder
{
  private string $sql = "";

  /** @var array<mixed> */
  private array $data = [];

  public function __construct(
    private readonly string $class,
    private readonly string $table,
    private readonly ConnectionDriver $connection
  ) {
  }

  private function maybePrependSelect(): void
  {
    if (str_starts_with($this->sql, "SELECT")) {
      return;
    }

    $this->sql = "SELECT * FROM {$this->table} {$this->sql}";
  }

  /**
   * 
   * @param string $col 
   * @param string $op 
   * @param mixed $value 
   * @return static 
   */
  public function where(string $col, string $op = "=", mixed $value = null): static
  {
    $this->sql .= " WHERE ";
    $this->sql .= "{$col} {$op} ?";
    $this->data[] = $value;

    return $this;
  }

  /**
   * @param string $col 
   * @param string $op 
   * @param mixed $value 
   * @return static 
   */
  public function andWhere(string $col, string $op = "=", mixed $value = null): static
  {
    $this->sql .= " AND ";
    $this->sql .= "{$col} {$op} ?";
    $this->data[] = $value;

    return $this;
  }

  /**
   * @param string $col 
   * @param string $op 
   * @param mixed $value 
   * @return static 
   */
  public function orWhere(string $col, string $op = "=", mixed $value = null): static
  {
    $this->sql .= " OR ";
    $this->sql .= "{$col} {$op} ?";
    $this->data[] = $value;

    return $this;
  }

  /**
   * @param string $col 
   * @param string $order 
   * @return static 
   */
  public function orderBy(string $col, string $order = "asc"): static
  {
    $this->sql .= " ORDER BY {$col} {$order}";

    return $this;
  }

  /**
   * @param int $limit 
   * @return static 
   */
  public function limit(int $limit): static
  {
    $this->sql .= " LIMIT {$limit}";

    return $this;
  }

  /**
   * @param int $offset
   * @return static
   */
  public function offset(int $offset): static
  {
    $this->sql .= " OFFSET {$offset}";

    return $this;
  }

  /**
   * Example usage:
   *
   * ```
   * $query->join('users, 'users.id', '=', 'posts.user_id');
   * ```
   *
   * @param string $table
   * @param string $first
   * @param string $operator
   * @param string $second
   * @return static
   */
  public function join(string $table, string $first, string $operator, string $second): static
  {
    $this->sql .= " JOIN {$table} ON {$first} {$operator} {$second}";

    return $this;
  }

  /**
   * Example usage:
   *
   * ```
   * $query->leftJoin('users', 'users.id', '=', 'posts.user_id');
   * ```
   *
   * @param string $table
   * @param string $first
   * @param string $operator
   * @param string $second
   * @return static
   */
  public function leftJoin(string $table, string $first, string $operator, string $second): static
  {
    $this->sql .= " LEFT JOIN {$table} ON {$first} {$operator} {$second}";

    return $this;
  }

  /**
   * Example usage:
   *
   * ```
   * $query->rightJoin('users', 'users.id', '=', 'posts.user_id');
   * ```
   *
   * @param string $table
   * @param string $first
   * @param string $operator
   * @param string $second
   * @return static
   */
  public function rightJoin(string $table, string $first, string $operator, string $second): static
  {
    $this->maybePrependSelect();

    $this->sql .= " RIGHT JOIN {$table} ON {$first} {$operator} {$second}";

    return $this;
  }

  /**
   * Example usage:
   *
   * ```
   * $query->innerJoin('users', 'users.id', '=', 'posts.user_id');
   * ```
   *
   * @param string $table
   * @param string $first
   * @param string $operator
   * @param string $second
   * @return static
   */
  public function innerJoin(string $table, string $first, string $operator, string $second): static
  {
    $this->maybePrependSelect();

    $this->sql .= " INNER JOIN {$table} ON {$first} {$operator} {$second}";

    return $this;
  }

  /**
   * Example usage:
   *
   * ```
   * $query->outerJoin('users', 'users.id', '=', 'posts.user_id');
   * ```
   *
   * @param string $table
   * @param string $first
   * @param string $operator
   * @param string $second
   * @return static
   */
  public function outerJoin(string $table, string $first, string $operator, string $second): static
  {
    $this->sql .= " OUTER JOIN {$table} ON {$first} {$operator} {$second}";

    return $this;
  }

  /**
   * Insert data into the table
   * 
   * @param array<string, mixed> $data 
   * @return void 
   * @throws PDOException 
   */
  public function insert(array $data): void
  {
    $cols = implode(",", array_keys($data));
    $values = implode(", ", array_map(fn ($v) => "?", array_values($data)));
    $this->sql = " INSERT INTO {$this->table} ({$cols}) VALUES ({$values})";
    $this->data = array_values($data);
    $this->execute();
  }

  /**
   * Update data in the table
   * 
   * @param string $identifier 
   * @param array<string, mixed> $data 
   * @return void 
   * @throws PDOException 
   */
  public function update(string $identifier, array $data): void
  {
    $sets = implode(",", array_map(fn ($k) => "{$k} = ?", array_keys($data)));
    $this->sql = " UPDATE {$this->table} SET {$sets} WHERE {$identifier} = ?";
    $this->data = [...array_values($data), $data[$identifier]];
    $this->execute();
  }

  /**
   * Delete data from the table
   * 
   * @return void 
   * @throws PDOException 
   */
  public function delete(): void
  {
    $this->sql = "DELETE FROM {$this->table} {$this->sql}";
    $this->execute();
  }

  /**
   * Execute a raw SQL query
   * 
   * @param string $sql 
   * @return static 
   */
  public function raw(string $sql, array $data = []): static
  {
    $this->sql .= " {$sql}";
    $this->data = [...$this->data(), ...$data];

    return $this;
  }

  /**
   * Execute the query
   * 
   * @return void 
   * @throws PDOException 
   */
  public function execute(): void
  {
    $this->connection->execute($this->sql(), $this->data());
  }

  /**
   * @return Collection<T>
   */
  public function get(): Collection
  {
    $this->maybePrependSelect();

    $result = $this->connection->fetch($this->sql(), $this->data());

    return new Collection(array_map(function ($row) {
      /** @var T */
      $instance = new $this->class();

      foreach ($row as $key => $value) {
        $instance->{$key} = $value;
      }

      return $instance;
    }, $result));
  }

  /**
   * @return T|null
   */
  public function first(): mixed
  {
    return $this->get()->first();
  }

  /**
   * @return T|null
   */
  public function last(): mixed
  {
    return $this->get()->last();
  }

  /**
   * Return the SQL string.
   * 
   * @return string 
   */
  public function sql(): string
  {
    return trim(preg_replace("/\s+/", " ", $this->sql));
  }

  /**
   * Return the data array.
   * 
   * @return array<mixed>
   */
  public function data(): array
  {
    return $this->data;
  }
}
