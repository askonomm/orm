<?php

namespace Asko\Orm;

/**
 * @template T
 */
class BaseModel
{
  /**
   * Model table name
   * 
   * @var string
   */
  protected static string $_table;

  /**
   * Model identifier column name
   * 
   * @var string
   */
  protected static string $_identifier;

  /**
   * Connection driver
   * 
   * @var ConnectionDriver
   */
  protected ConnectionDriver $_connection;

  /**
   * @param ConnectionDriver $_connection 
   */
  public function __construct(ConnectionDriver $_connection)
  {
    $this->_connection = $_connection;
  }

  /**
   * Get a property
   * 
   * @param string|null $key 
   * @param mixed $default 
   * @return mixed 
   */
  public function get(string $key = null, $default = null): mixed
  {
    if (is_null($key) && is_null($default)) {
      $vars = get_object_vars($this);
      unset($vars['_connection']);
      unset($vars['_table']);
      unset($vars['_identifier']);

      return $vars;
    }

    return $this->{$key} ?? $default;
  }

  /**
   * Set a property
   * 
   * @param string $key 
   * @param mixed $value 
   * @return BaseModel<T>
   */
  public function set(string $key, mixed $value): self
  {
    $this->{$key} = $value;

    return $this;
  }

  /**
   * @return QueryBuilder<T>
   */
  public function query(): QueryBuilder
  {
    /** @var class-string<T> */
    $class = static::class;

    return $this->_connection->queryBuilder($class, static::$_table);
  }

  /**
   * @param mixed $identifier
   * @return ?BaseModel<T>
   */
  public function find(mixed $identifier): ?self
  {
    return self::query()
      ->select('*')
      ->where(static::$_identifier, '=', $identifier)
      ->first();
  }

  /**
   * @return void
   */
  public function delete(): void
  {
    self::query()
      ->where(static::$_identifier, '=', $this->get(static::$_identifier))
      ->delete();
  }

  /**
   * @return void
   */
  public function store(): void
  {
    $query = $this->_connection->queryBuilder(static::class, static::$_table);
    $id = $this->get(static::$_identifier);

    // If item by identifier exists, update
    if ($query->select('*')->where(static::$_identifier, '=', $id)->first()) {
      $query->update(static::$_identifier, $this->get());
      return;
    }

    // Otherwise create it
    $query->insert($this->get());
  }
}
