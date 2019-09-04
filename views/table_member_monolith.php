<?php
// table_member.php -  Wed Aug 28 14:29:18 CDT 2019
// 

include "../essentials.php";
require_once "table_class.php";


class table_member extends table_class {
    function __construct() {
        parent::__construct('Members',  'member', 'form_member.php', 'user_id',
          array('user_id' => 'ID', 'first_name' =>'First', 'last_name' => 'Last', 
          'email' => 'Email'));
    }
}

$table_member = new table_member();
$table_member->execute();