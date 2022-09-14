<?php


namespace App\Classes\Helpers;


class ParseParamsFromUrlHelper
{
    public static function parseQueryParams(string $params) : array
    {
        parse_str($params, $response);

        return $response;
    }
}
