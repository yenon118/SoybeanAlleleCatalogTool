<?php

function pdoResultFilter($result){

    $result_arr = array();

    if (isset($result) && !empty($result)) {
        
        if (count($result) > 0) {
            for ($i = 0; $i < count($result); $i++) {
                array_push($result_arr, array());
                for ($j = 0; $j < count($result[$i]); $j++) {
                    $key = array_keys($result[$i])[$j];
                    if (is_string($key)) {
                        $result_arr[$i][$key] = $result[$i][$key];
                    }
                }
            }
        }
    }

    return $result_arr;
}

?>