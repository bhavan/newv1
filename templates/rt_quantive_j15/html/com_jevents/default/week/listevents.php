<?php 
defined('_JEXEC') or die('Restricted access');

/* removing the header */
/* $this->_header(); */
$this->_showNavTableBar();

echo $this->loadTemplate("body");

$this->_viewNavAdminPanel();

$this->_footer();


