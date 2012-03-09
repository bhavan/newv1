var myGeocoder = null;

function findAddressGeo(eventObj, submit){
	address = document.getElementById("googleaddress");
	if (address){
		address = address.value
	}
	if (!address || address.length==0){
		// uncomment to make compulsary
		// alert("Please enter an address and/or zip code");
		return false;
	}
	if (myGeocoder) {
		var myTimer = trapSlowGoogle.delay(3000);
		myGeocoder.getLatLng(
		address,
		function(point) {
			if (!point) {
				alert("Address/Zip Code "+ address + " not found.");
			} else {
				gvelem = document.getElementById("geosearch_fv");
				gvelem.value = point.y+","+point.x;

				$clear(myTimer);
				// since we stopped the event we must now allow the form to submit !!!
				 if (submit){
					myform = $("jeventspost");
					myform.submit();
				 }

			};
		});
	}
}

function trapSlowGoogle(){
	alert("The address lookup is taking too long and so the address filter has been ignored");
	myform = $("jeventspost");
	myform.submit();
}

function clearlonlat(){
	gvelem = document.getElementById("geosearch_fv");
	gvelem.value="";
}

window.addEvent("load",function (){
	if (typeof GBrowserIsCompatible == "function" && GBrowserIsCompatible()) {
		myGeocoder = new GClientGeocoder();
	}
	// set the form onsubmit event
	var myform = $("jeventspost");
	if (myform){
		myform.addEvent("submit", function (event){
			address = document.getElementById("googleaddress");
			if (address){
				address = address.value
			}
			// force submit to wait for the address to be checked
			if (address && address.length>0){
				var eventObj = new Event(event);
				eventObj.stop();
				findAddressGeo(eventObj, 1);
			}
		});

	};
});
