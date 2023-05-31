<?php

namespace App\Http\Models;

use App\Http\Core\AmoCrmApi;
use App\Http\Core\AmoCrmConfig;
use App\Http\Core\db;

class AmoCRM
{
    protected AmoCrmApi $api;
    protected \PDO $db;

    public function __construct()
    {
        $this->db = db::getConnection(); // Подключение к бд
    }

    // Получение токена для взаимодействия с API
    public function getAccessToken()
    {
        $api = new AmoCrmApi();
        return $api->validateAuth();
    }

    // Получение под домена для генерации ссылки в cURL
    public function getSubDomain()
    {
        return AmoCrmConfig::SUBDOMAIN;
    }

    // Создание сделки
    public function store($request)
    {
        // Параметры для запроса
        $data = [
            [
                "price" => (int) $request['price'],
                "pipeline_id" => 6861770,
                "_embedded" => [
                    "metadata" => [
                        "category" => "forms",
                        "form_id" => 1,
                        "form_name" => "Форма на сайте",
                        "form_page" => 'Сделка',
                        "form_sent_at" => strtotime(date("Y-m-d H:i:s")),
                        "ip" => '1.1.1.1',
                        "referer" => 'site.ua'
                    ],
                    "contacts" => [
                        [
                            "first_name" => $request['name'],
                            "custom_fields_values" => [
                                [
                                    "field_code" => "EMAIL",
                                    "values" => [
                                        [
                                            "enum_code" => "WORK",
                                            "value" => $request['email']
                                        ]
                                    ]
                                ],
                                [
                                    "field_code" => "PHONE",
                                    "values" => [
                                        [
                                            "enum_code" => "WORK",
                                            "value" => $request['phone']
                                        ]
                                    ]
                                ],
                            ]
                        ]
                    ],
                ],
            ]
        ];

        $method = "/api/v4/leads/complex";

        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . self::getAccessToken(),
        ];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_USERAGENT, 'amoCRM-API-client/1.0');
        curl_setopt($curl, CURLOPT_URL, "https://".self::getSubDomain().".amocrm.ru".$method);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_COOKIEFILE, 'amo/cookie.txt');
        curl_setopt($curl, CURLOPT_COOKIEJAR, 'amo/cookie.txt');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        $out = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $code = (int) $code;
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
        if ($code < 200 || $code > 204) die( "Error $code. " . ($errors[$code] ?? 'Undefined error') );

        echo 'Сделка была добавлена';
    }

}