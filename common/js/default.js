$(document).ready(function() {
  $("#prevPhoto").click(function() {
    visibleEle = $("div.photo_box:visible");
    if(visibleEle.prev("div.photo_box").attr('id')) {
      visibleEle.hide();
      visibleEle.prev("div.photo_box").show('slide', {direction: 'right'}, 300);
    } else {
      visibleEle.hide();
      $("div.photo_box:last").show('slide', {direction: 'right'}, 300);
    }
    //alert(visibleEle.attr('id'));
    return false;
  });
  $("#nextPhoto").click(function() {
    visibleEle = $("div.photo_box:visible");
    if(visibleEle.next("div.photo_box").attr('id')) {
      visibleEle.hide();
      visibleEle.next("div.photo_box").show('slide', {direction: 'left'}, 300);
    } else {
      visibleEle.hide();
      $("div.photo_box:first").show('slide', {direction: 'left'}, 300);
    }
    //alert(visibleEle.attr('id'));
    return false;
  });
  $("#randomPhoto").click(function() {
    visibleEle = $("div.photo_box:visible");
    var imgBoxArr = $('div.photo_box');
    imgBoxArrLength = imgBoxArr.length;
    while(1) {
      randNum = Math.floor((Math.random() * imgBoxArrLength) + 1);
      if(imgBoxArr.eq(randNum-1).attr('id') != visibleEle.attr('id'))
        break;
    }
    // show image that is nth child of image array
    //alert(randNum);
    //alert(imgBoxArr.eq(randNum-1).attr('id'));
    if(imgBoxArr.eq(randNum-1).attr('id')) {
      visibleEle.hide();
      imgBoxArr.eq(randNum-1).show('clip');
    }
    //alert(visibleEle.attr('id'));
    return false;
  });
});
