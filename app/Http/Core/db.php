<?php

namespace App\Http\Core;

use PDO;

class db
{
    public static function getConnection(): PDO
    {
        return new PDO("mysql:host=localhost;dbname=amoCRM", "root", "");
    }
}