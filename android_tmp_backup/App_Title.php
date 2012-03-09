<?php
include("connection.php");
 $sql = "select * from `jos_pageglobal`";
  $rec = mysql_query($sql) or die(mysql_error());
  $row=mysql_fetch_array($rec);
echo"
<generic_app>
<title>
".$row['site_name']."
</title>
<email>
".$row['email']."
</email>
<weather>
http://xoap.weather.com/weather/local/".$row['location_code']."?link=xoap&amp;cc=*&amp;dayf=5&amp;unit=s&amp;par=1005217190&amp;key=2e4490982af206e0
</weather>
</generic_app>";
?>