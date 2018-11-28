function hide_review(clicked_id) {
  var to_hide = "#r" + clicked_id.substring(1);
  $(to_hide).addClass('delete');

  // hide the x icon
  var x_hide = "#" + clicked_id;
  $(x_hide).addClass('delete');

  // show the pencil icon
  var pencil_show = "#w" + clicked_id.substring(1);
  $(pencil_show).removeClass('delete');
};

function show_review(clicked_id) {
  // hide other visible review, if it exists
  if ($('.review').not('.delete').length != 0) {
    var to_hide = $('.review').not('.delete').first().attr('id');
    hide_review("x" + to_hide.substring(1));
  }

  // show the review
  var to_show = "#r" + clicked_id.substring(1);
  $(to_show).removeClass('delete');

  // hide the pencil icon
  var pencil_hide = "#" + clicked_id;
  $(pencil_hide).addClass('delete');

  // show the x icon
  var x_show = "#x" + clicked_id.substring(1);
  $(x_show).removeClass('delete');
};

function scroll_save(i) {
  // keeping track of scroll height for backend redirect
  $("#scroll_top" + String(i)).val($(this).scrollTop());
};
