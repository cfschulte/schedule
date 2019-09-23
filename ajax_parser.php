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

$result = "No action taken for $id.";

// if($id=="update_db_record"){
//     require_once "update_db_record.php";
//     $result = update_db_record($data);
// } elseif($id="form_needs_undo"){
//     require_once "update_db_record.php";
//     $result = "Howdy";
//     $result = enable_undo($data);
// }

switch($id){
    case 'update_db_record':
        require_once "update_db_record.php";
        $result = update_db_record($data);
        break;
    case 'form_needs_undo':
        require_once "update_db_record.php";
        $result = enable_undo($data);
        break;
    case 'undo_last_change':
        require_once "update_db_record.php";
        $result = undo_last_change($data);
        break;
    case 'new_comment':
        require_once "update_db_record.php";
        $result = new_comment($data);
        break;
}


// replies to the javascript caller.
echo json_encode($result);