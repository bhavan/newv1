var myMap = false;
var myMarker = false;
var myGeocoder = null;
var peopleDeleteWarning = "";

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
			address = "";
			if (street.length>0){
				address+=street+",";
			}
			if (city.length>0){
				address+=city+",";
			}
			if (state.length>0){
				address+=state+",";
			}
			if (postcode.length>0){
				address+=postcode+",";
			}
			if (country.length>0){
				address+=country;
			}
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



var sortablePeople = {
	setup:function (){
		new Sortables('sortablePeople',{"onComplete":sortablePeople.fieldsHaveReordered});
		var uls = $('sortablePeople');
		var lis = uls.getChildren();
		lis.each(function(item, i){
			sortablePeople.copyTrash(item);
		},this);
		/*
		var trashitems = $$('.sortabletrash');
		trashitems.each(function(item, i){
		sortablePeople.setupTrashImage(item);
		},this);
		*/
	},
	copyTrash:function(item){
		var trashimage = $('trashimage');
		var child = trashimage.clone();
		child.style.display="inline";
		child.style.marginLeft="5px";
		child.style.lineHeight = item.style.lineHeight = "16px";
		item.style.backgroundImage="none";
		item.style.listStyleType="none";
		item.style.paddingLeft="0px";
		//item.appendChild(child);
		child.inject(item,"top");
		sortablePeople.setupTrashImage(child);
	},
	fieldsHaveReordered:function(targetNode){
		// Now rebuild the select list items
		var custom_person = $("custom_person");
		if (custom_person){
			var options = custom_person.getChildren();

			// new dummy selectlist
			var selectList = new Element('select');

			options.each(function (item,i){
				selectList.appendChild(item);
				//item.remove();
			});

			var uls = $('sortablePeople');
			var lis = uls.getChildren();
			lis.each(function(item, i){
				selectList.getChildren().each(function(opt,j){
					if (opt.id==item.id+"option"){
						custom_person.appendChild(opt);
						opt.selected = true;
					}
				});

			});
		}
		else {
			var menuperson = $("menuperson");
			if (menuperson) {
				menuperson.value = "";
				var uls = $('sortablePeople');
				var lis = uls.getChildren();
				lis.each(function(item, i){

					var id = item.id.replace("sortablepers","");
					menuperson.value += 'jevp:'+id+",";
				});

			}

		}

	},
	setupTrashImage:function(item){
		item.addEvent('mousedown',function(event){
			event = new Event(event);
			event.stop();
			if (!confirm(peopleDeleteWarning)) return;
			var id = event.target.parentNode.id;
			// remove the item from the li list
			event.target.parentNode.remove();
			// remove the item from the select list
			var option = $(id+"option");
			if (option) option.remove();

			var menuperson = $("menuperson");
			id = id.replace("sortablepers","");
			if (menuperson)  menuperson.value = menuperson.value.replace('jevp:'+id+",", "");
		});
	},
	selectThisPerson:function (personid, elem, typename){
		var duplicateTest = $("sortablepers"+personid);
		if (duplicateTest) {
			alert(jevpeople.duplicateWarning);
			SqueezeBox.close();
			return;
		}
		var title = elem.innerHTML;
		var custom_person = $('custom_person');
		var opt = new Element('option',{value:personid,id:"sortablepers"+personid+"option"});
		opt.text = title + " ("+typename+")";
		opt.selected = true;
		if (custom_person){
			custom_person.appendChild(opt);
		}
		// No do the visible list item too
		var uls = $('sortablePeople');
		var li = new Element('li',{id:"sortablepers"+personid});
		li.appendText(opt.text);
		if (uls){
			uls.appendChild(li);
			sortablePeople.copyTrash(li);

			// reset the sortable list
			new Sortables('sortablePeople',{"onComplete":sortablePeople.fieldsHaveReordered});
		}

		// If actually selecting a person for a menu item we do something different:
		var menuperson = $('menuperson');
		if (menuperson){
			menuperson.value += "jevp:"+personid+",";
		}

		SqueezeBox.close();
		return false;
	},
	selectPerson:function (url){

		SqueezeBox.initialize({});
		SqueezeBox.setOptions(SqueezeBox.presets,{'handler': 'iframe','size': {'x': 750, 'y': 500},'closeWithOverlay': 0});
		SqueezeBox.url = url;

		SqueezeBox.setContent('iframe', SqueezeBox.url );
		return;// SqueezeBox.call(SqueezeBox, true);

	}



}

