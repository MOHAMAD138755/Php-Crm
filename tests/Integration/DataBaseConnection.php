<?php

namespace Assessment\Tests\Integration;

use Assessment\Core\Config;
use Dotenv\Dotenv;
use PDO;
use PHPUnit\Framework\TestCase;

abstract class DataBaseConnection extends TestCase
{
    protected $pdo;

    protected function setUp(): void
    {
        parent::setUp();

        Dotenv::createUnsafeImmutable(dirname(__DIR__, 2))->load();

        $dbHost = Config::get('TEST_DB_HOST');
        $port = Config::get('TEST_DB_PORT');
        $dbName = Config::get('TEST_DB_DATABASE');
        $userName = Config::get('TEST_DB_USERNAME');
        $password = Config::get('TEST_DB_PASSWORD');

        $dataBaseSource = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $dbHost, $port, $dbName);

        $this->pdo = new PDO($dataBaseSource, $userName, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,]);
    }

    protected function tearDown(): void
    {
        $this->pdo = null;
        parent::tearDown();
    }
}