<?php


namespace App\Service;


class AgeCalculator
{
    function calculate_age(\DateTime $date_of_birth){
        $date1 = strtotime($date_of_birth->format('Y-m-d'));
        $date2 = strtotime(date('Y-m-d'));

// Formulate the Difference between two dates
        $diff = abs($date2 - $date1);


// To get the year divide the resultant date into
// total seconds in a year (365*60*60*24)
        $years = floor($diff / (365.25*60*60*24));
        return $years;
    }
}