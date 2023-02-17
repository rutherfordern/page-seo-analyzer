<?php

namespace PageAnalyzer\Database;

use Carbon\Carbon;
use Dotenv\Dotenv;

class DataBase
{
    private static $connect;

    private function __construct()
    {
    }

    public static function getInstance(): static
    {
        return static::$connect ?? static::$connect = new static();
    }

    private function connectDataBase()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->load();

        $conStr = sprintf("pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s",
            $_ENV['POSTGRESQL_HOST'],
            $_ENV['POSTGRESQL_PORT'],
            $_ENV['POSTGRESQL_DATABASE'],
            $_ENV['POSTGRESQL_USER'],
            $_ENV['POSTGRESQL_PASSWORD']);

        $pdo = new \PDO($conStr);
        $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }

    private function makeQueryDataBase(string $sqlQuery, array $data, bool $fetch = false, bool $fetchAll = false)
    {
        $dataPreparation = $this->connectDataBase()->prepare($sqlQuery);
        $dataPreparation->execute($data);

        if ($fetch === true) {
            return $dataPreparation->fetch(\PDO::FETCH_ASSOC);
        }

        if ($fetchAll === true) {
            return $dataPreparation->fetchAll(\PDO::FETCH_ASSOC);
        }
    }

    public function saveDataBaseTableUrl(string $name): string
    {
        if ($this->isInUrlDataBase($name)) {
            return "Страница уже существует";
        }

        $currentDate = Carbon::now('Europe/Moscow')->toDateTimeString();

        $query = 'INSERT INTO urls (name, created_at) VALUES (:name, :created_at)';

        $this->makeQueryDataBase($query, ['name' => $name, 'created_at' => $currentDate]);

        return "Страница успешно добавлена";
    }

    public function saveDataBaseTableUrlChecks(int $id, array $elements): string
    {
        $currentDate = Carbon::now('Europe/Moscow')->toDateTimeString();

        $query = 'INSERT INTO url_checks (url_id, status_code, h1, title, description, created_at) 
                  VALUES (:url_id, :status_code, :h1, :title, :description, :created_at)';

        $queryValues = [
            'url_id' => $id,
            'status_code' => $elements['status'],
            'h1' => $elements['h1'],
            'title' => $elements['title'],
            'description' => $elements['description'],
            'created_at' => $currentDate
        ];

        $this->makeQueryDataBase($query, $queryValues);

        return "Страница успешно проверена";
    }

    public function getDateUrlByNameFromDB(string $name)
    {
        $query = 'SELECT * FROM urls WHERE name = :name';
        return $this->makeQueryDataBase($query, ['name' => $name], true);
    }

    public function getDateUrlByIdFromDB(int $id)
    {
        $query = 'SELECT * FROM urls WHERE id = :id';
        return $this->makeQueryDataBase($query, ['id' => $id], true);
    }

    public function getAllUrl(): array
    {
        $queryJoinUrlsAndUrlChecks = 'SELECT urls.id, urls.name, url_checks.status_code, url_checks.created_at FROM urls LEFT JOIN url_checks 
                                      ON urls.id = url_checks.url_id;';

        $urlsJoin = $this->connectDataBase()->query($queryJoinUrlsAndUrlChecks)->fetchAll(\PDO::FETCH_ASSOC);

        $collection = collect($urlsJoin);

        $unique = $collection->unique('id');

        return $unique->all();
    }

    public function getCheckUrlShow(int $id): bool|array
    {
        $query = 'SELECT * FROM url_checks WHERE url_id = :id ORDER BY created_at DESC';
        return $this->makeQueryDataBase($query, ['id' => $id], false, true);
    }

    private function isInUrlDataBase(string $name): bool
    {
        $urlName = $this->getDateUrlByNameFromDB($name);

        return !empty($urlName);
    }
}
