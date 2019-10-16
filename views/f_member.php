<?php
// f_member.php -  Thu Aug 29 15:14:06 CDT 2019
// This is the main view. It contains at least one form_element.
//

require_once "../essentials.php";
require_once "view_class.php";
require_once "fe_member.php";
require_once "fe_comment.php";



////////////////////////////////////////
// EXTEND form_element
class discussion extends form_element {
	function makeForm() {
		parent::makeForm();
	}
}



////////////////////////////////////////
//  EXTEND view_class 
class f_member extends view_class {
    protected $is_new = 0;
    protected $id = '';
    protected $member_form;
//     protected $temp;
    

    function __construct() {
        parent::__construct($this->title);
//         $this->handle_get($_GET);
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
    
//  /*************************************************************************/  
//     function page_header() {
//         parent::page_header();
//         include "menu.php";
//     }


 /*************************************************************************/  
   // Show the table. 
   function page_content() {
   
       
   ?>
<input type="hidden" id="table_name" value="member" >
<input type="hidden" id="user_id" name="user_id" value="<?php echo $this->user_id; ?>" >
<div id="wrapper"> 
    
  <?php 
  $this->member_form->execute() ;
  $this->comment_form->execute();
  ?>
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
            $this->member_form  = new fe_member(0, $this->user_privileges,  array('table' => 'member', 'id' => $this->id));
            $this->comment_form = new fe_comment(0, $this->user_privileges,  array('table' => 'comment', 'member_id' => $this->id));
            
//             $this->title = "Member " . $this->member_form->get_value_by_column('first_name') . ' ' . $this->member_form->get_value_by_column('last_name');
            $this->title =  $this->member_form->get_value_by_column('first_name') . ' ' . $this->member_form->get_value_by_column('last_name');
        }
    }

 
 /*************************************************************************/  
   // A new, empty form
   function handle_no_input() {
        $this->member_form = new fe_member(1, 0,  array('table' => 'member', 'id' => $this->id));
        $this->title = "New Person";
   }

}

$f_member = new f_member();
$f_member->execute();
    
