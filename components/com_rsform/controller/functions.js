function displayTemplate(componentTypeId,componentId)
{
	//&& typeof componentId== 'undefined'
	if (document.getElementById('componentEdit'+componentTypeId).innerHTML!="" )
	{
		document.getElementById('componentEdit'+componentTypeId).innerHTML="";
		return;
	}

	var stuff=document.getElementsByTagName("div");
	//var stuff=document.getElementById('formComponentEdit');
	//alert(stuff);
	//alert(stuff.length);
	for(i=0;i<stuff.length;i++)
	{
		//alert(stuff[i].name);
		if(stuff[i].title=="componentEdit")
		{
			//alert(stuff[i].name);
			stuff[i].innerHTML="";
		}
	}
	document.getElementById('state').innerHTML='Status: loading...';
	document.getElementById('state').style.color='rgb(255,0,0)';

	document.getElementById('componentIdToEdit').value=-1;
	xml=buildXmlHttp();
	xml.onreadystatechange=function()
    {
		if(xml.readyState==4)
		{
			document.getElementById('componentEdit'+componentTypeId).innerHTML=xml.responseText;
			//alert(xml.responseText);
			document.getElementById('state').innerHTML='Status: ok';
			document.getElementById('state').style.color='';
			changeValidation($('VALIDATIONRULE'));
		}
    }
	if(typeof componentId != 'undefined')
	{
		document.getElementById('componentIdToEdit').value=componentId;
		xml.open('GET','index.php?option=com_rsform&task=components.display&componentType=' + componentTypeId + '&componentId=' + componentId + '&&randomTime=' + Math.random(), true);
	}
	else
	{	xml.open('GET','index.php?option=com_rsform&task=components.display&componentType='+componentTypeId+'&randomTime='+Math.random(),true);
	}
	xml.send(null);
	document.getElementById('componentEdit'+componentTypeId).innerHTML='';

}
function changeStatusComponent(formId,componentId)
{
	document.getElementById('state').innerHTML='Status: loading...';
	document.getElementById('state').style.color='rgb(255,0,0)';
	xml=buildXmlHttp();
	xml.onreadystatechange=function()
    {
		if(xml.readyState==4)
		{
			var icon = document.getElementById('currentStatus' + componentId);
			if(icon.src.match('unpublish.png'))
			{
				icon.src = 'components/com_rsform/images/icons/publish.png';
			}
			else
			{
				icon.src = 'components/com_rsform/images/icons/unpublish.png';
			}
			if(document.getElementById('FormLayoutAutogenerate').checked==true) generateLayout(formId,'no');
			document.getElementById('state').innerHTML='Status: ok';
			document.getElementById('state').style.color='';
		}
    }
	xml.open('GET','index.php?option=com_rsform&task=components.changestatus&componentId='+componentId+'&randomTime='+Math.random(),true);
	xml.send(null);
}
function removeComponent(formId,componentId)
{
	document.getElementById('state').innerHTML='Status: loading...';
	document.getElementById('state').style.color='rgb(255,0,0)';
	xml=buildXmlHttp();
	xml.onreadystatechange=function()
    {
		if(xml.readyState==4)
		{
			var table=document.getElementById('componentPreview');
			var rows=document.getElementsByName('previewComponentId');
			for(i=0;i<rows.length;i++)
			{
				if(rows[i].value==componentId)
				{
					table.deleteRow(i);
				}
			}
			tidyOrder();
			if(document.getElementById('FormLayoutAutogenerate').checked==true) generateLayout(formId,'no');
			
			document.getElementById('state').innerHTML='Status: ok';
			document.getElementById('state').style.color='';
		}
    }
	xml.open('GET','index.php?option=com_rsform&task=components.remove&componentId='+componentId+'&formId='+formId+'&randomTime='+Math.random(),true);
	xml.send(null);
}
function moveComponentUp(formId,componentId)
{
	document.getElementById('state').innerHTML='Status: loading...';
	document.getElementById('state').style.color='rgb(255,0,0)';
	xml=buildXmlHttp();
	xml.open('GET','index.php?option=com_rsform&task=components.moveup&formId='+formId+'&componentId='+componentId+'&randomTime='+Math.random(),true);
	xml.send(null);
  	//initForm(formId);
	var table=document.getElementById('componentPreview');
	var rows=document.getElementsByName('previewComponentId');
	for(i=0;i<rows.length;i++)
	{
		if(rows[i].value==componentId && i>1)
		{
			for(j=0;j<table.rows[i].cells.length;j++)
			{
				var aux=table.rows[i-1].cells[j].innerHTML;

				table.rows[i-1].deleteCell(j);
				table.rows[i-1].insertCell(j);

				table.rows[i-1].cells[j].innerHTML=table.rows[i].cells[j].innerHTML;

				table.rows[i].deleteCell(j);
				table.rows[i].insertCell(j);

				table.rows[i].cells[j].innerHTML=aux;
			}
			tidyOrder();
		}
	}

	xml.onreadystatechange=function()
	{
		if(xml.readyState==4)
		{
			if(document.getElementById('FormLayoutAutogenerate').checked==true) generateLayout(formId,'no');
			document.getElementById('state').innerHTML='Status: ok';
			document.getElementById('state').style.color='';
		}
	}
}
function moveComponentDown(formId,componentId)
{
	document.getElementById('state').innerHTML='Status: loading...';
	document.getElementById('state').style.color='rgb(255,0,0)';
	xml=buildXmlHttp();
	xml.open('GET','index.php?option=com_rsform&task=components.movedown&formId='+formId+'&componentId='+componentId+'&randomTime='+Math.random(),true);
	xml.send(null);
	var table=document.getElementById('componentPreview');
	var rows=document.getElementsByName('previewComponentId');
	//table.style.height+=1;
	for(i=1;i<rows.length;i++)
	{
		if(rows[i].value==componentId && i<table.rows.length-1)
		{

			for(j=0;j<table.rows[i].cells.length;j++)
			{
				/*var aux=table.rows[i].cells[j].innerHTML;
				table.rows[i].insertCell(j);
				table.rows[i].cells[j].innerHTML=;
				*/
				var aux=table.rows[i].cells[j].innerHTML;
				table.rows[i].deleteCell(j);
				table.rows[i].insertCell(j);
				table.rows[i].cells[j].innerHTML=table.rows[i+1].cells[j].innerHTML;

				table.rows[i+1].deleteCell(j);
				table.rows[i+1].insertCell(j);
				table.rows[i+1].cells[j].innerHTML=aux;

			}
			tidyOrder();
			break;
		}
	}

	xml.onreadystatechange=function()
	{
		if(xml.readyState==4)
		{
			if(document.getElementById('FormLayoutAutogenerate').checked==true) generateLayout(formId,'no');
			document.getElementById('state').innerHTML='Status: ok';
			document.getElementById('state').style.color='';
		}
	}
}
function tidyOrder()
{
	document.getElementById('state').innerHTML='Status: loading...';
	document.getElementById('state').style.color='rgb(255,0,0)';
	var comps=document.getElementsByName('previewComponentId');

	for(i=1;i<document.getElementById('componentPreview').rows.length;i++)
	{
		//alert('ordering['+comps[i].value+']');
		var id='ordering['+comps[i].value+']';
		var el=document.getElementsByName(id);
		el[0].value=i;
	}
	document.getElementById('state').innerHTML='Status: ok';
	document.getElementById('state').style.color='';
}

