
<div id="rt-mainbody">
<div id="toolbar-box"></div>

<div id="jevents" >
<form action="/index.php?option=com_jevents&Itemid=111" method="post" name="adminForm" enctype='multipart/form-data'>
<input type="hidden" name="jevtype" value="" />
<div style='width:500px;'></div>
<input type="hidden" name="rp_id" value="0" />
<input type="hidden" name="year" value="2010" />
<input type="hidden" name="month" value="11" />
<input type="hidden" name="day" value="23" />
<input type="hidden" name="state" id="state" value="1" />
<input type="hidden" name="evid" id="evid" value="0" />
<input type="hidden" name="valid_dates" id="valid_dates" value="1"  />

<div class="adminform" align="left">
  <table cellpadding="5" cellspacing="2" border="0"  class="adminform" id="jevadminform">
    <tr>
          <td align="left">Event Name</td>
            <td>
              <input type="text" name="title" size="50" maxlength="255" value="" />
            </td>
                        <td colspan="2">
              <input type="hidden" name="priority" value="0" />

            </td>
                </tr>
        <tr>
    <td colspan='4'><input type="hidden" name="ics_id" value="2" /></td>    </tr>
        <tr>
                      <td valign="top" align="left">Type of Event</td>
            <td style="width:200px" >
            <select name="catid" id="catid">
              <option value="0" >Choose Category</option>
              <?php foreach($param as $v) { ?>
                <option value="<?php echo $v['id']; ?>" ><?php echo $v['title']; ?></option>
              <?php } ?>
            </select>            </td>

                        <td align="left" class="accesslevel"></td>
            <td class="accesslevel"></td>
                </tr>
    <tr><td valign="top" align="left" colspan="4">   <div style="clear:both;">
    <fieldset class="jev_sed"><legend>Start, End, Duration</legend>
    <span>
    <span >All day Event or Unspecified time</span>

    <span><input type="checkbox" id='allDayEvent' name='allDayEvent'  onclick="toggleAllDayEvent();" />
    </span>
    </span>
    <!--- removing 12 hour checkbox on form --->
  <span style="margin:20px" class='checkbox12h'>
    <!--- <span style="font-weight:bold"></span> --->
        <!--- changed the type from checkbox to hidden --->
    <span><input type="hidden" id='view12Hour' name='view12Hour' checked='checked' onclick="toggleView12Hour();" value="1"/></span>
    
  </span>

    <div>
        <fieldset><legend>Start date</legend>
        <div style="float:left">
      <input type="text" name="publish_up" id="publish_up" value="2010-11-23" maxlength="10" onchange="checkDates(this);fixRepeatDates();" size="12"  />         </div>
         <div style="float:left;margin-left:20px!important;">
            Start Time&nbsp;      <span id="start_24h_area" style="display:inline">
            <input class="inputbox" type="text" name="start_time" id="start_time" size="8"  maxlength="8" value="21:30" onchange="checkTime(this);"/>

      </span>
      <span id="start_12h_area" style="display:inline">
            <input type="text" name="start_12h" id="start_12h" size="8" maxlength="8"  value="" onchange="check12hTime(this);" />
          <input type="radio" name="start_ampm" id="startAM" value="none" checked="checked" onclick="toggleAMPM('startAM');"  />am          <input type="radio" name="start_ampm" id="startPM" value="none" onclick="toggleAMPM('startPM');"  />pm      </span>
         </div>
         </fieldset>
     </div>
    <div>

        <fieldset><legend>End date</legend>
        <div style="float:left">
        <input type="text" name="publish_down" id="publish_down" value="2010-11-23" maxlength="10" onchange="checkDates(this);" size="12"  />         </div>
         <div style="float:left;margin-left:20px!important">
             End Time&nbsp;     <span id="end_24h_area" style="display:inline">
            <input class="inputbox" type="text" name="end_time" id="end_time" size="8" maxlength="8"  value="23:00" onchange="checkTime(this);" />
      </span>

      <span id="end_12h_area" style="display:inline">
            <input type="text" name="end_12h" id="end_12h" size="8" maxlength="8"  value="" onchange="check12hTime(this);" />
          <input type="radio" name="end_ampm" id="endAM" value="none" checked="checked" onclick="toggleAMPM('endAM');"  />am          <input type="radio" name="end_ampm" id="endPM" value="none" onclick="toggleAMPM('endPM');"  />pm      </span>
        <span style="margin-left:10px">
        <span><input type="checkbox" id='noendtime' name='noendtime'  onclick="toggleNoEndTime();" value="1" />
        <span >No specific end time</span>
        </span>

        </span>
         </div>
         </fieldset>
     </div>
    <div id="jevmultiday" style="display:none">
        <fieldset><legend>Multi Day Event Treatment</legend>
            Should this multi day event appear on each day of event?&nbsp;          <input type="radio" name="multiday" value="1" checked="checked"  onclick="updateRepeatWarning();" />Yes         <input type="radio" name="multiday" value="0"   onclick="updateRepeatWarning();" />No         </fieldset>

     </div>
     </fieldset>
     </div>
     <div >
   <!-- REPEAT FREQ -->
     <div style="clear:both;">
    <fieldset><legend>Repeat type</legend>
        <table border="0" cellspacing="2">

          <tr>                                  
            <td ><input type="radio" name="freq" id="NONE" value="none" checked="checked" onclick="toggleFreq('NONE');" /><label for='NONE'>No Repeat</label></td>
            <td ><input type="radio" name="freq" id="DAILY" value="DAILY" onclick="toggleFreq('DAILY');" /><label for='DAILY'>Daily</label></td>
            <td ><input type="radio" name="freq" id="WEEKLY" value="WEEKLY" onclick="toggleFreq('WEEKLY');" /><label for='WEEKLY'>Weekly</label></td>
            <td ><input type="radio" name="freq" id="MONTHLY" value="MONTHLY" onclick="toggleFreq('MONTHLY');" /><label for='MONTHLY'>Monthly</label></td>
            <td ><input type="radio" name="freq" id="YEARLY" value="YEARLY" onclick="toggleFreq('YEARLY');" /><label for='YEARLY'>Yearly</label></td>
            </tr>

    </table>
        </fieldset>
  </div>      
   <!-- END REPEAT FREQ-->
   <div style="clear:both;display:none" id="interval_div">
      <div style="float:left">
      <fieldset><legend>Repeat Interval</legend>
            <input class="inputbox" type="text" name="rinterval" id="rinterval" size="2" maxlength="2" value="1" onchange="checkInterval();" /><span id='interval_label' style="margin-left:1em"></span>
      </fieldset>

      </div>
      <div style="float:left;margin-left:20px!important"  id="cu_count" >
      <fieldset><legend><input type="radio" name="countuntil" value="count" id="cuc" checked="checked" onclick="toggleCountUntil('cu_count');" />Repeat Count</legend>
            <input class="inputbox" type="text" name="count" id="count" size="3" maxlength="3" value="1" onchange="checkInterval();" /><span id='count_label' style="margin-left:1em">repeats</span>
      </fieldset>
      </div>
      <div style="float:left;margin-left:20px!important;" id="cu_until">
      <fieldset style="background-color:#dddddd"><legend><input type="radio" name="countuntil" value="until" id="cuu" onclick="toggleCountUntil('cu_until');" />Repeat Until</legend>

      <input type="text" name="until" id="until" value="2010-11-23" maxlength="10" onchange="checkUntil();updateRepeatWarning();" size="12"  />
      </fieldset>
      </div>
   </div>
   <div style="clear:both;">
   <div  style="float:left;display:none;margin-right:1em;" id="byyearday">
      <fieldset><legend><input type="radio" name="whichby" id="jevbyd" value="byd"  onclick="toggleWhichBy('byyearday');" />By Year Day</legend>
        Comma separated list            <input class="inputbox" type="text" name="byyearday" size="20" maxlength="50" value="327" onchange="checkInterval();" />

        <br/>Count back from year end<input type="checkbox" name="byd_direction"  onclick="fixRepeatDates();" />
      </fieldset>
   </div>
   <div  style="float:left;display:none;margin-right:1em;" id="bymonth">
      <fieldset><legend><input type="radio" name="whichby"  id="jevbm" value="bm"  onclick="toggleWhichBy('bymonth');" />By Month</legend>
        Comma separated list            <input class="inputbox" type="text" name="bymonth" size="30" maxlength="20" value="11" onchange="checkInterval();" />
        </fieldset>

   </div>
   <div  style="float:left;display:none;margin-right:1em;" id="byweekno">
      <fieldset><legend><input type="radio" name="whichby"  id="jevbwn" value="bwn"  onclick="toggleWhichBy('byweekno');" />By Week Num</legend>
        Comma separated list            <input class="inputbox" type="text" name="byweekno" size="20" maxlength="20" value="47" onchange="checkInterval();" />
        <br/>Count back from year end<input type="checkbox" name="bwn_direction"   />
        </fieldset>
   </div>

   <div  style="float:left;display:none;margin-right:1em;" id="bymonthday">
      <fieldset><legend><input type="radio" name="whichby"  id="jevbmd" value="bmd"  onclick="toggleWhichBy('bymonthday');" />By Month Day</legend>
        Comma separated list            <input class="inputbox" type="text" name="bymonthday" size="30" maxlength="20" value="23" onchange="checkInterval();" />
        <br/>Count back from month end<input type="checkbox" name="bmd_direction"  onclick="fixRepeatDates();"  />
        </fieldset>
   </div>
   <div  style="float:left;display:none;margin-right:1em;" id="byday">

      <fieldset><legend><input type="radio" name="whichby"  id="jevbd" value="bd"  onclick="toggleWhichBy('byday');" />By Day</legend>
            <span  class="r1" ><input type="checkbox" id="cb_wd0" name="weekdays[]" value="0"  onclick="updateRepeatWarning();" />&nbsp;
