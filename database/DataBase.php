<?php

namespace Model\DataBase;

use Carbon\Carbon;
use PDO;

require __DIR__ . '/../vendor/autoload.php';


class DataBase
{
    private $connection;
    private $flashMessages;

    public function __construct()
    {
        $this->connection = new PDO('mysql:host=localhost;dbname=analyzer;', 'root', 'topi1409');
        $this->flashMessages = [
            'existed' => 'Страница уже существует',
            'new' => 'Страница успешно добавлена'
        ];
    }

    public function writeUrlToBase($name)
    {
        if ($this->isInBase($name)) {
            return $this->flashMessages['existed'];
        }
        $created_at = Carbon::now();
        $sql = 'INSERT INTO Urls (name, created_at) VALUES (:name, :created_at)';
        $query = $this->connection->prepare($sql);
        $query->execute(['name' => $name, 'created_at' => $created_at]);
        return $this->flashMessages['new'];
    }

    public function isInBase($name)
    {
        return $this->getUrlDataFromBaseByName($name);
    }

    public function getUrlDataFromBaseByName($name)
    {
        $sql = 'SELECT * FROM Urls WHERE name = :name';
        $query = $this->connection->prepare($sql);
        $query->execute(['name' => $name]);
        $urlData = $query->fetch(PDO::FETCH_ASSOC);
        return $urlData;
    }

    public function getUrlDataFromBaseById($id)
    {
        $sql = 'SELECT * FROM Urls WHERE id = :id';
        $query = $this->connection->prepare($sql);
        $query->execute(['id' => $id]);
        $urlData = $query->fetch(PDO::FETCH_ASSOC);
        return $urlData;
    }

    public function addCheck($id, $query)
    {
        $created_at = Carbon::now();
        $sql = 'INSERT INTO Url_checks (url_id, created_at) VALUES (:url_id, :created_at)';
        $query = $this->connection->prepare($sql);
        $query->execute(['url_id' => $id, 'created_at' => $created_at]);
    }

    public function getChecks($id)
    {
        $sql = 'SELECT * FROM Url_checks WHERE url_id = :url_id';
        $query = $this->connection->prepare($sql);
        $query->execute(['url_id' => $id]);
        $checkData = $query->fetchAll();
        usort($checkData, fn($check1, $check2) => $check2['id'] <=> $check1['id']);
        return $checkData;
    }
}