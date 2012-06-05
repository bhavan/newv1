<script type="text/javascript">
  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-31932515-1']);
  _gaq.push(['_setDomainName', 'auto']);
  _gaq.push(['_trackPageview']);

  <?php if($var->googgle_analytics != null && !empty($var->googgle_analytics)){
  	
			// Substring function to search the script tag in Google analytic code
  			$searchScriptTag = substr(trim($var->googgle_analytics), 1, 7);
  			
			if(trim($searchScriptTag) != 'script'){?>
  				_gaq.push(['t2._setAccount', '<?php echo $var->googgle_analytics;?>']);  
  				_gaq.push(['t2._trackPageview']);
		<?php }
  		}?>

  	(function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();
</script>
