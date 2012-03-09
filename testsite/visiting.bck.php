<?php

global $var;
include_once('./inc/var.php');
include_once($var->inc_path.'base.php');
_init();

?>

<!DOCTYPE HTML>
<html>
<head>
<title>Tally Life | Visiting</title>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<script>
  document.createElement('header');
  document.createElement('nav');
  document.createElement('section');
  document.createElement('article');
  document.createElement('aside');
  document.createElement('footer');
</script>
<link rel="stylesheet" type="text/css" href="common/css/all.css" media="screen" />
</head>

<body>

<header>
	<?php m_header(); ?> <!-- header -->
</header>
<div id="wrapper">
	<aside>
    <?php m_aside(); ?>
	</aside> <!-- left Column -->
	<section>
    <h2>Visit Tallahasse</h2>
    <table><tbody>
      <tr>
        <td>
          <?php m_visiting_intro(); ?>
        </td>
        <td>
          <div class="adThreeHunderd">
            <a href=""><img src="common/images/adHolder2.png" alt="sample ad" /></a>
          </div> <!-- adThreeHunderd -->
        </td>
      </tr>
    </tbody></table>
    <br />
    <h2>Hotels</h2>
    <table><tbody>
      <tr>
        <td>
          <?php m_location_list('Hotels'); ?>
        </td>
        <td>
          <div class="adThreeHunderd" style="margin-left:0px;">
            <a href=""><img src="common/images/adHolder2.png" alt="sample ad" /></a>
          </div> <!-- adThreeHunderd -->
        </td>
      </tr>
    </tbody></table>
    
	</section> <!-- rightColumn -->
</div> <!-- wrapper -->
<footer>
	<?php m_footer(); ?> <!-- footer -->
</footer>

</body>
</html> 