function show_review(clicked_id) {
  // show the review
  var to_show = ".r" + clicked_id.substring(1);
  $(to_show).removeClass('delete');

  // hide the pencil icon
  var pencil_hide = "#" + clicked_id;
  $(pencil_hide).addClass('delete');

  // show the x icon
  var x_show = "#x" + clicked_id.substring(1);
  $(x_show).removeClass('delete');
};

function hide_review(clicked_id) {
  var to_hide = ".r" + clicked_id.substring(1);
  $(to_hide).addClass('delete');

  // hide the x icon
  var x_hide = "#" + clicked_id;
  $(x_hide).addClass('delete');

  // show the pencil icon
  var pencil_show = "#w" + clicked_id.substring(1);
  $(pencil_show).removeClass('delete');
};
