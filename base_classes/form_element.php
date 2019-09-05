<?php
// form_element.php -  Thu Aug 29 14:48:11 CDT 2019
// We can't assume that all fields will be coming from one record in 
// one table. There will be joins and things.
//
//  $in_parameters should be structured array('table' => table_title, 'id'=> id, [columns => array(...)] )
// 

require_once "../essentials.php";

class form_element {
    protected $is_new = 0;
    protected $in_parameters;
    protected $form_data;
    
    
 /*************************************************************************/   
    function __construct($is_new, $in_parameters = []) {
        $this->is_new = $is_new;
        $this->in_parameters = $in_parameters;
        $this->fill_form_data();
    }

 /*************************************************************************/  
 // Execute the page.  
    function execute() {
        $this->makeForm();
    } /*** end execute **/
    
 /*************************************************************************/  
 // Execute the page.  
    function get_value_by_column($column_name) {
        return $this->form_data[$column_name];
    } 
    
    
 /*************************************************************************/  
   // Do the basic form things.  -- most likely to be overridden 
    function makeForm() {
        
        ?>
    <div id="form_wrap">
    
    <?php 
        foreach($this->form_data as $col => $value){
        	echo "<lable>$col <input name='$col type='text' value='$value'></lable><br>\n";
        }
    ?>
    
    </div>
        <?php
    }

  
 /*************************************************************************/  
   // The very basic get all data from one row. Joins, etc. will be handled 
   // in the extended classes.
   function fill_form_data() {
        if(!(array_key_exists('table', $this->in_parameters) || array_key_exists('id', $this->in_parameters))){
            return;
        }
        
        $db_obj = new db_class();
        $primary_key = $db_obj->getPrimaryKey($this->in_parameters['table']);
                
        $sql = 'SELECT * FROM ' . $this->in_parameters['table'] . ' WHERE ' . $primary_key . '=?';
        
        $keyTypeHash = $db_obj->columnTypeHash($this->in_parameters['table']);
        $keyType = $keyTypeHash[$primary_key];
        $db_table = $db_obj->simpleOneParamRequest($sql, $keyType, $this->in_parameters['id']);
        $db_obj->closeDB();
        
        $this->form_data = $db_table[0];
   }

}