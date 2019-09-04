<?php
// update_db_record.php -  Wed Sep 4 09:09:18 CDT 2019
//

 require_once "/schedule/base_classes/db_class.php"; 

//////////////////////////
// The epynomous function to rule them all.
function update_db_record($indata){
    if($indata['is_new']) {
        return new_record($indata);
    }
    
    
}


//////////////////////////
function new_record($indata){
    $indata['is_new'] = 0;
    return $indata;
}

//////////////////////////
