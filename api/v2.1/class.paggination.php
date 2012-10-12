<?php
//
//
//@class variables
//
//
class pagination{
var $HOST_NAME;
var $HOST_USER;
var $HOST_PWD;
var $SELECTED_DB;
var $CONNECTION_LINK;
var $DB_LINK;
var $RECORDSOURCE;
var $NUM_RECORD;
var $RETURNID;
var $RECORD_PER_SHEET;
var $NUM_PAGES;
var $CURRENT_PAGE;
var $QRY;
var $START;
//
//@Function for set the host name
//@input::Host name
//@return::
function set_qry($qry)
{
$this->QRY=$qry;
}
function set_record_per_sheet($record)
{
$this->RECORD_PER_SHEET=$record;
}
function current_page()
{
return $this->CURRENT_PAGE;
}
function num_pages()
{
$recsc=mysql_query($this->QRY);
$this->NUM_PAGES=ceil((mysql_num_rows($recsc)/$this->RECORD_PER_SHEET));
return $this->NUM_PAGES;
}
function start_page()
{
if ($this->CURRENT_PAGE-5>0)
$start=$this->CURRENT_PAGE-5;
else
$start=0;
$this->START=$start;
if ($this->START+10>$this->NUM_PAGES && $this->START-10>0)
$this->START=$this->NUM_PAGES-10;
return $this->START;
}
function end_page()
{
if ($this->START+10<$this->NUM_PAGES)
$end=$this->START+10;
else
$end=$this->NUM_PAGES;
return $end;
}
function set_host_name($hostname)
{
	$this->HOST_NAME=$hostname;
}
//
//
//@End of function
//
//@Function for set the host user
//@input::Host user name
//@return::
function set_host_user($hostuser)
{
$this->HOST_USER=$hostuser;
}
//
//
//@End of function
//
//@Function for set the host password
//@input::host password
//@return::
//
function set_host_PWD($hostpwd)
{
$this->HOST_PWD=$hostpwd;
}
//
//
//@End of function
//
//@Function for selected the database
//@input::database name
//@return::
function set_selected_db($selecteddb)
{
$this->SELECTED_DB=$selecteddb;
}
//
//
//@End of function
//
//@constructor
//@input::Host name,Hosu user name,Host password,Database name
//@return::
function pagination($hostname,$hostuser,$hostpwd,$selecteddb)
{
/*echo $hostuser;
echo $hostpwd;
echo $selecteddb;*/
$this->HOST_NAME=$hostname;
$this->HOST_USER=$hostuser;
$this->HOST_PWD=$hostpwd;
$this->SELECTED_DB=$selecteddb;
}
//
//@End of function
//
//@Function for established a connection
//@input::
//@return::
//
function connection()
{
$this->CONNECTION_LINK=@mysql_pconnect($this->HOST_NAME,$this->HOST_USER,$this->HOST_PWD);
if(!$this->CONNECTION_LINK)
die("Could not connect: " . mysql_error());
$this->DB_LINK=@mysql_select_db($this->SELECTED_DB,$this->CONNECTION_LINK);
if(!$this->DB_LINK)
die("'Can\'t use".$this->SELECTED_DB.mysql_error());
}
//
//
//@Function to exacute a query and return the record set
//@input::query
//@return::record set
//
//
function execute_query($recordno)
{
if ($recordno>=$this->RECORD_PER_SHEET)
$this->CURRENT_PAGE=ceil(($recordno/$this->RECORD_PER_SHEET));
else
$this->CURRENT_PAGE=0;
$qry=$this->QRY;



if(!($this->CONNECTION_LINK && $this->DB_LINK))
	$this->connection();

$this->RECORDSOURCE	=	mysql_query($this->QRY.' limit '.($recordno).' , '.$this->RECORD_PER_SHEET);

if (!$this->RECORDSOURCE)
	die("Invalid query: ".mysql_error());

$this->RETURNID		=	mysql_insert_id();
$this->NUM_RECORD	=	mysql_num_rows($this->RECORDSOURCE);

return $this->RECORDSOURCE;
}

//
//@End of function
//
//@Function to check no of record present
//@return::number of record 
//
//
function num_record()
{
return $this->NUM_ROWS;
}
//
//@End of function
//
//@Function to check that any record exist or not
//@return::true or false
//
function isanyrecord()
{
if ($this->NUM_ROWS)
return true;
else
return false;
}
//
//
//@End of function
//
//return last insert id
//
//
function last_id()
{
return $this->RETURNID;
}
}
?>