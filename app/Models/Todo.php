<?php

namespace Dst\Todo\Models;

use Dst\Todo\Core\Models\Entity;

class Todo extends Entity
{
    protected $tableName = 'todo';

    public $id;

    public $name;

    public $start;

    public $end;

    public $status;
}