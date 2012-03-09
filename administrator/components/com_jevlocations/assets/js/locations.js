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
			if (latlng) {
				document.getElementById('geolat').value=latlng.y;
				document.getElementById('geolon').value=latlng.x;
				document.getElementById('geozoom').value=myMap.getZoom();

				myMarker.setLatLng(latlng);
			}
		});

		GEvent.addListener(myMarker, "dragend", function(latlng) {
			if (latlng) {
				document.getElementById('geolat').value=latlng.y;
				document.getElementById('geolon').value=latlng.x;
				document.getElementById('geozoom').value=myMap.getZoom();
			}
		});

		GEvent.addListener(myMap, "zoomend", function(oldZoom,newZoom) {
			if (newZoom) {
				document.getElementById('geozoom').value=myMap.getZoom();
			}
		});
		
        myGeocoder = new GClientGeocoder();
		
	}
};

window.addEvent("load",myMapload);


function findAddress(){
	address = document.getElementById("googleaddress");
	if (address){
		address = address.value
		country = document.getElementById("googlecountry").value;
		address += ","+country;
	}
	else {
		try {
			street = document.getElementById("street").value;	
			city = document.getElementById("city").value;	
			state = document.getElementById("state").value;	
			country = document.getElementById("country").value;	
			postcode = document.getElementById("postcode").value;	
			address = street+","+city+","+state+","+postcode+","+country;
		}
		catch (e){}
	}
	if (myGeocoder) {
		myGeocoder.getLatLng(
		address,
		function(point) {
			if (!point) {
				alert(address + " not found");
			} else {
				myMap.setCenter(point,14);
				myMarker.setLatLng(point);
				document.getElementById('geolat').value=point.y;
				document.getElementById('geolon').value=point.x;
				document.getElementById('geozoom').value=myMap.getZoom();
			}
		}
		);
	}
}

function selectLocation(locid,url, x, y){

	SqueezeBox.initialize({});
	SqueezeBox.setOptions(SqueezeBox.presets,{'handler': 'iframe','size': {'x': x, 'y': y},'closeWithOverlay': 0});
	SqueezeBox.url = url;
		
	SqueezeBox.setContent('iframe', SqueezeBox.url );
	return;// SqueezeBox.call(SqueezeBox, true);	
	
}

function selectThisLocation(locid, elem){
	var title = elem.innerHTML;
	var locn = window.parent.document.getElementById('locn');
	if (locn){
		locn.value  = locid;
	}
	var evlocation = window.parent.document.getElementById('evlocation');
	if (evlocation){
		evlocation.value = title;
		// If actually selecting a location for a menu item we do something different:
		var menu_location = window.parent.document.getElementById('menu_location');
		if (menu_location){
			menu_location.value = "jevl:"+locid;
		}
	}
	// else this is a menu so do something different
	else {
		return window.parent.sortableLocations.selectThisLocation(locid, elem);
	}

	window.parent.SqueezeBox.close();
	return false;
}

function removeLocation(){
	document.getElementById('locn').value='';
	document.getElementById('evlocation').value='';
	return;
}

var sortableLocations = {
	setup:function (){
		new Sortables('sortableLocations',{"onComplete":sortableLocations.fieldsHaveReordered});
		var uls = $('sortableLocations');
		var lis = uls.getChildren();
		lis.each(function(item, i){
			sortableLocations.copyTrash(item);
		},this);
	},
	copyTrash:function(item){
		var trashimage = $('trashimageloc');
		var child = trashimage.clone();
		child.style.display="inline";
		child.style.marginLeft="5px";
		child.style.lineHeight = item.style.lineHeight = "16px";
		item.style.backgroundImage="none";
		item.style.listStyleType="none";
		item.style.paddingLeft="0px";
		//item.appendChild(child);
		child.inject(item,"top");
		sortableLocations.setupTrashImage(child);
	},
	fieldsHaveReordered:function(targetNode){
		// Now rebuild the select list items
		var menuloc = $("menuloc");
		if (menuloc) {
			menuloc.value = "";
			var uls = $('sortableLocations');
			var lis = uls.getChildren();
			lis.each(function(item, i){

				var id = item.id.replace("sortableloc","");
				menuloc.value += 'jevl:'+id+",";
			});

		}


	},
	setupTrashImage:function(item){
		item.addEvent('mousedown',function(event){
			event = new Event(event);
			event.stop();
			var id = event.target.parentNode.id;
			// remove the item from the li list
			event.target.parentNode.remove();
			// remove the item from the select list
			var option = $(id+"option");
			if (option) option.remove();

			var menuloc = $("menuloc");
			id = id.replace("sortableloc","");
			if (menuloc)  menuloc.value = menuloc.value.replace('jevl:'+id+",", "");
		});
	},
	selectThisLocation:function (locid, elem){
		var duplicateTest = $("sortableloc"+locid);
		if (duplicateTest) {
			alert(jevlocations.duplicateWarning);
			SqueezeBox.close();
			return;
		}
		var title = elem.innerHTML;

		// No do the visible list item too
		var uls = $('sortableLocations');
		var li = new Element('li',{id:"sortableloc"+locid});
		li.appendText(title);
		if (uls){
			uls.appendChild(li);
			sortableLocations.copyTrash(li);

			var togdown = document.getElement('h3.jpane-toggler-down');
			var tab = togdown.getNext();
			var tabtable = tab.getElement('table.paramlist');
			tab.setStyle('height',tabtable.offsetHeight);
			
			// reset the sortable list
			new Sortables('sortableLocations',{"onComplete":sortableLocations.fieldsHaveReordered});
		}

		// If actually selecting a location for a menu item we do something different:
		var menuloc = $('menuloc');
		if (menuloc){
			menuloc.value += "jevl:"+locid+",";
		}

		SqueezeBox.close();
		return false;
	},
	selectLocation:function (url,x,y){

		SqueezeBox.initialize({});
		SqueezeBox.setOptions(SqueezeBox.presets,{'handler': 'iframe','size': {'x': x, 'y': y},'closeWithOverlay': 0});
		SqueezeBox.url = url;

		SqueezeBox.setContent('iframe', SqueezeBox.url );
		return;// SqueezeBox.call(SqueezeBox, true);

	}



}