<label for="cb_wd0"><span class="sunday">S</span></label></span>
<span  class="r2" ><input type="checkbox" id="cb_wd1" name="weekdays[]" value="1"  onclick="updateRepeatWarning();" />&nbsp;
<label for="cb_wd1">M</label></span>
<span  class="r1" ><input type="checkbox" id="cb_wd2" name="weekdays[]" value="2"  checked="checked" onclick="updateRepeatWarning();" />&nbsp;
<label for="cb_wd2">T</label></span>
<span  class="r2" ><input type="checkbox" id="cb_wd3" name="weekdays[]" value="3"  onclick="updateRepeatWarning();" />&nbsp;
<label for="cb_wd3">W</label></span>
<span  class="r1" ><input type="checkbox" id="cb_wd4" name="weekdays[]" value="4"  onclick="updateRepeatWarning();" />&nbsp;
<label for="cb_wd4">T</label></span>

<span  class="r2" ><input type="checkbox" id="cb_wd5" name="weekdays[]" value="5"  onclick="updateRepeatWarning();" />&nbsp;
<label for="cb_wd5">F</label></span>
<span  class="r1" ><input type="checkbox" id="cb_wd6" name="weekdays[]" value="6"  onclick="updateRepeatWarning();" />&nbsp;
<label for="cb_wd6"><span class="saturday">S</span></label></span>
            <div id="weekofmonth">
        <span  class="r2" ><input type="checkbox" id="cb_wn1" name="weeknums[]" value="1"   onclick="updateRepeatWarning();" />&nbsp;
