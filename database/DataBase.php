<?php

namespace Model\DataBase;

use Carbon\Carbon;
use PDO;

require __DIR__ . '/../vendor/autoload.php';


class DataBase
{
    private $connection;

    public function __construct()
    {
        $this->connection = new PDO('mysql:host=localhost;dbname=analyzer;', 'root', 'topi1409');
    }

    public function writeUrlToBase($name)
    {
        $created_at = Carbon::now();
        $sql = 'INSERT INTO Urls (name, created_at) VALUES (:name, :created_at)';
        $query = $this->connection->prepare($sql);
        $query->execute(['name' => $name, 'created_at' => $created_at]);
    }

    public function getUrlDataFromBase($name)
    {
        $sql = 'SELECT * FROM Urls WHERE name = :name';
        $query = $this->connection->prepare($sql);
        $query->execute(['name' => $name]);
        $urlData = $query->fetch(PDO::FETCH_ASSOC);
        return $urlData;
    }

    public function getUrlDataFromBaseId($id)
    {
        $sql = 'SELECT * FROM Urls WHERE id = :id';
        $query = $this->connection->prepare($sql);
        $query->execute(['id' => $id]);
        $urlData = $query->fetch(PDO::FETCH_ASSOC);
        return $urlData;
    }
}