function buildXmlHttp()
{
	var xmlHttp;
	try
	{
		xmlHttp=new XMLHttpRequest();
	}
	catch (e)
	{
		try
		{
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
		}
		catch (e)
		{
			try
			{
				xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
			}
			catch (e)
			{
				alert("Your browser does not support AJAX!");
				return false;
			}
		}
	}
	return xmlHttp;
}
function saveOrdering(formId)
{
	document.getElementById('state').innerHTML='Status: loading...';
	document.getElementById('state').style.color='rgb(255,0,0)';
	//alert(document.getElementById('formLayout').value);
	xml=buildXmlHttp();
	//alert(formId);
	var url = "web_services/saveLayout.php";
	var params = "formId="+formId+"&layout="+document.getElementById('formLayout').value;
	//alert(params);
	xml.open("POST",url,true);
	xml.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xml.setRequestHeader("Content-length", params.length);
	xml.setRequestHeader("Connection", "close");
	xml.send(params);
	xml.onreadystatechange=function()
	{
		if(xml.readyState==4)
		{
			document.getElementById('formComponentOrder').submit();
			document.getElementById('state').innerHTML='Status: ok';
			document.getElementById('state').style.color='';
		}
	}
}
function processComponent(componentType)
{
	document.getElementById('state').innerHTML='Status: loading...';
	document.getElementById('state').style.color='rgb(255,0,0)';
	xml=buildXmlHttp();
	var autoLayout=document.getElementById('FormLayoutAutogenerate');


	if(componentType!=9)
	{
		if(document.getElementById('componentIdToEdit').value==-1)
		xml.open('GET','index.php?option=com_rsform&task=components.validate.name&componentName='+document.getElementById('NAME').value+'&formId='+document.getElementById('formId').value+'&componentType='+componentType+'&randomTime='+Math.random(),true);
		else
		xml.open('GET','index.php?option=com_rsform&task=components.validate.name&componentName='+document.getElementById('NAME').value+'&formId='+document.getElementById('formId').value+'&currentComponentId='+document.getElementById('componentIdToEdit').value+'&componentType='+componentType+'&randomTime='+Math.random(),true);
	}

	else
	{

		if(document.getElementById('componentIdToEdit').value==-1)
		xml.open('GET','index.php?option=com_rsform&task=components.validate.name&componentName='+document.getElementById('NAME').value+'&formId='+document.getElementById('formId').value+'&componentType='+componentType+'&randomTime='+Math.random()+'&destination='+document.getElementById('DESTINATION').value,true);
		else
		xml.open('GET','index.php?option=com_rsform&task=components.validate.name&componentName='+document.getElementById('NAME').value+'&formId='+document.getElementById('formId').value+'&currentComponentId='+document.getElementById('componentIdToEdit').value+'&componentType='+componentType+'&randomTime='+Math.random()+'&destination='+document.getElementById('DESTINATION').value,true);
	}

	xml.send(null);
	xml.onreadystatechange=function()
    {
		if(xml.readyState==4)
		{
			//if(autoLayout.checked==true)
			//	alert('Autolayout is checked');
			if(xml.responseText.indexOf('Ok') == -1)
				alert(xml.responseText);
			else
				document.getElementById('formComponentEdit').submit();
			document.getElementById('state').innerHTML='Status: ok';
			document.getElementById('state').style.color='';
		}
    }
}

