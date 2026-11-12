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

        $dbHost = Config::get('TEST_DB_HOST','127.0.0.1');
        $port = Config::get('TEST_DB_PORT','3307');
        $dbName = Config::get('TEST_DB_DATABASE','assessment_test');
        $userName = Config::get('TEST_DB_USERNAME','assessment');
        $password = Config::get('TEST_DB_PASSWORD','assessment');

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