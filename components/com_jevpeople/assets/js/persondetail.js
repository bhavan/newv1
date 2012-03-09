var myMap = false;
var myMarker = false;
var myGeocoder = null;

function myMapload(){
	
	if (typeof GBrowserIsCompatible == "function" && GBrowserIsCompatible()) {

		myMap = new GMap2(document.getElementById("gmap"));
		myMap.addControl( new GSmallMapControl() );
		myMap.addControl( new GMapTypeControl()) ;
		myMap.addControl( new GOverviewMapControl(new GSize(60,60)) );

		/*
		// Create our "tiny" marker icon
		var blueIcon = new GIcon(G_DEFAULT_ICON);
		blueIcon.image = "http://www.google.com/intl/en_us/mapfiles/ms/micons/blue-dot.png";

		// Set up our GMarkerOptions object
		markerOptions = { icon:blueIcon, draggable:true };
		*/
		markerOptions = {draggable:true };

		var point = new GLatLng(globallat,globallong);
		myMap.setCenter(point, globalzoom );

		myMarker = new GMarker(point, markerOptions);
		myMap.addOverlay(myMarker);


		GEvent.addListener(myMap, "click", function(overlay,latlng) {
			window.open (googleurl+"/maps?f=q&geocode=&time=&date=&ttype=&ie=UTF8&t=h&om=1&q="+globaltitle+"@"+globallat+","+globallong+"&ll="+globallat+","+globallong+"&z="+globalzoom+"&iwloc=addr","map");
		});

				
	}
};

window.addEvent("load",myMapload);

