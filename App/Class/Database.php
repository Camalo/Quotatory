<?php

namespace App\Class;

use PDO;
use PDOException;
use App\Class\Json;


class Database
{
    public $dbh;
    private $errors = array();
    protected static $_instance;

    public function __construct()
    {
        setlocale(LC_TIME, 'ru_RU.UTF-8');
        date_default_timezone_set('Europe/Moscow');
    }
    public static function init()
    {
        if (is_null(self::$_instance))
            self::$_instance = new Database;
        return self::$_instance;
    }

    public function connect()
    {
        $dbData = Json::geJsonData();

        try {
            $this->dbh = new PDO($dbData["driver"] . ":host=" . $dbData["host"] . ";dbname=" . $dbData["dbname"], $dbData["username"], $dbData["password"]);
            $this->dbh->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        } catch (PDOException $e) {
            $this->errors[] = $e->getMessage();
        }
    }
    /**
     * Выполняет запрос в базу данных без параметров
     * 
     * @param string $query
     * @return array
     */
    public function query($query)
    {
        $stmt = $this->dbh->query($query);
        $tableData = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $tableData;
    }
    /**
     * Выполняет запрос в базу данных и возвращает данные из таблицы
     * 
     * @param string $query
     * @param string|array $paramNames
     * @param string|array $paramValues
     * @return array|false
     */
    public function select(string $query, string|array $paramNames, string|array $paramValues)
    {

        $stmt = $this->dbh->prepare($query);

        if (is_array($paramNames) && is_array($paramValues)) {
            foreach ($paramNames as $key => $param) {
                $stmt->bindParam($param, $paramValues[$key]);
            }
        } else {
            $stmt->bindParam($paramNames, $paramValues);
        }

        try {
            $stmt->execute();
            $tableData = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {

            $this->errors[] = $e->getMessage();

            $tableData = array();
        }

        return $tableData;
    }

    /**
     * Выполняет запрос в базу данных
     * 
     * @param string $query
     * @param string|array $paramNames
     * @param string|array $paramValues
     * @return array|false
     */
    public function update(string $query, string|array $paramNames, string|array $paramValues)
    {

        $stmt = $this->dbh->prepare($query);

        if (is_array($paramNames) && is_array($paramValues)) {
            foreach ($paramNames as $key => $param) {
                $stmt->bindParam($param, $paramValues[$key]);
            }
        } else {
            $stmt->bindParam($paramNames, $paramValues);
        }

        try {
            return $stmt->execute();
        } catch (PDOException $e) {
            $this->errors[] = $e->getMessage();
            print_r($e->getMessage());
            return false;
        }
    }

    public function prepareQuery($query)
    {
        $stmt = $this->dbh->prepare($query);
        return $stmt;
    }

    public function getLastInsertId()
    {
        return $this->dbh->lastInsertId();
    }

    public function isError()
    {
        return (count($this->errors) > 0);
    }
}