function changeFormAutoGenerateLayout(formId){
	document.getElementById('state').innerHTML='Status: loading...';
	document.getElementById('state').style.color='rgb(255,0,0)';
	var layouts=document.getElementsByName('formLayoutOption');
	var layoutName=''
	for(i=0;i<layouts.length;i++)
		if(layouts[i].checked) layoutName=layouts[i].value


	xml=buildXmlHttp();
	xml.onreadystatechange=function()
	{
		if(xml.readyState==4)
		{

//alert(document.getElementById('formLayout').readonly);
			if(document.getElementById('FormLayoutAutogenerate').checked==true)
			{
				document.getElementById('rsform_layout_msg').style.display = 'none';
				document.getElementById('formLayout').readOnly=true;
			}
			else
			{
				document.getElementById('rsform_layout_msg').style.display = '';
				document.getElementById('formLayout').readOnly=false;
			}

			document.getElementById('state').innerHTML='Status: ok';
			document.getElementById('state').style.color='';
		}
	}
	xml.open('GET','index.php?option=com_rsform&task=forms.changeAutoGenerateLayout&formId='+formId+'&randomTime='+Math.random()+'&formLayoutName='+layoutName,true);
	xml.send(null);
}

function generateLayout(formId,alert)
{
	document.getElementById('state').innerHTML='Status: loading...';
	document.getElementById('state').style.color='rgb(255,0,0)';
	if(alert!='no')
	{
		var answer=confirm("Pressing the 'Generate layout' button will ERASE your current layout. Are you sure you want to continue?");
		if(answer==false) return;
	}
	var layoutName=document.getElementById('formLayoutStyle').value;

	/*if(layoutName!='Custom')
		document.getElementById('formLayout').readOnly=true;
	else
	{
		document.getElementById('formLayout').readOnly=false;
		return;
	}
	*/
	xml=buildXmlHttp();
	xml.onreadystatechange=function()
	{
		if(xml.readyState==4)
		{
			var text=xml.responseText;
			text.replace();
			document.getElementById('formLayout').value=xml.responseText;
			document.getElementById('state').innerHTML='Status: ok';
			document.getElementById('state').style.color='';
		}
	}
	xml.open('GET','index.php?option=com_rsform&task=layouts.generate&layout='+layoutName+'&formId='+formId+'&randomTime='+Math.random(),true);
	xml.send(null);
}