var ltujsonactive = false;
var cancelSearch = true;
var ltutimeout=false;
var ignoreSearch=false;

function findUser(e,elem, url, client){

	if (ignoreSearch) return;
	var key = 0;
	if (window.event){
		key = e.keyCode;
	}
	else if (e.which){
		key = e.which;
	}
	if (elem.value.length == 0 || key==8 || key==46){
		// clearing
		ltuClearMatches();
		currentSearch = "";
		return;
	}

	var requestObject = new Object();
	requestObject.error = false;
	requestObject.token = jsontoken;
	requestObject.task = "checkTitle";
	requestObject.title = elem.value;
	requestObject.type = $("type_id").value;
	requestObject.client = client;

	minlength=2;

	if (elem.value.length>=minlength){
		if (ltujsonactive) return;

		currentSearch = elem.value;

		if (ltutimeout) {
			clearTimeout(ltutimeout);
		}

		//url += '?start_debug=1&debug_host=127.0.0.1&debug_port=10000&debug_stop=1';

		ltujsonactive = true;
		var jSonRequest = new Json.Remote(url, {
			method:'get',
			onComplete: function(json){
				cancelSearch = false;
				ltujsonactive = false;
				if (json.error){
					try {
						eval(json.error);
					}
					catch (e){
						alert('could not process error handler');
					}
				}
				else {
					// If have started another search already then cancel this one
					if (cancelSearch) {
						return;
					}
					var ltumatches = document.getElement("#ltumatches");
					//alert(json.timing);
					if (json.titles.length==1){
						var ltumatches = document.getElement("#ltumatches");
						ltumatches.style.display="none";
						ltuClearMatches();

						var newid = json.titles[0]["id"]

						var linktouser = $("linktouser");
						var linktousertext = $("linktousertext");
						linktouser.value = newid;
						linktousertext.innerHTML=json.titles[0]["name"]+" ("+json.titles[0]["username"]+")";
					}
					else if (json.titles.length>1){
						ltumatches.style.display="block";
						ltuClearMatches();
						var shownotes = false;
						for (var jp=0;jp<json.titles.length;jp++){
							// If have started another search already then cancel this one
							if (cancelSearch) {
								return;
							}
							var option = new Element('div', {id:"ltu_"+json.titles[jp]["id"]});
							option.addEvent('mousedown', ltuaddInvitee.bindWithEvent(option));
							option.appendText(json.titles[jp]["name"]+" ("+json.titles[jp]["username"]+")");

							option.injectInside(ltumatches);
						}
					}
					else {
						ltuClearMatches();
					}

					// If have started another search already then cancel this one
					if (cancelSearch) {
						return;
					}
				}
			},
			onFailure: function(){
				if (ignoreSearch) return;
				ltujsonactive = false;
				alert('Something went wrong...')
				ltuClearMatches();
			}
		}).send(requestObject);
	}
}

function ltuClearMatches(){
	if (ltutimeout) {
		clearTimeout(ltutimeout);
	}
	var ltumatches = document.getElement("#ltumatches");
	ltumatches.innerHTML = "";
}

function ltuaddInvitee(event){
	var oldid = this.id;
	var newid = this.id.replace("ltu_","");

	var linktouser = $("linktouser");
	var linktousertext = $("linktousertext");
	linktouser.value = newid;
	linktousertext.innerHTML=this.innerHTML;

	var ltumatches = document.getElement("#ltumatches");
	ltumatches.style.display="none";
	ltuClearMatches();

}
