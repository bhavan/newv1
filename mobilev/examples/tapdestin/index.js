

$(document).ready(function(){
  /* $.ajax({
    url: "http://www.tapdestin.com/m.index.php",
    data: { tab: "events" },
    success: function(d) {
      // alert(d);
      _html = d;
    }
  });
  $.get("http://www.tapdestin.com/m.index.php", function(d){
    _html = d;
    alert(d);
  });
  $("iframe.container-iframe object").attr("width", "180px");
  $("iframe.container-iframe object").attr("height", "120px");
  $("iframe.container-iframe embed").attr("width", "180px");
  $("iframe.container-iframe embed").attr("height", "120px"); */
}); 


Ext.setup({
  icon: 'icon.png',
  glossOnIcon: false,
  tabletStartupScreen: 'tablet_startup.png',
  phoneStartupScreen: 'phone_startup.png',
  onReady: function() {
    var places = new Ext.Component({
      title: 'Hello',
      cls: 'card2',
      scroll: 'vertical',
      tpl: [
        '<table>',
          '<tpl for=".">',
            '<tr>',
              '<td>{title}<td>',
              '<td>{street}</td>',
              '<td>{phone}</td>',
            '</tr>',
          '</tpl>',
        '</table>'
      ]
    });
    // [{"loc_id":"51","title":"Bob's Pizza Shack","street":"555 Whatever Lane","phone":"(850) 555-1112","loccat":"150"},{"loc_id":"49","title":"Mellow Mushroom","street":"981 Highway 98","phone":"850-583-1368","loccat":"147"}]
    Ext.util.JSONP.request({
      url: 'http://www.tapdestin.com/m.index.php',
      callbackKey: 'callback',
      params: {
        tab: 'places'
      },
      callback: function(data) {
        data = data.results;
        // Update the tweets in timeline
        timeline.update(data);
      }
    });
    new Ext.TabPanel({
      fullscreen: true,
      sortable: true,
      items: [{
        title: 'Events',
        html: '<iframe class="container-iframe" src="http://www.tapdestin.com/m.index.php"></iframe>',
        cls: 'card1'
      }, places, {
        title: 'Photos',
        html: '<iframe class="container-iframe" src="http://www.tapdestin.com/m.index.php?tab=photos"></iframe>',
        cls: 'card3'
      }, {
        title: 'Videos',
        html: '<iframe class="container-iframe" src="http://www.tapdestin.com/m.index.php?tab=videos"></iframe>',
        cls: 'card4'
      }]
    });
  }
  
});


