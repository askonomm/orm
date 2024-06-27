# ORM

[![codecov](https://codecov.io/gh/askonomm/orm/graph/badge.svg?token=IZTEUIVDFG)](https://codecov.io/gh/askonomm/orm)

A object relational mapper with a query builder and out of box support for MySQL databases.

## Installation

```
composer require asko/orm
```

## Set-up

To start with create your base model class that your actual data models will be extending, like so:

```php
use Asko\Orm\BaseModel;
use Asko\Orm\Drivers\MysqlDriver;

/**
 * @template T
 * @extends BaseModel<T>
 */
class Model extends BaseModel
{
  public function __construct()
  {
    parent::__construct(new MysqlDriver(
      host: "",
      name: "",
      user: "",
      password: "",
      port: 3006
    ));
  }
}
```

And then you can create all your data models like this:

```php
use Asko\Orm\Column;

/**
 * @extends Model<User>
 */
class User extends Model
{
  protected static string $_table = "users";
  protected static string $_identifier = "id";

  #[Column]
  public int $id;

  #[Column]
  public string $name;

  #[Column]
  public string $email;
}
```

And woalaa, you have an ORM mapping data classes to tables in the database all with full type support (works especially well with PHPStan).

Note that the `$_identifier` should match the name of the primary key column, which in the above case is `id`, and the `$_table` should match the name of the database table, naturally. The other properties here represent the columns of the table, these will be populated by the ORM automatically when querying data and have to have the `Column` attribute.

## Querying

You can query for data using the numerous query builder methods built into ORM.

An example query looks like this:

```php
$user = (new User)
  ->query()
  ->where('id', '=', 1)
  ->first();
```

Although because we use the primary key identifier to search for the user, the above could be simplified as:

```php
$user = (new User)->find(1);
```

### All query methods

#### `where`

Where clause to filter the results.

Usage:

```php
(new User)->query()->where('id', '=', 1);
// or
(new User)->query()->where('id', '>', 1);
```

#### `andWhere`

Same as `where` but with `AND` operator.

Usage:

```php
(new User)->query()->where('id', '=', 1)->andWhere('email', '=', 'john@smith.com');
```

#### `orWhere`

Same as `where` but with `OR` operator.

Usage:

```php
(new User)->query()->where('id', '=', 1)->orWhere('email', '=', 'john@smith.com');
```

#### `orderBy`

Order the results by a column.

Usage:

```php
(new User)->query()->orderBy('id', 'asc');
```

#### `limit`

Limit the number of results.

Usage:

```php
(new User)->query()->limit(10);
```

#### `offset`

Offset the results.

Usage:

```php
(new User)->query()->offset(10);
```

#### `join`

Join another table.

Usage:

```php
(new User)->query()->join('posts', 'posts.user_id', '=', 'users.id');
```

#### `leftJoin`

Join another table with a left join.

Usage:

```php
(new User)->query()->leftJoin('posts', 'posts.user_id', '=', 'users.id');
```

#### `rightJoin`

Join another table with a right join.

Usage:

```php
(new User)->query()->rightJoin('posts', 'posts.user_id', '=', 'users.id');
```

#### `innerJoin`

Join another table with an inner join.

Usage:

```php
(new User)->query()->innerJoin('posts', 'posts.user_id', '=', 'users.id');
```

#### `outerJoin`

Join another table with an outer join.

Usage:

```php
(new User)->query()->outerJoin('posts', 'posts.user_id', '=', 'users.id');
```

#### `raw`

Add a raw SQL to the query.

Usage:

```php
(new User)->query()->raw('WHERE id = ?', [1]);
```

#### `get`

Get all the results.

Usage:

```php
(new User)->query()->get();
```

#### `first`

Get the first result.

Usage:

```php
(new User)->query()->first();
```

#### `last`

Get the last result.

Usage:

```php
(new User)->query()->last();
```

## Creating

To create a new record in the database, you can do the following:

```php
$user = new User;
$user->name = "John Smith";
$user->email = "john@smith.com"
$user->store();
```

## Updating

To update a record in the database, you can do the following:

```php
$user = (new User)->find(1);
$user->name = "John Doe";
$user->store();
```

## Deleting

To delete a record in the database, you can do the following:

```php
$user = (new User)->find(1);
$user->delete();
```

## Creating a connection driver

ORM comes with the MySQL driver already built in, but if you wish to extend the ORM to support other databases, you can do so by creating a new driver class that implements the `ConnectionDriver` interface, and if you need to also build a new query builder due to syntax differences with the MySQL query builder, you can do so by creating a new query builder class that implements the `QueryBuilder` interface.
