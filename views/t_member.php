<?php
// t_member.php -  Thu Aug 29 13:01:26 CDT 2019
// 

require_once "../essentials.php";
require_once "view_class.php";
require_once "table_element.php";


//////////////////////////////////////
//  EXTEND table_element 
class member_table extends table_element {
    
 /*************************************************************************/  
   // table_row  
    function table_row($row) {
    
        if(!empty($this->row_classes)){
            echo '<tr class="' . $this->row_classes . '">';
        } else {
            echo '<tr>';
        }
        
        $id = $row[$this->primary_key];

        while ( list($key, $datum) = each($row) ) {
           if(!array_key_exists($key, $this->columns_to_show)){
                continue;
           } elseif($key == 'member_id'){
                $this->linked_cell($datum, $id);
           }else {
                $this->table_cell($key, $datum);
           }
        }
        echo '</tr>' . "\n";
        
    }
    
 /*************************************************************************/  
   // table_row  
    function linked_cell($datum, $id) {
       
       if(!empty($datum)){
            echo '<td class="link_button"><a  href="/schedule/views/f_member?id=' . $id . '">' . $datum . '</a></td>';
       } else {
            echo '<td class="link_button"><a  href="/schedule/views/f_member?id=' . $id . '">' . $id . '</a></td>';
       }
    }
}



//////////////////////////////////////
//  EXTEND view_class 
class t_member extends view_class {
    function __construct() {
        parent::__construct("People");
    }
    

 /*************************************************************************/  
 // The load the javascripts -- it can be called from the child classes 
 // and then augmented.
    function jscript_list() {
        parent::jscript_list();
?>
    <script type="text/javascript" src="/jquery/jquery.tablesorter.min.js"></script>
    <script type="text/javascript" src="/schedule/js/table.js"></script>
    
<?php 
    }


 /*************************************************************************/  
   // additionalHeaderStuff .
   function additionalHeaderStuff() {
   ?>
   <div class="from_the_right">
   <a class="a_button" href="/schedule/views/f_member.php">New Person</a>
   </div>
   <?php
   }

 /*************************************************************************/  
   // Show the table. 
   function page_content() {
   
       $member_table = new member_table('member', array('member_id' => 'ID', 'first_name' =>'First', 'last_name' => 'Last', 'email' => 'Email'));
       $member_table->set_classes("default_table");
   ?>
<input type="hidden" id="table_name" value="member" >
<div id="wrapper">
  <div class="display">
  <?php $member_table->execute()?>
  </div>
</div>
   <?php
   
   }
}

$t_member = new t_member();
$t_member->execute();