<?php
// essentials.php -  Tue Aug 27 15:38:40 CDT 2019
// 

$root = $_SERVER['DOCUMENT_ROOT'] ;
set_include_path($root . '/schedule/:'  . 
                 $root . '/schedule/base_classes:' .
                 $root . '/schedule/models:' .
                 $root . '/schedule/views');

// access the database.
require_once "db_class.php";

// a few debugging things
//////////////
function showArray($inArray){
    if(is_array($inArray)){
        echo "<pre style=\"text-align:left\">\n";
        print_r($inArray);
        echo "</pre><br/>\n";
    } else {
        echo "not an array<br/>\n";
    }
}

//////////////
function showDebug( $string ) {
    echo 'DEBUG: ' . $string . '<br/>' . "\n";
}

//////////////
function dump($value) {
    echo '<pre>';
    var_dump($value);
    echo '</pre>';
}

///////////////////////////////////////////////////
// We will figure this one out. There are a number of
// ways to do this.
function check_login() {
    return array('user_id' => 'stopher', 'authority' => 10);
}


///////////////////////////////////////////////////
//  Make sure the data value is the correct type for
//  the database field.
// Right now, I'm just checking for dates, but this
// should be expanded to make sure numbers are cast 
// correctly. 
function ensureType($val, $val_type) {
    if($val_type == 'date') {
        $val = ensureDate($val);
    }
    return $val;
}


////////////////////////////////////
//  Creates a date format that won't screw up MySQL.
//  This defaults to 1969-12-31, so that would be an error.
function ensureDatetime($date) {
    date_default_timezone_set("America/Chicago");
    
    $time = strtotime($date);
    if( $time != '' ) {
        $format = "Y-m-d H:i:s";
        return date($format, $time);
    } else {
        return NULL;
    }
}


////////////////////////////////////
//  Creates a date format that won't screw up MySQL.
//  This defaults to 1969-12-31, so that would be an error.
function ensureDate($date) {
    date_default_timezone_set("America/Chicago");
    $time = strtotime($date);
    
    // having issues with some formats using m/d/y
    $standardized_date = date("Y-m-d", $time);

    // Make sure that the date wasn't set to the future - e.g. 1/4/69 is not 
    // set to 01/04/2069 instead of 01/04/1969
    $this_year = date("Y"); 
    $date_array = explode('-', $standardized_date);
    if($date_array[0] > $this_year){
        $date_array[0] = $date_array[0] - 100;
        $new_date = implode("-", $date_array); 
        return $new_date;
    }
    
    // just double checking 
    if( $time != '' ) {
        return $standardized_date;
    } else {
        return NULL;
    }
}

function today() {
    date_default_timezone_set("America/Chicago");
    return date("Y-m-d H:i:s");  
//     return date("m/d/Y");  
}

/////////////////////////////

function americanDate($date) {
    date_default_timezone_set("America/Chicago");
    $time = strtotime($date);
    if( $time != '' ) {
        $format = "m/d/Y";
        return date($format, $time);
    } else {
        return '';
    }
}

