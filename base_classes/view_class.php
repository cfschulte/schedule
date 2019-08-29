<?php
// view_class.php -  Thu Aug 29 12:03:30 CDT 2019
// I'm going to try something. This is a generic view that will 
// contain forms, tables, and whatever else needs to be contained.
// I'm going to make the tables a little more flexible and containable.


require_once "../essentials.php";

class view_class {
    protected $title;
    protected $member_id;
    protected $member_priveleges;
    protected $is_new;

    
 /*************************************************************************/   
    function __construct( $title = '') {
        $this->title = $title;
        $this->is_new = 0;
        
    }


 /*************************************************************************/  
 // Execute the page.  
    function execute() {
        $userInfo = check_login(); // put this into this class?
        $this->member_id = $userInfo['member_id'];
        $this->member_privilges = $userInfo['authority'];

        if(!empty($_POST) ) {
            $this->handle_post($_POST);
        } elseif(!empty($_GET) ) {
            $this->handle_get($_GET);
        } else {
            $this->handle_no_input();
        }
?>
<!DOCTYPE html>
<html>
    <?php 
        $this->header(); 
        $this->body(); 
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
    <script type="text/javascript" src="/jquery/jquery-ui/jquery-ui.min.js"></script>
<?php 
    }
 
 /*************************************************************************/  
 // The load the javascripts -- it can be called from the child classes 
 // and then augmented.  
 // NOTE: we will probably want a print.css 
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
    $this->page_content();
?> 
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
<?php 
   }

 /*************************************************************************/  
   // additionalHeaderStuff - optional things to go into the header.
   function additionalHeaderStuff() {
        //virtual
   }

    
 /*************************************************************************/  
   // The main content for the page.
   function page_content() {
    // virtual
   }

    
 /*************************************************************************/  
   // 
   function handle_get($indata) {
   }
    
    
 /*************************************************************************/  
   // 
   function handle_post($indata) {
   }
 
 
 /*************************************************************************/  
   // 
   function handle_no_input() {
   }
   
}