function saveLayoutName(formId,layoutName)
{
	document.getElementById('formLayoutStyle').value = layoutName;
	document.getElementById('state').innerHTML='Status: loading...';
	document.getElementById('state').style.color='rgb(255,0,0)';
	xml=buildXmlHttp();
	xml.open('GET','index.php?option=com_rsform&task=layouts.saveLayoutName&formId='+formId+'&randomTime='+Math.random()+'&formLayoutName='+layoutName,true);
	xml.send(null);
	xml.onreadystatechange=function()
	{
		if(xml.readyState==4)
		{
			if(document.getElementById('FormLayoutAutogenerate').checked==true)generateLayout(formId);
			document.getElementById('state').innerHTML='Status: ok';
			document.getElementById('state').style.color='';
		}
	}
	
	
}

function checkUpdates(session, revision){
	xml=buildXmlHttp();
	xml.open('GET','index.php',true);
	xml.send(null);
	xml.onreadystatechange=function()
	{
		if(xml.readyState==4)
		{
			checkUpdates = document.getElementById('checkUpdates');
			checkUpdates.innerHTML='No New Updates';
			checkUpdates.style.className = 'none';
		}
	}
}
 
function refreshCaptcha(componentId, captchaPath){
	if(!captchaPath) captchaPath = 'index.php?option=com_rsform&task=captcha&componentId=' + componentId;
	document.getElementById('captcha' + componentId).src = captchaPath + '&' + Math.random();
	document.getElementById('captchaTxt' + componentId).value='';
	document.getElementById('captchaTxt' + componentId).focus();

}


function submitbutton(pressbutton) {
	if(isset('tinyMCE')) tinyMCE.execCommand('mceFocus', false,'Thankyou');
	if(isset('tinyMCE')) if(tinyMCE.getInstanceById('UserEmailText')!=null) tinyMCE.execCommand('mceFocus', false,'UserEmailText');
	if(isset('tinyMCE')) if(tinyMCE.getInstanceById('AdminEmailText')!=null) tinyMCE.execCommand('mceFocus', false,'AdminEmailText');
	if(pressbutton!='false')submitform(pressbutton);
}



function componentsCopy(formId){
	loc = 'index.php?option=com_rsform&task=components.copy.screen&formId=' + formId;
	checks = document.getElementsByName('checks[]');
	for(i=0;i<checks.length;i++){
		if(checks[i].checked) loc += '&checks[]=' + checks[i].value;
	}
	document.location = loc;
}

function isset(varname)  {
  if(typeof( window[ varname ] ) != "undefined") return true;
  else return false;
}




//MAPPINGS//

