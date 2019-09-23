<?php
// fe_member.php -  Tue Sep 3 12:51:11 CDT 2019
//  This inherits from form_element. The member form itself will contain one 
//  or more form_elements. 

require_once "../essentials.php";
require_once "form_element.php";

class fe_member extends form_element {
    function __construct($is_new, $user_priveleges, $in_parameters = []) {
        $this->user_priveleges = $user_priveleges;
        parent::__construct($is_new, $user_priveleges, $in_parameters);
    }
    
 /*************************************************************************/  
   // Do the basic form things.  -- most likely to be overridden 
    function makeForm() {
        
        ?>
    <div id="form_wrap">
    <h2>General Info</h2> 
    <?php 
//         showArray($this->form_data);
        
    ?>
    <form method="POST" class="ajax_form">
    <button  id="undo_button" disabled>Undo</button>
    <div class="clear"></div>
    <input type="hidden" id="is_new" name="is_new" value="<?php echo $this->is_new ?>">
    <input type="hidden" id="id" name="id" value="<?php echo $this->form_data['id'] ?>">
    <input type="hidden" id="table" name="table" value="member">
<div class="flex_container">   
   
<div class="flex_item">
    <label for="first_name" class="basic_label">First name</label>
    <input class="" type="text" name="first_name" id="first_name" value="<?php echo $this->form_data['first_name'] ?>">
</div>
    
<div class="flex_item">
    <label for="other_names" class="basic_label">Middle</label>
    <input class="" type="text" name="other_names" id="other_names" value="<?php echo $this->form_data['other_names'] ?>">
</div>
    
<div class="flex_item">
    <label for="last_name" class="basic_label">Last name</label>
    <input class="" type="text" name="last_name" id="last_name" value="<?php echo $this->form_data['last_name'] ?>">
</div>
</div> 
<div class="flex_container">  
    
<div class="flex_item">
    <label for="user_id" class="basic_label">Member ID</label>
    <input class="" type="text" name="member_id" id="member_id" value="<?php echo $this->form_data['member_id'] ?>">
</div>

<div class="flex_item">
    <label for="authority" class="basic_label">Role</label>
<?php
    if($this->user_priveleges > 5){
        $this->buildGenericSelect("authority", "authority", $this->form_data['authority']);
    } else {
        $role = $this->getRole($this->form_data['authority']);
        echo "<b>$role</b>";
    }
?>
</div>
</div>    
<div class="flex_container">  
<div class="flex_item">
    <label for="phone" class="basic_label">Phone</label>
    <input class="" type="text" name="phone" id="phone" value="<?php echo $this->form_data['phone'] ?>">
</div>
    
<div class="flex_item">
    <label for="email" class="basic_label">Email</label>
    <input class="" type="text" size="35" name="email" id="email" value="<?php echo $this->form_data['email'] ?>">
</div>
</div>  
<div class="address">  
    <label for="address" class="basic_label">Address</label>
    <input class="" type="text" name="address" id="address" value="<?php echo $this->form_data['address'] ?>"><br>
    
    <label for="city" class="basic_label">City</label>
    <input class="" type="text" name="city" id="city" value="<?php echo $this->form_data['city'] ?>"><br>
    
    <label for="state_provence" class="basic_label">State/Prov</label>
    <input class="" type="text" name="state_provence" id="state_provence" value="<?php echo $this->form_data['state_provence'] ?>"><br>
    
    <label for="zipcode" class="basic_label">Zipcode</label>
    <input class="" type="text" name="zipcode" id="zipcode" value="<?php echo $this->form_data['zipcode'] ?>"><br>
    
    <label for="country" class="basic_label">Country</label>
    <input class="" type="text" name="country" id="country" value="<?php echo $this->form_data['country'] ?>"><br>
</div>    
<div class="grouper">  
<!-- 
    <label for="comments" class="basic_label">Comments</label>
    <textarea class="full_sized"></textarea>
 -->
</div>
    
    </form>
    </div>
        <?php
    }
    
    
 /*************************************************************************/  
   // 
    function getRole($authority){
        $sql = 'SELECT description FROM authority WHERE id=?';
        $db_obj = new db_class();
        $db_table = $db_obj->simpleOneParamRequest($sql, 'i', $authority);
        $db_obj->closeDB();
        
        return $db_table[0];
    }
}

