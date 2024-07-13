<?php

namespace Asko\Orm;

use ReflectionProperty;

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
     * @param mixed|null $default
     * @return mixed
     * @throws \ReflectionException
     */
    public function get(string $key = null, mixed $default = null): mixed
    {
        if (is_null($key) && is_null($default)) {
            $vars = get_object_vars($this);
            $column_vars = [];

            // Keep only vars that have the Column attribute
            foreach ($vars as $k => $v) {
                $reflection = new ReflectionProperty($this, $k);
                $attributes = $reflection->getAttributes();

                foreach ($attributes as $attribute) {
                    if ($attribute->getName() === Column::class) {
                        $column_vars[$k] = $v;
                    }
                }
            }

            return $column_vars;
        }

        return $this->{$key} ?? $default;
    }

    /**
     * Set a property
     *
     * @param string $key
     * @param mixed $value
     * @return static
     */
    public function set(string $key, mixed $value): static
    {
        $this->{$key} = $value;

        return $this;
    }

    /**
     * @return QueryBuilder<T>
     */
    public function query(): QueryBuilder
    {
        /** @var class-string<T> $class */
        $class = static::class;

        return $this->_connection->queryBuilder($class, static::$_table);
    }

    /**
     * @param mixed $identifier
     * @return static|null
     */
    public function find(mixed $identifier): ?static
    {
        return self::query()
            ->where(static::$_identifier, '=', $identifier)
            ->first();
    }

    /**
     * @return void
     * @throws \ReflectionException
     */
    public function delete(): void
    {
        self::query()
            ->where(static::$_identifier, '=', $this->get(static::$_identifier))
            ->delete();
    }

    /**
     * @return void
     * @throws \ReflectionException
     */
    public function store(): void
    {
        $query = $this->_connection->queryBuilder(static::class, static::$_table);
        $id = $this->get(static::$_identifier);

        // If item by identifier exists, update
        if ($query->where(static::$_identifier, '=', $id)->first()) {
            $query->update(static::$_identifier, $this->get());
            return;
        }

        // Otherwise create it
        $query->insert($this->get());
    }
}
