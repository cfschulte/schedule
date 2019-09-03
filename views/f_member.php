<?php
// f_member.php -  Thu Aug 29 15:14:06 CDT 2019
// 

require_once "../essentials.php";
require_once "view_class.php";
require_once "fe_member.php";




////////////////////////////////////////
//  EXTEND view_class 
class f_member extends view_class {
    protected $is_new = 0;
    protected $id = '';
    protected $member_form;
//     protected $temp;
    

    function __construct() {
        $this->handle_get($_GET);
        parent::__construct($this->title);
    }


 /*************************************************************************/  
 // The load the javascripts -- it can be called from the child classes 
 // and then augmented.
    function jscript_list() {
        parent::jscript_list();
?>
    <script type="text/javascript" src="/schedule/js/form.js"></script>
    
<?php 
    }


 /*************************************************************************/  
   // Show the table. 
   function page_content() {
   
       
   ?>
<input type="hidden" id="table_name" value="member" >
<div id="wrapper">  
  <?php $this->member_form->execute() ?>
</div>
   <?php
   
   }

 /*************************************************************************/  
   // 
    function handle_get($indata) {
        if(!array_key_exists('id', $indata)) {
            $this->is_new = 1;
        } else {
            $this->id = $indata['id'];
            $this->member_form = new fe_member(0, array('table' => 'member', 'id' => $this->id));
            
            $this->title = "Member " . $this->member_form->get_value_by_column('first_name') . ' ' . $this->member_form->get_value_by_column('last_name');
        }
    }
}

$f_member = new f_member();
$f_member->execute();
    
