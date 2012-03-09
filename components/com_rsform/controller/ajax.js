var list = new Array();
checked = false;

var xmlHttp = null;
var xmlHttp2 = null;
var xmlHttpArray = new Array();
function CreateXMLHttpRequest()
{
	//Ajax script to create the browser based ActiveXObject object
	var xmlHttpRequest = false;
	try
	{
		if(window.ActiveXObject)
		{
			for(var i = 5; i; i--)
			{
				try
				{
					if(i == 2)
					{
						xmlHttpRequest = new ActiveXObject("Microsoft.XMLHTTP");
					}
					 else
					{
						xmlHttpRequest = new ActiveXObject("Msxml2.XMLHTTP." + i + ".0");
					}
					break;
				}
				catch(excNotLoadable)
				{
					xmlHttpRequest = false;
				}
			}
		}
		else if(window.XMLHttpRequest)
		{
			xmlHttpRequest = new XMLHttpRequest();
		}
	}
	catch(excNotLoadable)
	{
		xmlHttpRequest = false;
	}
	return xmlHttpRequest;
}

function sortRows(sort_id, order)
{
	var delrow = "";
	for (i = 1; i <= list.length; i++)
	{
			if (list[i] == true)
			{
				delrow += i + ",";
				list[i] = true;
			}
	}

	checked = false;
	document.getElementById("page").value = 1;
	var filter = document.getElementById("filter").value;
	document.getElementById("sort_id").value = sort_id;
	document.getElementById("sort_order").value = order;
	formId = document.getElementById("formId").value;
	limit = document.getElementById("limit").value;

	parameters = "selectedrow=" + delrow;
	xmlHttp=CreateXMLHttpRequest();
	xmlHttp.open("POST","index.php?option=com_rsform&task=submissions.edit&action=sort&formId=" + formId + "&filter=" + filter + "&sort_id=" + sort_id + "&order=" + order + "&limit=" + limit + "&page=" + 1,true);
	xmlHttp.onreadystatechange = function() {
		if (xmlHttp.readyState == 4) {
			document.getElementById('content').innerHTML = xmlHttp.responseText;
		}
	}
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.setRequestHeader("Content-length", parameters.length);
	xmlHttp.setRequestHeader("Connection", "close");
	xmlHttp.send(parameters);

	xmlHttp2=CreateXMLHttpRequest();
	xmlHttp2.open("GET","index.php?option=com_rsform&task=submissions.edit&action=pager&formId=" + formId + "&filter=" + filter + "&sort_id=" + sort_id + "&order=" + order + "&limit=" + limit + "&page=" + 1,true);
	xmlHttp2.onreadystatechange = function() {
		if (xmlHttp2.readyState == 4) {
			document.getElementById('pager').innerHTML = xmlHttp2.responseText;
		}
	}
	xmlHttp2.send(null);

}
function changeRows()
{
	var filter = document.getElementById("filter").value;
	sort_id = document.getElementById("sort_id").value;
	order = document.getElementById("sort_order").value;
	formId = document.getElementById("formId").value;
	limit = document.getElementById("limit").value;

	parameters = "limit=" + limit;
	xmlHttp=CreateXMLHttpRequest();
	xmlHttp.open("POST","index.php?option=com_rsform&task=submissions.edit&action=sort&formId=" + formId + "&filter=" + filter + "&sort_id=" + sort_id + "&order=" + order + "&limit=" + limit + "&page=" + 1,true);
	xmlHttp.onreadystatechange = function() {
		if (xmlHttp.readyState == 4) {
			document.getElementById('content').innerHTML = xmlHttp.responseText;
		}
	}
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.setRequestHeader("Content-length", parameters.length);
	xmlHttp.setRequestHeader("Connection", "close");
	xmlHttp.send(parameters);

	xmlHttp2=CreateXMLHttpRequest();
	xmlHttp2.open("GET","index.php?option=com_rsform&task=submissions.edit&action=pager&formId=" + formId + "&filter=" + filter + "&sort_id=" + sort_id + "&order=" + order + "&limit=" + limit + "&page=" + 1,true);
	xmlHttp2.onreadystatechange = function() {
		if (xmlHttp2.readyState == 4) {
			document.getElementById('pager').innerHTML = xmlHttp2.responseText;
		}
	}
	xmlHttp2.send(null);

}

