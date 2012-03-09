<?php
###################################################################################
#
# Weather report 1.3 index.php, by Aid Arslanagic, version 0.3
# http://www.simpa.ba
#
# This code is released under The GNU General Public License (GPL).
# Read the license at http://www.opensource.org/licenses/gpl-license.php
#
###################################################################################

require ("inc/config.php"); 

$handle = fopen($query, "r");
$xml = '';
while (!feof($handle)) {
  $xml.= fread($handle, 8192);
}
fclose($handle);
$data = XML_unserialize($xml);

###################################################################################
# Change the layout for your site below
###################################################################################

###################################################################################
# DAY 0
###################################################################################
echo "<TABLE border=0><TR><TD VALIGN=TOP ALIGN=CENTER WIDTH=94>";
echo "<div style='padding-top:8px;padding-bottom:10px;'><strong>Today</strong></div>";
echo "<IMG SRC='icons/" . $data[weather][dayf][day][0][part][0][icon] . ".png' width='60' height='50'><br />";
echo str_replace('N/A','0',$data[weather][dayf][day][0][hi]) . "&#176; / "  . str_replace('N/A','0',$data[weather][dayf][day][0][low]) . "&#176;<br /><small><i>" . $data[weather][dayf][day][0][part][1][t] . "</i></small></TD>";

###################################################################################
# DAY 1
###################################################################################
echo "<TD VALIGN=TOP ALIGN=CENTER WIDTH=94>";
echo "<div style='padding-top:8px;padding-bottom:10px;'><strong>Tomorrow</strong></div>";
echo "<IMG SRC='icons/" . $data[weather][dayf][day][1][part][0][icon] . ".png' width='60' height='50'><br />";
echo str_replace('N/A','0',$data[weather][dayf][day][1][hi]) . "&#176; / "  . str_replace('N/A','0',$data[weather][dayf][day][1][low]) . "&#176;<br /><small><i>" . $data[weather][dayf][day][1][part][1][t] . "</i></small></TD>";

###################################################################################
# DAY 2
###################################################################################date('D, F j', strtotime('+2 day'))
echo "<TD VALIGN=TOP ALIGN=CENTER WIDTH=94>";
echo "<div style='padding-top:8px;padding-bottom:10px;'><strong>".date('l', strtotime('+2 day'))."</strong></div>";
echo "<IMG SRC='icons/" . $data[weather][dayf][day][2][part][0][icon] . ".png' width='60' height='50'><br />";
echo str_replace('N/A','0',$data[weather][dayf][day][2][hi]) . "&#176; / "  . str_replace('N/A','0',$data[weather][dayf][day][2][low]) . "&#176;<br /><small><i>" . $data[weather][dayf][day][2][part][1][t] . "</i></small></TD>";

###################################################################################
# DAY 3
###################################################################################
echo "<TD VALIGN=TOP ALIGN=CENTER WIDTH=94>";
echo "<div style='padding-top:8px;padding-bottom:10px;'><strong>".date('l', strtotime('+3 day'))."</strong></div>";
echo "<IMG SRC='icons/" . $data[weather][dayf][day][3][part][0][icon] . ".png' width='60' height='50'><br />";
echo str_replace('N/A','0',$data[weather][dayf][day][3][hi]) . "&#176; / "  . str_replace('N/A','0',$data[weather][dayf][day][3][low]) . "&#176;<br /><small><i>" . $data[weather][dayf][day][3][part][1][t] . "</i></small></TD>";

###################################################################################
# DAY 4
###################################################################################
echo "<TD VALIGN=TOP ALIGN=CENTER WIDTH=94>";
echo "<div style='padding-top:8px;padding-bottom:10px;'><strong>".date('l', strtotime('+4 day'))."</strong></div>";
echo "<IMG SRC='icons/" . $data[weather][dayf][day][4][part][0][icon] . ".png' width='60' height='50'><br />";
echo str_replace('N/A','0',$data[weather][dayf][day][4][hi]) . "&#176; / "  . str_replace('N/A','0',$data[weather][dayf][day][4][low]) . "&#176;<br /><small><i>" . $data[weather][dayf][day][4][part][1][t] . "</i></small></TD></TR>";
echo "</TABLE>";

###################################################################################
# Uncomment this to see complete array:
#
#echo "<pre>";
#print_r($data);
#echo "</pre>";
###################################################################################
?>