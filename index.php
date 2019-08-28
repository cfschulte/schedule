<?php
// index.php -  Wed Aug 28 13:57:51 CDT 2019
// 
	require_once "essentials.php";

    $userInfo = check_login();
?>
<!DOCTYPE html>
<html lang="en">
<HEAD>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
 <META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">

<title>Schedule</title>
<link rel="stylesheet" href="css/style.css">
</HEAD>
<body>

<h1>Schedule</h1>   

<h2><a href="views/member_table.php"   ></a></h2>

 <div id="footer">
    <p>Department of Human Oncology, UW Medical School, 600 Highland Ave., Madison, WI 53792</p>
    <p><a href="http://www.med.wisc.edu/">University of Wisconsin School of Medicine and Public Health</a></p>
    <p>© 2017 Board of Regents of the <a href="http://www.wisconsin.edu">University of Wisconsin System</a></p>
    
  <?php
     date_default_timezone_set("America/Chicago");
     echo "<p>This page was last modified: " .  date("d F Y", getlastmod()) . "</p>";
  ?>  
  	
  </div>
</body>
</html>
