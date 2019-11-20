<?php


namespace Wolfren\Http;


class Response
{
    private function __construct()
    {

    }

    /**
     * @param $data
     */
    public static function output($data)
    {
        if (! $data) {
            return;
        }
        if (! is_string($data)) {
            $data = static::json($data);
        }

        echo $data;
    }

    /**
     * @param mixed $data
     */
    public static function json($data)
    {
        echo json_encode($data);
    }
}