<label for="cb_wn1">week 1 </label></span>
<span  class="r1" ><input type="checkbox" id="cb_wn2" name="weeknums[]" value="2"   onclick="updateRepeatWarning();" />&nbsp;
<label for="cb_wn2">week 2 </label></span>
<span  class="r2" ><input type="checkbox" id="cb_wn3" name="weeknums[]" value="3"   onclick="updateRepeatWarning();" />&nbsp;
<label for="cb_wn3">week 3 </label></span>

<span  class="r1" ><input type="checkbox" id="cb_wn4" name="weeknums[]" value="4"   onclick="updateRepeatWarning();" />&nbsp;
<label for="cb_wn4">week 4 </label></span>
<span  class="r2" ><input type="checkbox" id="cb_wn5" name="weeknums[]" value="5"   onclick="updateRepeatWarning();" />&nbsp;
<label for="cb_wn5">week 5 </label></span>
        <br/>Count back from month end<input type="checkbox" name="bd_direction"   onclick="updateRepeatWarning();"/>
            </div>
      </fieldset>
   </div>
   <div  style="float:left;display:none;margin-right:1em;" id="bysetpos">

      <fieldset><legend>NOT YET SUPPORTED</legend>
      </fieldset>
   </div>
   </div>
   <div style="clear:both;"></div>