function createFilter()
{
	var delrow = "";
	for (i = 0; i < list.length; i++)
	{
			if (list[i] == true)
			{
				delrow += i + ",";
				list[i] = true;
			}
	}

	checked = false;
	var sort_id = document.getElementById("sort_id").value;
	var order = document.getElementById("sort_order").value;
	var filter = document.getElementById("filter").value;
	var formId = document.getElementById("formId").value;
	var limit = document.getElementById("limit").value;

	parameters = "selectedrow=" + delrow;
	xmlHttp=CreateXMLHttpRequest();
	xmlHttp.open("POST","index.php?option=com_rsform&task=submissions.edit&action=filter&filter=" + filter + "&sort_id=" + sort_id + "&order=" + order + "&formId=" + formId + "&limit=" + limit + "&page=" + 1,true);
	xmlHttp.onreadystatechange = function() {
		if (xmlHttp.readyState == 4) {
			document.getElementById('content').innerHTML = xmlHttp.responseText;
		}
	}
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.setRequestHeader("Content-length", parameters.length);
	xmlHttp.setRequestHeader("Connection", "close");
	xmlHttp.send(parameters);

	document.getElementById("page").value = 1;

	xmlHttp2=CreateXMLHttpRequest();
	xmlHttp2.open("GET","index.php?option=com_rsform&task=submissions.edit&action=pager&formId=" + formId + "&filter=" + filter + "&sort_id=" + sort_id + "&order=" + order + "&limit=" + limit + "&page=" + 1,true);
	xmlHttp2.onreadystatechange = function() {
		if (xmlHttp2.readyState == 4) {
			document.getElementById('pager').innerHTML = xmlHttp2.responseText;
		}
	}
	xmlHttp2.send(null);
}

function clearFilter()
{
	checked = false;
	document.getElementById("filter").value = "";
	createFilter();
}

function editRow(id, total)
{
	for(var i=0; i < total; i = i +1)
	{
		value = document.getElementById('textarea-' + id + '-' + i).innerHTML;

		if (value.match(/\n/ig))
		{
			document.getElementById('row-' + id + '-' + i).innerHTML = '<textarea style="display: none;" id="hidden-' + id + '-' + i + '">' + rsform_htmlentities(value) + '</textarea><textarea id="input-' + id + '-' + i + '" name="textarea-' + id + '">' + rsform_htmlentities(value) + '</textarea>';
		}
		else
		{
			document.getElementById('row-' + id + '-' + i).innerHTML = '<input type="hidden" id="hidden-' + id + '-' + i + '" value="' + rsform_htmlentities(value) + '" /><input type="text" id="input-' + id + '-' + i + '" value="' + rsform_htmlentities(value) + '" name="textarea-' + id + '"/>';
		}
	}

	document.getElementById('act-' + id).innerHTML = '<input type="button" id="input-' + id + '-' + i + '" value="Save" onclick="saveRow(' + id + ', ' + total + ')"/>' + '<input type="button" id="input-' + id + '-' + i + '" value="Cancel" onclick="cancelRow(' + id + ', ' + total + ')"/>' + '<input type="button" id="input-' + id + '-' + i + '" value="Remove" onclick="removeRow(' + id + ')"/>';
}

function resendRow(id, thebutton)
{
	while(xmlHttpArray.length > 0) { xmlHttpArray.pop(); }

	thebutton.disabled = true;
	
	parameters = 'SubmissionId=' + id;
	
	xmlHttp=CreateXMLHttpRequest();
	xmlHttp.open("POST","index.php?option=com_rsform&task=submissions.resend&SubmissionId=" + id,true);
	xmlHttp.onreadystatechange = function() {
		if (xmlHttp.readyState == 4) {
			thebutton.value = 'Sent!';
		}
	}
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=utf-8");
	xmlHttp.setRequestHeader("Content-length", parameters.length);
	xmlHttp.setRequestHeader("Connection", "close");
	xmlHttp.send(parameters);
}

