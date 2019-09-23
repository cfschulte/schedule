<?php
// update_db_record.php -  Wed Sep 4 09:09:18 CDT 2019
//

///////////////////////////////////////////////////
// Determine whether or not the update button should 
// be enabled of disabled 
function enable_undo($indata) {
    
    $db_obj = new db_class() ;
    $sql = 'SELECT time_saved FROM backup_table WHERE db_table=? AND id=?';
    $db_table = $db_obj->safeSelect($sql, 'si', array($indata['table'], $indata['id']));
    $db_obj->closeDB();
    
    return sizeof($db_table);
}

//////////////////////////
// The epynomous function to rule them all.
function update_db_record($indata){
    // check whether or not this is a new record.
    if($indata['is_new']) {
        return new_record($indata);
    }
    
    // backup
    $backup_result = backup_field($indata);
    
    // set the new value 
    $update_result = update_field($indata);
    
    return array('update_result' => $update_result, 'backup_result' => $backup_result);
}

//////////////////////////
function update_field($indata){

    $db_obj = new db_class();
    
    $typeHashLong = $db_obj->columnTypeHashLong($indata['table']);
    
    $sql = 'UPDATE ' . $indata['table'] . ' SET ' . $indata['name'] . '=? WHERE id=?';
    $value = $indata['value'];
    if( $typeHashLong[$indata['name']]['type'] =='date' ){
        $value = ensureDate( $indata['value'] );
    }
    
    $paramList = array($value, $indata['id']);
    
    
    $typeList = $typeHashLong[$indata['name']]['typeChar'] ;
    $typeList .= 'i';

    $update_result = $db_obj->safeInsertUpdateDelete($sql, $typeList, $paramList);
    
    $db_obj->closeDB();
    
    
    return $update_result;
}

//////////////////////////
function new_record($indata){
    $db_obj = new db_class();
    
    $data_elements = array($indata['name'] => $indata['value']);
    $result = $db_obj->buildAndExecuteInsert( $indata['table'], $data_elements);
    $id = $db_obj->lastInsertedID();
    $db_obj->closeDB();
    
    return array('result' => $result, 'id' => $id);
}


//////////////////////////
// BACKUP 
function backup_field($indata) {
    $db_obj = new db_class();
    
    $typeHashLong = $db_obj->columnTypeHashLong($indata['table']);
    $backup_value = $indata['previousAjaxDBVal'];
    // make sure the date is in the correct format.
    if( $typeHashLong[$indata['name']]['type'] =='date' ){
        $backup_value = ensureDate( $indata['previousAjaxDBVal'] );
    }
    
    $sql =   'INSERT INTO backup_table ' ;
    $sql .=  '(db_table,id,form_type,table_column,'. which_backup_field($typeHashLong[$indata['name']]['type'])  . ',time_saved) ';
    $sql .=  ' VALUES (?,?,?,?,?,?) ';
    
    $typeList = 'siss' . $typeHashLong[$indata['name']]['typeChar'] . 'i';
    $paramList = array($indata['table'], $indata['id'], $indata['type'], $indata['name'], $backup_value , time());
    
    $db_result = $db_obj->safeInsertUpdateDelete($sql, $typeList, $paramList);
    $db_obj->closeDB();
   
   
    return $db_result;
}

///////////////////////////////////////////////////
// Which database to send save this in?
//    value_varchar    varchar(256) DEFAULT NULL,
//    value_text       TEXT DEFAULT NULL,
//    value_int        int DEFAULT NULL,
//    value_float      float DEFAULT NULL,
//    value_money      decimal(15,2) DEFAULT NULL,
//    value_date      DATE DEFAULT NULL,

function which_backup_field( $column_type ) {
    if( preg_match("/varchar/" ,$column_type) ) {
        return 'value_varchar';
    } elseif(  preg_match("/text/" ,$column_type) ) {
        return 'value_text';
    } elseif(  preg_match("/int/" ,$column_type) ) {
        return 'value_int';
    } elseif(  preg_match("/float/" ,$column_type) || preg_match("/real/" ,$column_type) || preg_match("/double/" ,$column_type)) {
        return 'value_float';
    } elseif(  preg_match("/decimal/" ,$column_type) ) {
        return 'value_money';
    } elseif(  preg_match("/date/" ,$column_type) ) {
        return 'value_date';
    }
    
    return 'no database';
}

///////////////////////////////////////////////////
// UNDO
function undo_last_change($indata) {
    // Set the table and id for clarity 
    $table = $indata['table'];
    $id    = $indata['id'];
    
    $db_obj = new db_class() ;
    // Get the last change to this table with this id
    $sql = 'SELECT MAX(backup_id) FROM backup_table WHERE db_table=? AND id=? ';
    $db_result = $db_obj->safeSelect($sql, 'si', array($table, $id));
    $backup_id = $db_result[0]['MAX(backup_id)'];
    
    // Get the information from that table
    $sql = 'SELECT * FROM backup_table WHERE backup_id=?';
    $db_result = $db_obj->simpleOneParamRequest($sql, 'i', $backup_id);
    
    // This will require a type for the query 
    $typeHashLong = $db_obj->columnTypeHashLong($db_result[0]['db_table']);
    
   // arrange the parameters so that they are easier to handle.
    $column      = $db_result[0]['table_column'];
    $form_type   = $db_result[0]['form_type'];  // RETHINK THE NEED FOR THE FORM ELEMENT TYPE
    $value_type  = $typeHashLong[$column]['type'];
    $typeChar    = $typeHashLong[$column]['typeChar'];
    $value_field = which_backup_field($value_type);
    
    $value       = $db_result[0][$value_field];

   // set the field back to what it was
    $sql = 'UPDATE ' . $indata['table'] . ' SET ' . $column . '=? WHERE id=?';
    $typeList = $typeChar . 'i';
    $paramList = array($value, $indata['id']);
    $revert_result = $db_obj->safeInsertUpdateDelete($sql, $typeList, $paramList);

  // pop the undo off of the backup_table "stack" 
    $sql = 'DELETE FROM backup_table WHERE backup_id=?';
    $typeList = 'i';
    $paramList = array($backup_id);
    $pop_result = $db_obj->safeInsertUpdateDelete($sql, $typeList, $paramList);
    
   // should the undo button be enabled?
    $sql = 'SELECT time_saved FROM backup_table WHERE db_table=? AND id=?';
    $undo_size = $db_obj->safeSelect($sql, 'si', array($indata['table'], $indata['id']));
    
    $db_obj->closeDB();
    

    // format the date to what the users want.
    if($value_field == 'value_date') {
        $value = americanDate($value);
    }
    
    return array('column' => $column, 'value'  => $value,  'form_type'  => $form_type, 'undo_size' => sizeof($undo_size));
}