</div>
<script type="text/javascript" language="Javascript">
// make the correct frequency visible
function setupRepeats(){
  }
//if (window.attachEvent) window.attachEvent("onload",setupRepeats);
//else window.onload=setupRepeats;
//setupRepeats();
window.setTimeout("setupRepeats()", 500);
// move to 12h fields
set12hTime(document.adminForm.start_time);
set12hTime(document.adminForm.end_time);
// toggle unvisible time fields
toggleView12Hour();
</script>
</td></tr>
         <tr>

          <td valign="middle" align="left">
            Event Description </td>
            <td colspan="3">
            <div id='jeveditor'><textarea id="jevcontent" name="jevcontent" cols="70" rows="10" style="width:100%;height:125px;" class="mceEditor"></textarea>
</div>            </td>
         </tr>
         <tr>
          <td width="130" align="left">Event Location</td>

            <td colspan="3">
            <input type="hidden" name="location" id="locn" value=""/><input type="text" name="evlocation_notused" disabled="disabled" id="evlocation" value=" -- " style="float:left"/><div class="button2-left"><div class="blank"><a href="javascript:selectLocation('' ,'/indexiphone.php?option=com_jevlocations&amp;task=locations.select&amp;tmpl=component','750','500')" title="Select Location" style="text-decoration:none;">select</a></div></div>&nbsp;&nbsp;&nbsp;<div class="button2-left"><div class="blank"><a href="javascript:removeLocation();" title="Remove Location" style="text-decoration:none;">remove</a></div></div><div style='font-size:9px;'></div>
            </td>
         </tr>
         <tr>
            <td align="left">Event Contact</td>

            <td colspan="3">
            <input type="text" name="contact_info" size="50" maxlength="120" value="" /><br />
            <div style='font-size:9px;'>(email address, phone number and/or website where people can find more information)</div>
            </td>
          </tr>
          <!--- adding row for ext under contact info field --->
          <!--- <tr>
            <td align="left"></td>
            <td colspan="3">(email address, phone number and/or website where people can find more information)
            </td>
          </tr> --->
          
          <!---extra info fields --->

<!--        <tr>
            <td align="left" valign="top"></td>
            <td colspan="3">
              <textarea class="text_area" name="extra_info" id="extra_info" cols="50" rows="4" wrap="virtual" ></textarea>
            </td>
        </tr> -->
                  <tr>
          <td valign="top"  width="130" align="left">Select Person</td>
            <td colspan="3"><div style='float: left;'><ul id='sortablePeople' style='margin:0px;'></ul><select multiple="multiple" name="custom_person[]" id="custom_person" size="4" style="display:none" ></select></div><div class="button2-left"><div class="blank"><a href="javascript:sortablePeople.selectPerson('/index.php?option=com_jevpeople&amp;task=people.select&amp;tmpl=component');" title="Select a Person::After adding entries you an reorder entries by dragging and dropping them within the select box."  class="hasTip" style="text-decoration:none;">Select</a></div></div><img src='/administrator/images/publish_x.png' class='sortabletrash' id='trashimage' style='display:none;padding-right:2px;'/><script type='text/javascript'>
      sortablePeople.setup();
      var jevpeople = {
duplicateWarning : 'Already Selected'
    }
      </script></td>
         </tr>
                   <tr>
          <td valign="top"  width="130" align="left">Your name</td>

            <td colspan="3"><input size="50" type="text" name="custom_anonusername" id="custom_anonusername" value="" /></td>
         </tr>
                   <tr>
          <td valign="top"  width="130" align="left">Your email address</td>
            <td colspan="3"><input size="50" type="text" name="custom_anonemail" id="custom_anonemail" value="" /></td>
         </tr>
      <tr>
        <td valign="top" width="130" align="left">
          <input type="hidden" name="custom_field4" value="0" checked="checked" />
        </td>
        <td colspan="3"><input type="submit" name="submit" value="Submit" /></td>
      </tr>
    </table>

  </div>
<input type="hidden" name="boxchecked" value="0" />
<input type="hidden" name="updaterepeats" value="0"/>
<input type="hidden" name="task" value="icalevent.edit" />
<input type="hidden" name="option" value="com_jevents" />
</form>
</div>
</div>

</div>

