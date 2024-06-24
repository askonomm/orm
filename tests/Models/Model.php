<?php

declare(strict_types=1);

namespace Asko\Orm\Tests\Models;

use Asko\Orm\BaseModel;
use Asko\Orm\Tests\Mocks\Drivers\MysqlDriver;

/**
 * @template T
 * @extends BaseModel<T>
 */
class Model extends BaseModel
{
  public function __construct()
  {
    parent::__construct(new MysqlDriver());
  }
}
