
 // JEvents Language Srings
var handm = 'Hours and minutes must be separated by a \':\', \'-\', \'.\' or \',\'';
var invalidtime = 'Invalid Time';
var invalidcorrected = 'invalid date has been corrected - please check';
var jevyears= 'years';
var jevmonths= 'months';
var jevweeks= 'weeks';
var jevdays= 'days';
 // end JEvents Language Srings

//Varialble for current host url - Yogi
var currentHostUrl = window.location.host;

window.addEvent('domready', function(){ var JTooltips = new Tips($$('.hasTip'), { maxTitleChars: 50, fixed: false}); });
window.addEvent('domready', function() {

  SqueezeBox.initialize({});

  $$('a.modal').each(function(el) {
    el.addEvent('click', function(e) {
      new Event(e).stop();
      SqueezeBox.fromElement(el);
    });
  });
});
var JevrRequiredFields = {
  fields: new Array(),
  verify:function (form){
    var messages =  new Array();
    valid = true;
    JevrRequiredFields.fields.each(function (item,i) {
      name = item.name;
      var matches = new Array();
      $A(form.elements).each (function (testitem,testi) {
        if(testitem.name == name){
          matches.push(testitem);
        };
      });
      var value = "";
      if(matches.length==1){
        value = matches[0].value;
      }
      // A set of radio checkboxes
      else if (matches.length>1){
        matches.each (function (match, index){
          if (match.checked) value = match.value; 
        });       
      }
      //if (elem) elem.value = item.value;
      if (value == item['default'] || value == ""){
        valid = false;
        // TODO add message together 
        if(item.reqmsg!=""){
          messages.push(item.reqmsg);
        }
      }
    });
    if (!valid){
      message = "";
      messages.each (function (msg, index){message += msg+"\n";});
      alert(message); 
    }
    return valid;
  }
}
// Disabled for now
/*
window.addEvent("domready",function(){
  var form =document.adminForm;
  if (form){    
    $(form).addEvent('submit',function(event){if (!JevrRequiredFields.verify(form)) {event = new Event(event); event.stop();}});
    //JevrRequiredFields
  };
});
*/


