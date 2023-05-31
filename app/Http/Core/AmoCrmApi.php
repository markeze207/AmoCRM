<?php

namespace App\Http\Core;

class AmoCrmApi
{
    protected \PDO $db;

    public function __construct()
    {
        $this->db = db::getConnection(); // Подключение к бд
    }

    // Авторизация в API и создание токена в бд
    public function authAmoCRM()
    {
        $link = "https://".AmoCrmConfig::SUBDOMAIN.".amocrm.ru/oauth2/access_token";

        $data = [
            'client_id'     => AmoCrmConfig::CLIENT_ID,
            'client_secret' => AmoCrmConfig::CLIENT_SECRET,
            'grant_type'    => 'authorization_code',
            'code'          => AmoCrmConfig::CODE,
            'redirect_uri'  => AmoCrmConfig::REDIRECT_URI,
        ];

        $curl = curl_init();
        curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
        curl_setopt($curl,CURLOPT_URL, $link);
        curl_setopt($curl,CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
        curl_setopt($curl,CURLOPT_HEADER, false);
        curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
        $out = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $code = (int)$code;
        $errors = [
            301 => 'Moved permanently.',
            400 => 'Wrong structure of the array of transmitted data, or invalid identifiers of custom fields.',
            401 => 'Not Authorized. There is no account information on the server. You need to make a request to another server on the transmitted IP.',
            403 => 'The account is blocked, for repeatedly exceeding the number of requests per second.',
            404 => 'Not found.',
            500 => 'Internal server error.',
            502 => 'Bad gateway.',
            503 => 'Service unavailable.'
        ];

        if ($code < 200 || $code > 204) die( "Errors $code. " . ($errors[$code] ?? 'Undefined error') );

        $response = json_decode($out, true);

        // Создание токена в бд
        $result = $this->db->prepare("INSERT INTO `access_token` SET `access_token` = ?,`refresh_token` = ?, `endTokenTime` = ?");
        $result->execute([$response['access_token'],$response['refresh_token'], $response['expires_in'] + time()]);
        return $response['access_token'];
    }

    // Метод для обновления токена
    public function refreshToken()
    {
            // Получение информации о токене
            $sql = $this->db->prepare("SELECT * FROM `access_token`");
            $sql->execute();
            $resultToken = $sql->fetch();

            $link = "https://".AmoCrmConfig::SUBDOMAIN.".amocrm.ru/oauth2/access_token";

            $data = [
                'client_id'     => AmoCrmConfig::CLIENT_ID,
                'client_secret' => AmoCrmConfig::CLIENT_SECRET,
                'code'          => AmoCrmConfig::CODE,
                'redirect_uri'  => AmoCrmConfig::REDIRECT_URI,
                'grant_type' => 'refresh_token',
            ];

            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-oAuth-client/1.0');
            curl_setopt($curl, CURLOPT_URL, $link);
            curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type:application/json']);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 1);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
            $out = curl_exec($curl);
            $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            $code = (int)$code;
            // Список ошибок
            $errors = [
                301 => 'Moved permanently.',
                400 => 'Wrong structure of the array of transmitted data, or invalid identifiers of custom fields.',
                401 => 'Not Authorized. There is no account information on the server. You need to make a request to another server on the transmitted IP.',
                403 => 'The account is blocked, for repeatedly exceeding the number of requests per second.',
                404 => 'Not found.',
                500 => 'Internal server error.',
                502 => 'Bad gateway.',
                503 => 'Service unavailable.'
            ];

            // Вывод ошибки
            if ($code < 200 || $code > 204) die("Errorss $code. " . ($errors[$code] ?? 'Undefined error'));

            $response = json_decode($out, true);

            // Обновление токена в бд
            $result = $this->db->prepare("UPDATE `access_token` SET `access_token` = ?,`refresh_token` = ?, `endTokenTime` = ? WHERE `access_token` = ?");
            $result->execute([$response['access_token'],$response['refresh_token'], $response['expires_in'] + time(), $resultToken["access_token"]]);
            return $response['access_token']; // Возврат токена
    }

    // Валидация авторизации
    public function validateAuth()
    {
        $sql = $this->db->prepare("SELECT * FROM `access_token`");
        $sql->execute();
        $resultToken = $sql->fetch();
        if($resultToken) // Создан ли токен
        {
            if ((int)$resultToken["endTokenTime"] - 60 < time()) { // Не истекло ли время токена
                return self::refreshToken(); // Обновление и возвращение токена
            } else {
                return $resultToken['access_token']; // Возвращение токена
            }
        } else {
            return self::authAmoCRM(); // Авторизация и создание токена
        }
    }

}