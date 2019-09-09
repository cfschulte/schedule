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
    $indata['is_new'] = 0;
    return $indata;
}


//////////////////////////
// BACKUP 
function backup_field($indata) {
    $db_obj = new db_class();
    
    $typeHashLong = $db_obj->columnTypeHashLong($indata['table']);
    $backup_value = $indata['previousGenericVal'];
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

