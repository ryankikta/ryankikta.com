<?php

class ApiCall_C
{

    public static $con;

    public static function GetResponse($url, $header, $request_type, $data)
    {
        self::$con = curl_init();

        curl_setopt(self::$con, CURLOPT_URL, $url);
        curl_setopt(self::$con, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt(self::$con, CURLOPT_SSL_VERIFYHOST, 0);

        curl_setopt(self::$con, CURLOPT_HTTPHEADER, $header);
        if ($request_type == 'POST') {
            curl_setopt(self::$con, CURLOPT_POST, 1);
        } else {
            curl_setopt(self::$con, CURLOPT_CUSTOMREQUEST, $request_type);
        }

        curl_setopt(self::$con, CURLOPT_POSTFIELDS, $data);

        curl_setopt(self::$con, CURLOPT_RETURNTRANSFER, 1);

        return curl_exec(self::$con);
    }

}