// category conditional fields
var JevrCategoryFields = {
  fields: [],
  cats: {"39":["39"],"40":["40"],"150":["150"],"42":["42"],"43":["43"],"66":["66"],"0":[],"151":[0]},
  setup:function (){
    if (!$('catid')) return;
    var catid = $('catid').value;
    var cats = this.cats[catid];

    // These are the ancestors of this cat
    this.fields.each(function (item,i) {
      var elem = $(document).getElement(".jevplugin_customfield_"+item.name);
      // This is the version that ignores parent category selections
      /*
      // only show it if the selected category is in the list
      if ($A(item.catids).contains(catid)){
        elem.style.display="table-row";
      }
      else {        
        elem.style.display="none";
      }
      */
      // hide the item by default
      elem.style.display="none";
      $A(cats).each (function(cat,i){
        if ($A(item.catids).contains(cat)){
          //alert("matched "+cat + " cf "+item.catids);
          elem.style.display="table-row";
        }
      });

    });
  }
};
window.addEvent("load",function(){
  if (JevrCategoryFields){
    JevrCategoryFields.setup();
    $('catid').addEvent('change',function(){
      JevrCategoryFields.setup();
    });
    if (!$('ics_id')) return;
    $('ics_id').addEvent('change',function(){
      setTimeout("JevrCategoryFields.setup()",500);
    });
  }
});
JevrRequiredFields.fields.push({'name':'custom_field4', 'default' :'0' ,'reqmsg':'This is required'}); 
    window.addEvent('domready', function(){ $$('dl.tabs').each(function(tabs){ new JTabs(tabs, {}); }); });
    tinyMCE.init({
      doctype: "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">",
      mode: "textareas",
      editor_selector: "mceEditor",
      language: "en",
      directionality: "ltr",
      theme: "none",
      invalid_elements: "table,li,ul,h1,h2,span,div,strong,applet,iframe,script,style",
      plugins: "contextmenu,browser,inlinepopups,media,safari,spellchecker,code,cleanup,tabfocus,paste",
      //document_base_url: "http://tapdestin.com/",
      document_base_url: "http://"+currentHostUrl+"/",
      site_url: "/",
      theme_advanced_toolbar_location: "top",
      theme_advanced_path: true,
      theme_advanced_statusbar_location: "bottom",
      theme_advanced_resizing: true,
      theme_advanced_resize_horizontal: true,
      theme_advanced_resizing_use_cookie: true,
      theme_advanced_blockformats: "p,h3,h4",
      removeformat_selector: "*",
      theme_advanced_fonts: "Andale Mono=andale mono,times;Arial=arial,helvetica,sans-serif;Arial Black=arial black,avant garde;Book Antiqua=book antiqua,palatino;Comic Sans MS=comic sans ms,sans-serif;Courier New=courier new,courier;Georgia=georgia,palatino;Helvetica=helvetica;Impact=impact,chicago;Symbol=symbol;Tahoma=tahoma,arial,helvetica,sans-serif;Terminal=terminal,monaco;Times New Roman=times new roman,times;Trebuchet MS=trebuchet ms,geneva;Verdana=verdana,geneva;Webdings=webdings;Wingdings=wingdings,zapf dingbats",
      theme_advanced_font_sizes: "8pt,10pt,12pt,14pt,18pt,24pt,36pt",
      theme_advanced_buttons1: "bold,italic,formatselect,removeformat,link,undo,redo,image,spellchecker,cut,copy,paste",
      theme_advanced_buttons2: "",
      theme_advanced_buttons3: "",
      verify_html: false,
      plugin_preview_width: 750,
      plugin_preview_height: 550,
      table_inline_editing: true,
      fix_list_elements: true,
      fix_table_elements: true,
      entity_encoding: "raw",
      content_css: "/templates/rt_quantive_j15/css/template.css",
      forced_root_block: false,
      force_br_newlines: false,
      force_p_newlines: true,
      inlinepopups_skin: "clearlooks2",
      file_browser_callback: function(name, url, type, win){tinyMCE.activeEditor.plugins.browser.browse(name, url, type, win);},
      spellchecker_languages: "+English=en",
      code_php: false,
      code_javascript: false,
      code_css: false,
      tabfocus_elements: ":prev,:next"
    });
        var fieldpublish_up=false;
        window.addEvent('domready', function() {
        if (fieldpublish_up) return;
        fieldpublish_up=true;
        new NewCalendar(
          { publish_up :  "Y-m-d"},
          {
          direction:0, 
          classes: ["dashboard"],
          draggable:true,
          navigation:2,
          tweak:{x:0,y:-75},
          offset:1,
          range:{min:2009,max:2015},
          readonly:1,
          months:["January",
          "February",
          "March",
          "April",
          "May",
          "June",
          "July",
          "August",
          "September",
          "October",
          "November",
          "December"
          ],
          days :["Sunday",
          "Monday",
          "Tuesday",
          "Wednesday",
          "Thursday",
          "Friday",
          "Saturday"
          ]
          ,
          onHideStart : function () { var elem = $("publish_up");checkDates(elem);fixRepeatDates();; },
          onHideComplete :function () { checkDates(this);fixRepeatDates();; }}
        );
      });
        var fieldpublish_down=false;
        window.addEvent('domready', function() {
        if (fieldpublish_down) return;
        fieldpublish_down=true;
        new NewCalendar(
          { publish_down :  "Y-m-d"},
          {
          direction:0, 
          classes: ["dashboard"],
          draggable:true,
          navigation:2,
          tweak:{x:0,y:-75},
          offset:1,
          range:{min:2009,max:2015},
          readonly:1,
          months:["January",
          "February",
          "March",
          "April",
          "May",
          "June",
          "July",
          "August",
          "September",
          "October",
          "November",
          "December"
          ],
          days :["Sunday",
          "Monday",
          "Tuesday",
          "Wednesday",
          "Thursday",
          "Friday",
          "Saturday"
          ]
          ,
          onHideStart : function () { var elem = $("publish_down");checkDates(elem);; },
          onHideComplete :function () { checkDates(this);; }}
        );
      });
        var fielduntil=false;
        window.addEvent('domready', function() {
        if (fielduntil) return;
        fielduntil=true;
        new NewCalendar(
          { until :  "Y-m-d"},
          {
          direction:0, 
          classes: ["dashboard"],
          draggable:true,
          navigation:2,
          tweak:{x:0,y:-75},
          offset:1,
          range:{min:2009,max:2015},
          readonly:1,
          months:["January",
          "February",
          "March",
          "April",
          "May",
          "June",
          "July",
          "August",
          "September",
          "October",
          "November",
          "December"
          ],
          days :["Sunday",
          "Monday",
          "Tuesday",
          "Wednesday",
          "Thursday",
          "Friday",
          "Saturday"
          ]
          ,
          onHideStart : function () { updateRepeatWarning();; },
          onHideComplete :function () { checkUntil();updateRepeatWarning();; }}
        );
      });
    window.addEvent('domready', function() {

      SqueezeBox.initialize({});

      $$('a.modal-button').each(function(el) {
        el.addEvent('click', function(e) {
          new Event(e).stop();
          SqueezeBox.fromElement(el);
        });
      });
    });
