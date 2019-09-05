<?php
// form_class.php -  Wed Aug 28 15:08:11 CDT 2019
//  This was mostly taken for the treatment project, which was based 
//  on the schedule project. 

require_once "../essentials.php";


class form_class {
    protected $title;
    protected $table_title;
    protected $table_display;
    protected $user_id;
    protected $member_priveleges;
    protected $is_new;
    protected $form_data = array();
    
 /*************************************************************************/   
    function __construct( $table_title='', $primary_key = 'id', $table_display='table_class.php') {
        $this->title = 'Empty';
        $this->is_new = 0;
        
        $this->table_title = $table_title;
        $this->table_display = $table_display;
        $this->primary_key = $primary_key;
        
    }

 /*************************************************************************/  
 // Execute the page.  
    function execute() {
        $userInfo = check_login();
        $this->user_id = $userInfo['user_id'];
        $this->user_privileges = $userInfo['authority'];

        
        if(!empty($_POST) ) {
            $this->handle_post($_POST);
        } elseif(!empty($_GET) ) {
            $this->handle_get($_GET);
        } else {
            $this->create_new();
        }
?>
<!DOCTYPE html>
<html>
         <?php $this->header(); ?>
         <?php 
             if(! $this->problem_with_page ) { 
                $this->body(); 
             } else  {
                $this->warnUser();
             }
         ?>
</html>
<?php 
    } /*** end execute **/

    
 /*************************************************************************/  
 // The header  -- 
 // 
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
?>
    <script type="text/javascript" src="/jquery/jquery.js"></script>
    <script type="text/javascript" src="/schedule/js/form.js"></script>  

<?php 
    }
 
 /*************************************************************************/  
 // The load the javascripts -- it can be called from the child classes 
 // and then augmented.  
 // NOTE: we will probably want a print.css 
    function css_list() {
?>
<link rel="Stylesheet" type="text/css" href="/schedule/css/style.css" />    

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
<div id="form_wrapper">
  <div id="form_display">
    <?php $this->makeForm(); ?>
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
 <h1><?php echo $this->title; ?></h1>

<?php 
    $this->additionalHeaderStuff();
?>
 </div>
 </div>
 <div style="clear:both;"></div>
 <?php 
 // we'll figure this ot 
    include "../menu.php"; 
 ?>
 </div>
<?php 
   }

 /*************************************************************************/  
   // additionalHeaderStuff - form specific buttons 
   function additionalHeaderStuff() {
        return;
   }

 /*************************************************************************/  
   // Do the basic form things.  -- most likely to be overridden 
   function makeForm() {
        if($this->table_title != '' ) {
            $db_obj = new db_class();
            $desc_table = $db_obj->tableDescription($this->table_title);
            $db_obj->closeDB();
        
//             showArray($desc_table);
            echo '<form id="generic_edit_form" method="POST" >';
            echo '<input type="hidden" name="title_input"  value="name"> '; // THIS MUST BE HERE FOR CLONABLE FORMS!!!
            echo '<input type="hidden" name="table"  value="vendors">';     // THE TABLE MUST BE LABELED
            echo '<b>This is a generic input</b> <input type="text" name="generic">' . "\n";
        
            echo '<br><br><input type="submit" >';
            echo '</form>';
        } else {
            echo '<h2>hello, form<h2>';
        }
   }

      
/*************************************************************************/ 
  // NOTE: rethink this. It is for saving a row that has been deleted.
       function deleteInfo() {
?>
    <form method="post"  id="delete_form">
    <!-- for deletes to work, this must be here and the values must be in this order -->
     <input type="hidden" id="primary_key"  name="primary_key" value="<?php echo $this->primary_key ?>">
     <input type="hidden" id="primary_key_value" name="primary_key_value" value="<?php echo $this->primary_key_value ?>">
     <input type="hidden" id="delete_from_table" name="delete_from_table" value="<?php echo $this->table_title ?>">
     <input type="hidden" id="return_address" name="return_address" value="/treatment/tables/<?php echo $this->table_display ?>">
    </form>
<?php
       }
    
 /*************************************************************************/  
   // set initial variables with 
   function handle_get($indata) {
//        showDebug('form_class GET:');
//        showArray($indata);
        if(array_key_exists('new', $indata)) {
            $this->is_new = 1;
            $this->title = 'New data';
        } 
        if(array_key_exists('find', $indata)){
            $this->is_find_form = 1;
            $this->title = 'Find in ' . $this->table_title;
        }
        if(array_key_exists('id', $indata)){
            $this->id = $indata['id'];
            $this->form_data = $this->fetchRecordInfo($this->id);
        }
   }
    
    
 /*************************************************************************/  
   // set initial variables with 
   function handle_post($indata) {
//        showDebug('form_class POST:');
//        showArray($indata); 
       return;
   }
 
 /*************************************************************************/  
   // set initial variables with ???????/
   function create_new() {
        if($this->table_title != '' ) {
            $this->title = 'New ' . $this->table_title .
            $this->makeForm();
        }
   }

 /*************************************************************************/  
   // Clone the original record in the Database. 
   // This assumes the datatypes match correctly.
   function clone_record($db_table) {
        $db_obj = new db_class();
        $typeHash = $db_obj->columnTypeHash($this->table_title);
        
        $sql = 'INSERT INTO ' . $this->table_title ;
        $typeList = '';
        $columns = ' (' ;
        $values = '(' ;
        $paramList = array();
        $count = 0;
        
        foreach($db_table as $key => $value) {
            if($count > 0){ 
                $columns .= ',' ; 
                $values .= ',';
            }
            $columns .= $key;
            $values .= '?';
            $paramList[] = $value;
            $typeList .= $typeHash[$key];
            
            $count++;
        }
        
        $columns .= ')';
        $values .= ')';
        $sql .= $columns . ' VALUES ' . $values;
        
        showArray(array($sql, $typeList, $paramList));
        
        $db_obj->closeDB();
        
   }
  
 /*************************************************************************/  
   // set initial variables with 
   function fetchRecordInfo($identifier) {
        $db_obj = new db_class();
        $primary_key = $db_obj->getPrimaryKey($this->table_title);
        $sql = 'SELECT * FROM ' . $this->table_title . ' WHERE ' . $primary_key . '=?';
        
        $keyTypeHash = $db_obj->columnTypeHash($this->table_title);
        $keyType = $keyTypeHash[$this->primary_key];
        $db_table = $db_obj->simpleOneParamRequest($sql, $keyType, $identifier);
        $db_obj->closeDB();
        
        return $db_table[0];
   }


} /* end class definition */