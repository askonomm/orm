# ORM

[![codecov](https://codecov.io/gh/askonomm/orm/graph/badge.svg?token=IZTEUIVDFG)](https://codecov.io/gh/askonomm/orm)

A object relational mapper with a query builder and out of box support for MySQL databases.

## Installation

```php
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
/**
 * @extends Model<User>
 */
class User extends Model
{
  protected static string $_table = "users";
  protected static string $_identifier = "id";

  public ?int $id = null;
  public ?string $name = null;
  public ?string $email = null;
}
```

And woalaa, you have an ORM mapping data classes to tables in the database all with full type support.

## Querying

To be written.
