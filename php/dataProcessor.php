<?php

function processAccessionCounts($accessionCounts){
    $splittedAccessionCounts = array();

    for ($i=0; $i<count($accessionCounts); $i++){
        if (array_key_exists($accessionCounts[$i]['Gene'], $splittedAccessionCounts)) {
            array_push($splittedAccessionCounts[$accessionCounts[$i]['Gene']], $accessionCounts[$i]);
        } else {
            $splittedAccessionCounts[$accessionCounts[$i]['Gene']] = array();
            array_push($splittedAccessionCounts[$accessionCounts[$i]['Gene']], $accessionCounts[$i]);
        }
    }

    return $splittedAccessionCounts;
}

?>