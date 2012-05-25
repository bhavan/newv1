<div id="footerWrap">
  <ul id="footerAds">
    <li><?php m_show_banner('Website Footer 1'); ?></li>
    <li><?php m_show_banner('Website Footer 2'); ?></li>
    <li><?php m_show_banner('Website Footer 3'); ?></li>
    <li><?php m_show_banner('Website Footer 4'); ?></a></li>
  </ul>
  <ul id="leftLinks">
    <li><a href="about_us.php">Acerca de Nosotros</a>&nbsp;|&nbsp;</li> 
    <li><a href="links.php">Enlaces</a>&nbsp;|&nbsp;</li> 
    <li><a href="advertise.php">Anunciar</a>&nbsp;|&nbsp;</li> 
    <li><a href="contact_us.php">Contacte con Nosotros</a></li>
  </ul>
  <ul id="rightLinks">
    <li><a href="terms_of_service.php">Términos de Uso </a>&nbsp;|&nbsp;</li> 
    <li><a href="privacy_policy.php">Política de Privacidad</a>&nbsp;|&nbsp;</li> 
    <li>&copy;&nbsp;<?PHP $time = time () ; $year= date("Y",$time); echo $year . "&nbsp;" . $var->site_name; ?></li>
    <li id="partner"><a href="http://www.townwizard.com/" target="_blank">Town Wizard Socio</a></li>
  </ul>
</div> <!-- footerWrap -->