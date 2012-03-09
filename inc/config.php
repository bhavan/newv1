<?php
###################################################################################
#
# Weather report 1.3 config.php, by Aid Arslanagic, version 0.3
# http://www.simpa.ba
#
# This code is released under The GNU General Public License (GPL).
# Read the license at http://www.opensource.org/licenses/gpl-license.php
#
###################################################################################
require ("xmllib.php");
###################################################################################
# XML Library, by Keith Devens, version 1.2
# http://keithdevens.com/software/phpxml
###################################################################################

$source = "http://xoap.weather.com/weather/local/";
$prod = "xoap";
###################################################################################
# Check your location at this address:
# http://xoap.weather.com/search/search?where=sarajevo # replace "sarajevo" with
# your city and enter your location here:
$code = $var->location_code;
###################################################################################
$cc = "*";
###################################################################################
# You can change units from Metric "m" to Standard "s":
$unit = "s";
###################################################################################
# You can change number of days:
$dayf = "5";
###################################################################################
$par = "1005217190";
$key = "2e4490982af206e0";
# note that prod=xoap recently changed to link=xoap - thanks to Neal-at-fenna.co.uk
$query =  $source . $code . "?link=" . $prod . "&cc=" . $cc . "&dayf=" . $dayf . "&unit=" . $unit . "&par=" . $par . "&key=" . $key;
?>