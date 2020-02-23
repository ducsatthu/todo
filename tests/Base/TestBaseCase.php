<?php

namespace Tests\Base;

use PHPUnit\Framework\TestCase;

/**
 * Define
 */
require_once __DIR__ . '/../../configs/server.php';

abstract class TestBaseCase extends TestCase
{
    public $serverDomain;

    public function __construct(?string $name = null, array $data = [], string $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->serverDomain = BASE_URL;
    }
}