<?php
// table_class.php -  Wed Aug 28 11:35:34 CDT 2019
// I've been using a table_class in a lot of my other web 
// applications. I'm taking one from schedule, 
// going through it, cleaning it up, and creating an essential
// table view.
//
//  I'm going to leave out a lot of functionality until it is 
//  actually needed. 
//

require_once "../essentials.php";

// Class Definition
class table_class {
    protected $title;
    protected $table_name;
    protected $title_column ;
    protected $primary_key ; //set in getDBTable
    protected $edit_page;
    protected $filtered_query;
    
  // Use an associative array of columns => column titles for the display.
    protected $columns_to_show = array(); 
    
    protected $memberid = ''; 
    protected $member_privileges = 0; 
    
    
 /*************************************************************************/   
 // 
    function __construct($title="No Table", $table_name='', 
                $edit_page = '', $title_column = '', $columns_to_show){
        $this->title = $title;
        $this->table_name = $table_name;
        $this->title_column = $title_column;
        $this->edit_page = $edit_page;
        $this->columns_to_show = $columns_to_show;
    }
    
 /*************************************************************************/   
 // 
    function execute(){
        $userInfo = check_login();
        $this->user_id = $userInfo['user_id'];
        $this->user_privileges = $userInfo['authority'];
        
        // We will use post or get to filter the lists.
        // I want to revamp this. 
        if(!empty($_GET)) {
            $this->filtered_query = 1;
        } elseif(!empty($_POST)){
            $this->filtered_query = 1;
        } else {
            $this->filtered_query = 0;
        }
?>
<!DOCTYPE html>
<html lang="en"> 
         <?php $this->header(); ?>
         <?php $this->body(); ?>
</html>
<?php    
    } /*** end execute **/
    
 /*************************************************************************/  
 // The HTML header  -- 
   function header() {
?>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<?php
  // load the javascripts -- this is a little confusing and could be done more better.
  $this->jscript_list();
  $this->css_list();
?>
    <Title><?php echo $this->title; ?></Title>
</head>

<?php 
   }
    
 /*************************************************************************/  
 // The load the javascripts -- it can be called from the child classes 
 // and then augmented.
    function jscript_list() {
  // we might want this later:
  //     <script type="text/javascript" src="/schedule/js/jquery.tablesorter.js"></script>


?>
    <script type="text/javascript" src="/jquery/jquery.js"></script>
    <script type="text/javascript" src="/jquery/jquery-ui/jquery-ui.min.js"></script>
    <script type="text/javascript" src="/jquery/jquery.tablesorter.min.js"></script>
    <script type="text/javascript" src="/schedule/js/table.js"></script>
<?php 
    }
    
 /*************************************************************************/  
 // The load the css files -- it can also be called from the child classes 
 // and then augmented.
    function css_list() {
?>
    <link rel="Stylesheet" type="text/css" href="/schedule/css/style.css" />
    <link rel="Stylesheet" type="text/css" href="/jquery/jquery-ui/jquery-ui.min.css" />

<?php 
    }


    
 /*************************************************************************/  
   // The body
   function body() {
?>
<body>
<?php
    $this->page_header();
?> 
<input type="hidden" id="table_name" value="<?php echo $this->table_name ; ?>" >
<div id="wrapper">
  <div id="display">
    <?php $this->create_table(); ?>
  </div>
</div>
</body>
<?php 
   }


 /*************************************************************************/  
   // page_header
   function page_header() {
?>
<div class="page_header">
<div class="in_header">
 <h1 ><?php echo $this->title; ?></h1>
<?php
    $this->additional_page_header_stuff();
?>
 </div>
 <div style="clear:both;"></div>
<?php 
     include "../menu.php";
</div>
   }

 /*************************************************************************/  
   // Additional_header_stuff. This is meant to be overriden on a per table
   // basis.
   function additional_page_header_stuff() {
?>   
<!-- HTML  -->
<?php   
   }


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
   // table_declaration
   function table_declaration() {
       echo  '<table  class="default_table">' . "\n"; // generic default can be overridden 
   }

 /*************************************************************************/  
   // table_head
   function table_head() {
        
        echo "<thead>\n<tr>";
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
   // table_row  -- This is where most of the overriding should happen. 
    function table_row($row) {
        $primary = $row[$this->primary_key];
        echo '<tr>';
        while ( list($key, $datum) = each($row) ) {
           if($key == $this->primary_key){
                continue;
           }elseif($key == $this->title_column) {
                $this->table_cell_primary($key, $datum, $primary);
           } else {
                $this->table_cell($key, $datum);
           }
        }
        echo '</tr>' . "\n";
    }

 /*************************************************************************/  
   // table_cell
    function table_cell($key, $datum) {
        echo "<td>$datum</td>";
    }

 /*************************************************************************/  
   // table_cell_primary  
   
    function table_cell_primary($key, $datum, $primary='') {
       echo '<td class="link_button"><a  href="/schedule/views/' . $this->edit_page .'?id=' . $primary . '&table=' .$this->table_name . '">' . $datum . '</a></td>';
            
    }

 /*************************************************************************/  
   // getDBTable - THIS is where we select the rows according to the filter.
   function getDBTable() {
        // start with a general db_obj 
        $db_obj = new db_class();
        
        $this->primary_key = $db_obj->getPrimaryKey($this->table_name);
        
        if(! $this->filtered_query){
            $sql = $this->get_sql();
            $db_table = $db_obj->getTableNoParams($sql);
        } else {
            $variables = gen_filtered_search();
            $db_table = $db_obj->safeSelect($variables['sql'], $variables['typestr'], $variables['params'] );
        }
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
    
 /*************************************************************************/  
   // 
    function gen_filtered_search() {
        $sql = 'SELECT ' . $this->primary_key;
        $typestr = '';
        $params = array();
        
        return array('sql' => $sql, 'typestr' => $typestr, 'params' => $params);
    }
    



} /* end of class declaration. */


