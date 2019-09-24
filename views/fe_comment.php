<?php
// fe_comment.php -  Mon Sep 23 13:27:39 CDT 2019
// 

require_once "../essentials.php";
require_once "form_element.php";

class fe_comment extends form_element {
    function __construct($is_new, $user_priveleges, $in_parameters = []) {
        $this->user_priveleges = $user_priveleges;
        parent::__construct($is_new, $user_priveleges, $in_parameters);
    }

 /*************************************************************************/  
   // 
    function makeForm() {
        ?>
    <div id="form_wrap">
    <h2>Comments</h2>
    <?php
    echo $this->user_id;
    foreach($this->form_data as $discussion) {
        $this->showDiscussion($discussion);
    }
    ?>
    <button id="new_comment">Add Comment</button>
    <br>
    </div>
    <?php
    }



 /*************************************************************************/  
   // 
    function showDiscussion($discussion) {
//         showArray($discussion);
    ?>
    <div class="comment">
    <h3><?php echo americanDate($discussion['post_date']) ?></h3>
    <p>Posted by: <?php echo $discussion['post_date'] ?></p>
    <input  name="posted_by" type="hidden" value="<?php echo $discussion['posted_by'] ?>">
    <input  name="id" type="hidden" value="<?php echo $discussion['id']?>">
    <input  name="is_new" type="hidden" value="0">
     
     <textarea name="the_text" class="full_sized"><?php echo $discussion['the_text'] ?></textarea>
    </div>
    <?php
    }

 /*************************************************************************/  
   // The very basic get all data from one row. Joins, etc. will be handled 
   // in the extended classes.
   function fill_form_data() {
        if(!(array_key_exists('table', $this->in_parameters) || array_key_exists('member_id', $this->in_parameters))){
            return;
        }
        
        $db_obj = new db_class();
        $primary_key = $db_obj->getPrimaryKey($this->in_parameters['table']);
                
        $sql = 'SELECT * FROM ' . $this->in_parameters['table'] . ' WHERE member_id=? ORDER BY post_date';
        $keyTypeHash = $db_obj->columnTypeHash($this->in_parameters['table']);
        $keyType = $keyTypeHash[$primary_key];
        $db_table = $db_obj->simpleOneParamRequest($sql, $keyType, $this->in_parameters['member_id']);
        $db_obj->closeDB();
//         showArray($db_table);
        $this->form_data = $db_table;
   }

}