function jInsertEditorText(text,editor){JContentEditor.insert(editor,text);}
var rokboxPath = '/plugins/system/rokbox/';
window.addEvent('domready', function() {
  var modules = ['rt-block'];
  var header = ['h3','h2','h1'];
  GantryBuildSpans(modules, header);
});
window.addEvent('load', function() {
  new Fusion('ul.menutop', {
    pill: 0,
    effect: 'slide and fade',
    opacity: 1,
    hideDelay: 500,
    centered: 0,
    tweakInitial: {'x': -2, 'y': 0},
        tweakSubsequent: {'x': 0, 'y': -14},
    menuFx: {duration: 200, transition: Fx.Transitions.Sine.easeOut},
    pillFx: {duration: 400, transition: Fx.Transitions.Back.easeOut}
  });
});
window.addEvent((window.webkit) ? 'load' : 'domready', function() {
  window.rokajaxsearch = new RokAjaxSearch({
    'results': ' Results',
    'close': '',
    'websearch': 0,
    'blogsearch': 0,
    'imagesearch': 0,
    'videosearch': 0,
    'imagesize': 'SMALL',
    'safesearch': 'OFF',
    'search': ' Search...',
    'readmore': ' Read more...',
    'noresults': ' No results',
    'advsearch': ' Advanced search',
    'page': ' Page',
    'page_of': ' of',
    //'searchlink': 'http://tapdestin.com/index.php?option=com_search&amp;view=search&amp;tmpl=component',
    'searchlink': 'http://'+currentHostUrl+'/index.php?option=com_search&amp;view=search&amp;tmpl=component',
    //'advsearchlink': 'http://tapdestin.com/index.php?option=com_search&amp;view=search',
    'advsearchlink': 'http://'+currentHostUrl+'/index.php?option=com_search&amp;view=search',
    //'uribase': 'http://tapdestin.com/',
    'uribase': 'http://'+currentHostUrl+'/',
    'limit': '10',
    'perpage': '3',
    'ordering': 'newest',
    'phrase': 'any',
    'hidedivs': '',
    'includelink': 1,
    'viewall': ' View all results',
    'estimated': ' estimated',
    'showestimated': 1,
    'showpagination': 1,
    'showcategory': 1,
    'showreadmore': 1,
    'showdescription': 1
  });
});

//var jax_live_site = 'http://tapdestin.com/index.php';
var jax_live_site = 'http://'+currentHostUrl+'/index.php';
var jax_site_type = '1.5';

function submitbutton(pressbutton) {
  if (pressbutton.substr(0, 6) == 'cancel' || !(pressbutton == 'icalevent.save' || pressbutton == 'icalrepeat.save'  || pressbutton == 'icalevent.apply'  || pressbutton == 'icalrepeat.apply')) {
    if (document.adminForm['catid']){
      // restore catid to input value
      document.adminForm['catid'].value=0;
      document.adminForm['catid'].disabled=true;
    }
    submitform( pressbutton );
    return;
  }
  var form = document.adminForm;
  JContentEditor.getContent('jevcontent');  // do field validation
  if (form.title.value == "") {
    alert ( "Title cannot be blank" );
  }
  else if (form.ics_id.value == "0"){
    alert( "MISSING ICAL SELECTION" );
  }
  else if (form.valid_dates.value =="0"){
    alert( "Invalid dates - please correct");
  }
  else {
    // sets the date for the page after save
    resetYMD();
    submitform(pressbutton);
  }
}
