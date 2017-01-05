<?php

namespace Base\Utils;

class Output
{

    public static function json($datos, $return = false)
    {
        $result = json_encode($datos);

        if ($return) {
            return $result;
        }

        echo $result;
    }

    public static function jsonData($data = array())
    {
        $result = json_encode(array('data' => $data));
        echo $result;
    }
}
    