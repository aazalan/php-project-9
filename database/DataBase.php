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

    public function addCheck($id, $queryToUrl = null)
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

    private function getCheckedData()
    {
        $sql = 'SELECT status_code, url_id, MAX(created_at) FROM Url_checks GROUP BY url_id, status_code';
        $query = $this->connection->query($sql);
        $data = $query->fetchAll();
        return $data;
    }

    private function getUrlsData()
    {
        $sql = 'SELECT id, name FROM Urls';
        $query = $this->connection->query($sql);
        $data = $query->fetchAll();
        return $data;
    }

    public function getAllUrls()
    {
        $checkedData = $this->getCheckedData();
        $urlsData = $this->getUrlsData();
        
        $checksById = array_reduce($checkedData, function($arr, $url) {
            $arr[$url['url_id']] = [
                'status_code' => $url['status_code'],
                'created_at' => $url['MAX(created_at)']];
                return $arr;
        }, []);
        
        $fullUrlsData = array_reduce($urlsData, function($arr, $url) use($checksById) {
            $id = $url['id'];
            $arr[$id]['name'] = $url['name'];
            $arr[$id]['id'] = $url['id'];
            if ($checksById[$id]) {
                $arr[$id]['status_code'] = $checksById[$id]['status_code'];
                $arr[$id]['created_at'] = $checksById[$id]['created_at'];
            }
            return $arr;
        }, []);

        return $fullUrlsData;
    }
}

//SELECT status_code, url_id, MAX(created_at) FROM Url_checks GROUP BY url_id, status_code;

// SELECT name, status_code, url_id, MAX(Url_checks.created_at)
//                 FROM Urls JOIN Url_checks ON Urls.id=Url_checks.url_id 
//                 GROUP BY url_id, status_code