function updateColumns()
{
	//alert(document.getElementById('rsform_mapping_table'));
	var currentTable=document.getElementById('rsform_mapping_table').value;
	//options[document.getElementById('rsform_mapping_table').selectedIndex].value;
	//alert(currentTable);
	
	var xmlHttp;
	try
	{
		xmlHttp=new XMLHttpRequest();
	}
	catch (e)
	{
	try
		{
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
	    }catch (e)
	    {
	    try
	      {
	      xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
	      }
	    catch (e)
	      {
	      alert("Your browser does not support AJAX!");
	      return false;
	      }
	    }
	  }
	
	xmlHttp.onreadystatechange=function()
	{
		if(xmlHttp.readyState==4)
		{
			document.getElementById('rsform_html_mapping_column').innerHTML=xmlHttp.responseText;
			//document.myForm.time.value=xmlHttp.responseText;
		}
	}
	xmlHttp.open("GET","index.php?option=com_rsform&task=plugin&plugin_task=mappings.getColumns&tableName="+currentTable,true);
	xmlHttp.send(null);
}
function saveMapping(formId)
{
	var currentTable=document.getElementById('rsform_mapping_table')./*options[document.getElementById('rsform_mapping_table').selectedIndex].*/value;
	var currentColumn=document.getElementById('rsform_mapping_column')./*options[document.getElementById('rsform_mapping_column').selectedIndex].*/value;
	//var currentComponent=document.getElementById('rsform_mapping_component').options[document.getElementById('rsform_mapping_component').selectedIndex].value;
	var currentComponent=document.getElementById('rsform_mapping_component').value;
	var xmlHttp;
	try
	{
		xmlHttp=new XMLHttpRequest();
	}
	catch (e)
	{
	try
		{
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
	    }catch (e)
	    {
	    try
	      {
	      xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
	      }
	    catch (e)
	      {
	      alert("Your browser does not support AJAX!");
	      return false;
	      }
	    }
	  }
	
	xmlHttp.onreadystatechange=function()
	{
		if(xmlHttp.readyState==4)
		{
			if(xmlHttp.responseText=='1')
			{
				alert("You can't add the same mapping twice");
				return;
			}
			document.getElementById('rsform_html_mappings_table').innerHTML=xmlHttp.responseText;
			//document.myForm.time.value=xmlHttp.responseText;
		}
	}
		xmlHttp.open("GET","index.php?option=com_rsform&task=plugin&plugin_task=mappings.saveMapping&ComponentId="+currentComponent+"&MappingTable="+currentTable+"&MappingColumn="+currentColumn+"&FormId="+formId,true);
	xmlHttp.send(null);
	
}
function deleteMapping(mappingId,formId)
{
	var xmlHttp;
	try
	{
		xmlHttp=new XMLHttpRequest();
	}
	catch (e)
	{
	try
		{
			xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
	    }catch (e)
	    {
	    try
	      {
	      xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
	      }
	    catch (e)
	      {
	      alert("Your browser does not support AJAX!");
	      return false;
	      }
	    }
	  }
	
	xmlHttp.onreadystatechange=function()
	{
		if(xmlHttp.readyState==4)
		{
			if(xmlHttp.responseText=='1')
			{
				alert("You can't add the same mapping twice");
				return;
			}
			document.getElementById('rsform_html_mappings_table').innerHTML=xmlHttp.responseText;
			//document.myForm.time.value=xmlHttp.responseText;
		}
	}
	xmlHttp.open("GET","index.php?option=com_rsform&task=plugin&plugin_task=mappings.deleteMapping&MappingId="+mappingId+"&FormId="+formId,true);
	xmlHttp.send(null);
}

function exportProcess(start, limit, total){
	xml=buildXmlHttp();
	xml.onreadystatechange=function()
    {
		if(xml.readyState==4)
		{
			post = xml.responseText;
			if(post=='END')
			{
				document.getElementById('progressBar').style.width = document.getElementById('progressBar').innerHTML = '100%';
				document.location = 'index.php?option=com_rsform&task=submissions.export.file&ExportFile=' + document.getElementById('ExportFile').value;
			}
			else
			{
				document.getElementById('progressBar').style.width = Math.ceil(start*100/total) + '%';
				document.getElementById('progressBar').innerHTML = Math.ceil(start*100/total) + '%';
				start = start + limit;
				exportProcess(start, limit, total);
			}
		}
    }
		
	xml.open('GET','index.php?option=com_rsform&task=submissions.export.process&start=' + start + '&limit=' + limit + '&randomTime=' + Math.random(),true);
	xml.send(null);
}

function number_format( number, decimals, dec_point, thousands_sep ) { 
    var n = number, prec = decimals;
    n = !isFinite(+n) ? 0 : +n;
    prec = !isFinite(+prec) ? 0 : Math.abs(prec);
    var sep = (typeof thousands_sep == "undefined") ? ',' : thousands_sep;
    var dec = (typeof dec_point == "undefined") ? '.' : dec_point;
 
    var s = (prec > 0) ? n.toFixed(prec) : Math.round(n).toFixed(prec); //fix for IE parseFloat(0.55).toFixed(0) = 0;
 
    var abs = Math.abs(n).toFixed(prec);
    var _, i;
 
    if (abs >= 1000) {
        _ = abs.split(/\D/);
        i = _[0].length % 3 || 3;
 
        _[0] = s.slice(0,i + (n < 0)) +
              _[0].slice(i).replace(/(\d{3})/g, sep+'$1');
 
        s = _.join(dec);
    } else {
        s = s.replace('.', dec);
    }
 
    return s;
}

function changeValidation(elem){
	if(elem == null) return;
	if(elem.id == 'VALIDATIONRULE') 
		if(elem.value == 'custom' || elem.value == 'numeric' || elem.value == 'alphanumeric' || elem.value == 'alpha' )
			document.getElementById('idVALIDATIONEXTRA').className='showVALIDATIONEXTRA';
		else
			document.getElementById('idVALIDATIONEXTRA').className='hideVALIDATIONEXTRA';
}
