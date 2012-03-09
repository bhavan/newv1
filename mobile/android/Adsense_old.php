
<!-- eg: Adsense.html?cat='iPhone-Events'-->

<html>

<head>
	<!-- PUT THIS TAG IN THE head SECTION -->
	<title>Google Ad</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
	<script type="text/javascript" src="http://partner.googleadservices.com/gampad/google_service.js"></script>
	<script type="text/javascript">
		GS_googleAddAdSenseService("ca-pub-3838836092298493");
		GS_googleEnableAllServices();
	</script>

	<script type="text/javascript">

	function getQueryVariable(variable)
	{
	    var query = window.location.search.substring(1);
	    var vars = query.split("&");
	    for (var i=0;i<vars.length;i++)
	    {
	        var pair = vars[i].split("=");
	        if (pair[0] == variable)
	        {
	            return pair[1];
	        }
	    }
	}

	</script>

	<script type="text/javascript">
		 var szCategory = getQueryVariable('cat');

		GA_googleAddSlot("ca-pub-3838836092298493", szCategory);
	</script>
	<script type="text/javascript">
		GA_googleFetchAds();
	</script>
	<script language="javascript">
		function linkClicked(link) { document.location = link; }
	</script>
	<style>
		body {
			background-color: #000000;
			margin: 0px;
		}
	</style>
	<!-- END OF TAG FOR head SECTION -->
</head>

<body>

	<div style="height:0px; width:320px;"></div>

	<!-- PUT THIS TAG IN DESIRED LOCATION OF SLOT iPhone-30A -->
	<script type="text/javascript">
		 var szCategory = getQueryVariable('cat');

		GA_googleFillSlot(szCategory);
	</script>
	<!-- END OF TAG FOR SLOT iPhone-30A -->
</body>

</html>