function cancelRow(sort_id, total)
{
	for(var i=0; i < total; i = i +1)
	{
		value = document.getElementById("textarea-" + sort_id + "-" + i).value;
		document.getElementById("row-" + sort_id + "-" + i).innerHTML = value;
	}

	document.getElementById("act-" + sort_id).innerHTML = "<input type='button' id='" + "input-" + sort_id + "-" + i + "' value='Edit' onclick='editRow(" + sort_id + ", " + total + ")'/>" + " <input type='button' id='" + "input-" + sort_id + "-" + i + "' value='Remove' />" + ' <input type="button" name="resend" onclick="resendRow(\'' + sort_id + '\', this)" value="Resend Emails" />';
}

function saveRow(id, total)
{
	while(xmlHttpArray.length > 0) { xmlHttpArray.pop(); }

	FieldValues = document.getElementsByName('textarea-' + id);
	FieldNames = document.getElementsByName('fieldName-' + id);
	SubmissionValueIds = document.getElementsByName('SubmissionValueId-'+id);

	var formId = document.getElementById("formId").value;
	
	j=0;
	for(i =0;i<SubmissionValueIds.length;i++)
	{

		value = FieldValues[i].value;
		//if(value){
			parameters = "value=" + urlencode(value);
			xmlHttpArray.push(CreateXMLHttpRequest());
			xmlHttpArray[j].open("POST","index.php?option=com_rsform&task=submissions.edit&action=edit&formId=" + formId + "&SubmissionId=" + id + "&SubmissionValueId=" + SubmissionValueIds[i].value + "&fieldName=" + FieldNames[i].value,true);
			/*xmlHttpArray[i].onreadystatechange = function() {
				onReadyState(i, value, total, id);
			};*/
			xmlHttpArray[j].setRequestHeader("Content-type", "application/x-www-form-urlencoded");
			xmlHttpArray[j].setRequestHeader("Content-length", parameters.length);
			xmlHttpArray[j].setRequestHeader("Connection", "close");
			xmlHttpArray[j].send(parameters);
			onReadyState(j, value, total, id);
			j++;
		//}
	}
}

function onReadyState(i, value, total, id) {
	//if (xmlHttpArray[i].readyState == 4) {				//no checking for successfull update is performed !
		document.getElementById("row-" + id + "-" + i).innerHTML = value;
		document.getElementById("textarea-" + id + "-" + i).innerHTML = value;
		if ((i + 1) == total) { _saveRow(id, total); }
	//}
}

function _saveRow(id, total)
{
	document.getElementById("act-" + id).innerHTML = "<input type='button' id='" + "input-" + id + "-" + i + "' value='Edit' onclick='editRow(" + id + ", " + total + ")'/>" + "<input type='button' id='" + "input-" + id + "-" + i + "' value='Remove' onclick='removeRow(" + id + ")'/>";
}

function changePage(page)
{
	var delrow = "";
	for (i = 0; i < list.length; i++)
	{
			if (list[i] == true)
			{
				delrow += i + ",";
				list[i] = true;
			}
	}

	checked = false;
	document.getElementById("page").value = page;
	var sort_id = document.getElementById("sort_id").value;
	var order = document.getElementById("sort_order").value;
	var filter = document.getElementById("filter").value;
	var formId = document.getElementById("formId").value;
	var limit = document.getElementById("limit").value;

	parameters = "selectedrow=" + delrow;
	xmlHttp=CreateXMLHttpRequest();
	xmlHttp.open("POST","index.php?option=com_rsform&task=submissions.edit&action=page&formId=" + formId + "&filter=" + filter + "&sort_id=" + sort_id + "&order=" + order + "&page=" + page + "&limit=" + limit,true);
	xmlHttp.onreadystatechange = function() {
		if (xmlHttp.readyState == 4) {
			document.getElementById('content').innerHTML = xmlHttp.responseText;
		}
	}
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=utf-8");
	xmlHttp.setRequestHeader("Content-length", parameters.length);
	xmlHttp.setRequestHeader("Connection", "close");
	xmlHttp.send(parameters);

	xmlHttp2=CreateXMLHttpRequest();
	xmlHttp2.open("GET","index.php?option=com_rsform&task=submissions.edit&action=pager&formId=" + formId + "&filter=" + filter + "&sort_id=" + sort_id + "&order=" + order + "&page=" + page + "&limit=" + limit,true);
	xmlHttp2.setRequestHeader("Content-type", "application/x-www-form-urlencoded; charset=utf-8");
	xmlHttp2.onreadystatechange = function() {
		if (xmlHttp2.readyState == 4) {
			document.getElementById('pager').innerHTML = xmlHttp2.responseText;
		}
	}
	xmlHttp2.send(null);
}

