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

    private function doQuery($sql, $data, $isNeedToFetch = true)
    {
        $query = $this->connection->prepare($sql);
        $query->execute($data);
        if ($isNeedToFetch) {
            return $query->fetchAll();
        }
    }

    private function isInBase($name)
    {
        return $this->getUrlDataFromBaseByName($name);
    }



    public function writeUrlToBase($name)
    {
        if ($this->isInBase($name)) {
            return $this->flashMessages['existed'];
        }
        $created_at = Carbon::now();
        $sql = 'INSERT INTO Urls (name, created_at) VALUES (:name, :created_at)';
        $this->doQuery($sql, ['name' => $name, 'created_at' => $created_at], false);
        return $this->flashMessages['new'];
    }

    public function getUrlDataFromBaseByName($name)
    {
        $sql = 'SELECT * FROM Urls WHERE name = :name';
        $urlData = $this->doQuery($sql, ['name' => $name]);
        return $urlData[0];
    }

    public function getUrlDataFromBaseById($id)
    {
        $sql = 'SELECT * FROM Urls WHERE id = :id';
        $urlData = $this->doQuery($sql, ['id' => $id]);
        return $urlData[0];
    }

    public function addCheck($id, $queryToUrl = null)
    {
        $created_at = Carbon::now();
        $sql = 'INSERT INTO Url_checks 
                (url_id, created_at) VALUES (:url_id, :created_at)';
        $this->doQuery($sql, ['url_id' => $id, 'created_at' => $created_at], false);
    }

    public function getChecks($id)
    {
        $sql = 'SELECT * FROM Url_checks WHERE url_id = :url_id';
        $checkData = $this->doQuery($sql, ['url_id' => $id]);
        usort($checkData, fn($check1, $check2) => $check2['id'] <=> $check1['id']);
        return $checkData;
    }

    private function getCheckedData()
    {
        $sql = 'SELECT status_code, url_id, MAX(created_at) 
                FROM Url_checks GROUP BY url_id, status_code';
        $data = $this->doQuery($sql, []);
        return $data;
    }

    private function getUrlsData()
    {
        $sql = 'SELECT id, name FROM Urls';
        $data = $this->doQuery($sql, []);
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

