<?php
// table_element.php -  Thu Aug 29 12:27:38 CDT 2019
// The general default is that this will show the contents
// of a table;

require_once "../essentials.php";

class table_element {
    protected $table_name;
    protected $primary_key; 
    protected $columns_to_show = array(); 
    protected $table_classes = '';
    protected $header_classes= '';
    protected $row_classes = ''; // cells will be handled with overrides

    
 /*************************************************************************/   
 // 
    function __construct($table_name='', $columns_to_show = array()){
        $this->table_name = $table_name;
        $this->columns_to_show = $columns_to_show;
    }
    
    
 /*************************************************************************/   
 // 
    function execute(){
        $this->create_table();
    } /*** end execute **/


 /*************************************************************************/  
   // create_table -- each of the called functions can be overridden.
   function create_table() {
      if($this->table_name == '') { return; }
            
       $this->table_declaration();
       $this->table_head();
       $this->table_body();
       echo  "</table>\n";

   } /* end of create_table */

 /*************************************************************************/   
 // 
    function set_classes($table_classes='', $header_classes = '', $row_classes =''){
        $this->table_classes = $table_classes;
        $this->header_classes = $header_classes;
        $this->row_classes = $row_classes;
    }

 /*************************************************************************/  
   // table_declaration
   function table_declaration() {
       
       if(!empty($this->table_classes != "")){
        echo  '<table  class="' . $this->table_classes . '">' . "\n"; 
       } else {
        echo  '<table>' . "\n"; 
       }
   }

 /*************************************************************************/  
   // table_head
   function table_head() {
        
        if(empty($this->header_classes)){
            echo "<thead>\n";
        } else {
            echo "<thead class='$this->header_classes'>\n";
        }
        echo "<tr>";
        
        foreach( $this->columns_to_show as $col => $title ) {
            if(!empty($title)){
                echo '<th>' . $title . '</th>';
            }
        }
        echo "</tr>\n</thead>\n";
   }

 /*************************************************************************/  
   // table_body
   function table_body() {
        $db_table =$this->getDBTable();
        
        echo "<tbody>\n";
        foreach( $db_table as $row ) {
            $this->table_row($row);
        }
        echo "</tbody>\n";
    }


 /*************************************************************************/  
   // table_row  
    function table_row($row) {

        if(!empty($this->row_classes)){
            echo '<tr class="' . $this->row_classes . '">';
        } else {
            echo '<tr>';
        }
        
        while ( list($key, $datum) = each($row) ) {
           if(!array_key_exists($key, $this->columns_to_show)){
                continue;
           } else {
                $this->table_cell($key, $datum);
           }
        }
        echo '</tr>' . "\n";
    }


 /*************************************************************************/  
   // table_cell  -- the key might be needed in the overrides
    function table_cell($key, $datum) {
        echo "<td>$datum</td>";
    }

 /*************************************************************************/  
   // getDBTable - THIS is where we select the rows according to the filter.
   function getDBTable() {
        // start with a general db_obj 
        $db_obj = new db_class();
        
        $this->primary_key = $db_obj->getPrimaryKey($this->table_name);
        
//         if(! $this->filtered_query){  // TODO: figure out find and specific views.
            $sql = $this->get_sql();
            $db_table = $db_obj->getTableNoParams($sql);
//         } else {
//             $variables = gen_filtered_search();
//             $db_table = $db_obj->safeSelect($variables['sql'], $variables['typestr'], $variables['params'] );
//         }
        $db_obj->closeDB();
        
        return $db_table;
    }
    
 /*************************************************************************/  
   // get_sql  -- Meant to be overridden by child classes
   function get_sql() {
        $sql = 'SELECT ' . $this->primary_key;
                
        if( ! empty($this->columns_to_show) ) {
            foreach($this->columns_to_show as $col => $title) {
                $sql .=  ',' . $col;
            }
        } else {
            $sql .= '* ';
        }
        $sql .= ' FROM ' . $this->table_name ;
        
        // possibly add orderby later, otherwise, we can just do that in the child
        
       return $sql;
    }

}