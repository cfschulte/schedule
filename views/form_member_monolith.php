<?php
// form_member.php -  Wed Aug 28 14:51:38 CDT 2019
// 

require_once "../essentials.php";
require_once "form_class.php";


class form_member extends form_class {
    protected $id;
    protected $authority;
    protected $user_id;
    protected $last_name;
    protected $other_names;
    protected $first_name;
    protected $phone;
    protected $email;
    protected $address;
    protected $city;
    protected $state_provence;
    protected $country;
    protected $zipcode;

/*************************************************************************/   
    function __construct() {
        parent::__construct('member', 'id','table_member.php');
        $this->is_new = '0';
        $this->is_clone = FALSE;
    }
    

 /*************************************************************************/  
   // 
   function makeForm() {
?>
    <div id="form_wrap">
    <?php showArray($this->form_data);  ?>
    </div>

<?php
    }
}

$form_member = new form_member();
$form_member->execute();