function removeRow(id)
{
	if (confirm("Are you sure you want to delete this record?"))
	{
		var delrow = "";
		for (i = 0; i < list.length; i++)
		{
				if (list[i] == true)
				{
					delrow += i + ",";
					list[i] = true;
				}
		}

		checked = false;
		var page = document.getElementById("page").value;
		var sort_id = document.getElementById("sort_id").value;
		var order = document.getElementById("sort_order").value;
		var filter = document.getElementById("filter").value;
		var formId = document.getElementById("formId").value;
		var limit = document.getElementById("limit").value;

		parameters = "selectedrow=" + delrow;
		xmlHttp=CreateXMLHttpRequest();
		xmlHttp.open("POST","index.php?option=com_rsform&task=submissions.edit&action=remove&filter=" + filter + "&sort_id=" + sort_id + "&order=" + order + "&page=" + page + "&id=" + id + "&limit=" + limit + "&formId=" + formId,true);
		xmlHttp.onreadystatechange = function() {
			if (xmlHttp.readyState == 4) {
				document.getElementById('content').innerHTML = xmlHttp.responseText;
			}
		}
		xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlHttp.setRequestHeader("Content-length", parameters.length);
		xmlHttp.setRequestHeader("Connection", "close");
		xmlHttp.send(parameters);

		xmlHttp2=CreateXMLHttpRequest();
		xmlHttp2.open("GET","index.php?option=com_rsform&task=submissions.edit&action=pager&filter=" + filter + "&sort_id=" + sort_id + "&order=" + order + "&page=" + page + "&limit=" + limit + "&formId=" + formId,true);
		xmlHttp2.onreadystatechange = function() {
			if (xmlHttp2.readyState == 4) {
				document.getElementById('pager').innerHTML = xmlHttp2.responseText;
			}
		}
		xmlHttp2.send(null);
	}
}

function urlencode(str)
{
	str = str.replace(/\+/g, '%2B');
	str = str.replace(/\%20/g, '+');
	str = str.replace(/\*/g, '%2A');
	str = str.replace(/\//g, '%2F');
	str = str.replace(/\@/g, '%40');
	return str;
}

function checkAll()
{
	field = document.getElementsByName('checks[]');
	if (checked == "true" )
	{
		checked = "false";
		for (i = 0; i < field.length; i++)
		{
			field[i].checked = false ;
			list[field[i].value] = false;
		}
	}
	else
	{
		checked = "true";
		for (i = 0; i < field.length; i++)
		{
			field[i].checked = true ;
			list[field[i].value] = true;
		}
	}
	if (checked == "true") {
		document.adminForm.boxchecked.value = field.length;
	} else {
		document.adminForm.boxchecked.value = 0;
	}

}

function checkOne(field)
{
	if (field.checked)
	{
		list[field.value] = true;
	}
	else
	{
		list[field.value] = false;
	}
}

function rsform_htmlentities(string)
{
	var newstring = string.replace(/&/,"&amp;");
	newstring = newstring.replace(/</,"&lt;");
	newstring = newstring.replace(/>/,"&gt;");
	newstring = newstring.replace(/'/,"&#39;");
	return newstring;
}