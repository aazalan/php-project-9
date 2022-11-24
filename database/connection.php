<?php

namespace Connection;

function makeConnection () {
    try {
        $connection = new PDO('pgsql:host=127.0.0.1;port=5432;dbname=analyzer;', 'postgres', 'postgres');
    } catch (PDOException $e) {
        print "Error";
    }
    return $connection;
}