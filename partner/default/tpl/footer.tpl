<div id="footerWrap">
  <ul id="footerAds">
    <li><?php m_show_banner('Website Footer 1'); ?></li>
    <li><?php m_show_banner('Website Footer 2'); ?></li>
    <li><?php m_show_banner('Website Footer 3'); ?></li>
    <li><?php m_show_banner('Website Footer 4'); ?></a></li>
  </ul>
  <ul id="leftLinks">
    <li><a href="about_us.php">About Us</a>&nbsp;&nbsp;|&nbsp;&nbsp;</li> 
    <li><a href="links.php">Links</a>&nbsp;&nbsp;|&nbsp;&nbsp;</li> 
    <li><a href="advertise.php">Advertise</a>&nbsp;&nbsp;|&nbsp;&nbsp;</li> 
    <li><a href="contact_us.php">Contact Us</a></li>
  </ul>
  <ul id="rightLinks">
    <li><a href="terms_of_service.php">Terms of Use </a>&nbsp;&nbsp;|&nbsp;&nbsp;</li> 
    <li><a href="privacy_policy.php">Privacy Policy</a>&nbsp;&nbsp;|&nbsp;&nbsp;</li> 
    <li>&copy;&nbsp;<?PHP $time = time () ; $year= date("Y",$time); echo $year . "&nbsp;" . $var->site_name; ?></li>
    <li id="partner"><a href="http://www.townwizard.com/" target="_blank">Town Wizard Partner</a></li>
  </ul>
</div> <!-- footerWrap -->