<?php
// ajax_parser.php -  Wed Sep 4 08:55:08 CDT 2019
// This is called from javascript, mostly, if not always 
// from jQuery scripts. 

require_once "essentials.php";

$id = $_POST['id'];
$data = '';
if(array_key_exists('data', $_POST)){
    $data = $_POST['data'];
}

$result = "No action taken.";

if($id=="key_value_update"){
    require_once "update_db_record.php";
    update_db_record($data);
}


// replies to the javascript caller.
echo json_encode($result);