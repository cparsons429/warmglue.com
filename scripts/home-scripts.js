function get_first_visible_intro() {
  return parseInt($('.intro-text').not('.delete').first().attr('id').substring(5), 10);
};

function get_max_visible_intros() {
  if (window.innerWidth > 1200) {
    return Math.floor(($('.intro-list').height() - 2 * 16) / ((1.1 + 1.1) * 16));
  } else {
    return Math.floor(($('.intro-list').height() + 6 * 16) / ((1.1 + 1.5) * 16));
  }
};

function get_total_intros() {
  return $('.intro-text').length;
};

function show_intros(first_visible_intro, max_visible_intros, total_intros) {
  // show intros from first visible intro to min(first_visible_intro + max_visible_intros, total_intros)
  if ($('.intro-text').length != 0) {
    // only proceed if there are actually intros to show
    if (total_intros > max_visible_intros) {
      // only proceed if there are more total intros than the max visible intros
      var intro_to_show;
      var class_to_show;
      var i;

      for (i = 0; i < first_visible_intro; i++) {
        // hide the following intros
        intro_to_hide = "#intro" + String(i);
        class_to_hide = ".i" + String(i);
        review_to_hide = "#r" + String(i);
        id_to_hide = "x" + String(i);

        if (!($(intro_to_hide).hasClass('delete'))) {
          // if the element isn't already hidden, hide it and its icons
          $(class_to_hide).addClass('delete');
        }

        if (!($(review_to_hide).hasClass('delete'))) {
          // if the element's review isn't already hidden, hide it
          total_hide_review(id_to_hide);
        }
      }

      for (i = first_visible_intro; i < Math.min(first_visible_intro + max_visible_intros, total_intros); i++) {
        // show the maximum possible intros after the first visible intro
        intro_to_show = "#intro" + String(i);
        class_to_show = ".i" + String(i);

        if ($(intro_to_show).hasClass('delete')) {
          // if the element isn't already shown, show it and its write icon
          $(class_to_show).not('.x-right').removeClass('delete');
        }
      }

      var intro_to_hide;
      var class_to_hide;
      var review_to_hide;
      var id_to_hide;

      for (i = first_visible_intro + max_visible_intros; i < total_intros; i++) {
        // hide the following intros
        intro_to_hide = "#intro" + String(i);
        class_to_hide = ".i" + String(i);
        review_to_hide = "#r" + String(i);
        id_to_hide = "x" + String(i);

        if (!($(intro_to_hide).hasClass('delete'))) {
          // if the element isn't already hidden, hide it and its icons
          $(class_to_hide).addClass('delete');
        }

        if (!($(review_to_hide).hasClass('delete'))) {
          // if the element's review isn't already hidden, hide it
          total_hide_review(id_to_hide);
        }
      }

      if (first_visible_intro > 0) {
        // if there are intros with lower id than the first visible intro, don't grey out the tab-left button
        $('.page-left-grey').addClass('delete');
        $('.page-left-red').removeClass('delete');
      } else {
        // otherwise, make sure the tab-left button is greyed out
        $('.page-left-grey').removeClass('delete');
        $('.page-left-red').addClass('delete');
      }

      if (first_visible_intro + max_visible_intros < total_intros) {
        // if there are intros with higher id than the final visible intro, don't grey out the tab-right button
        $('.page-right-grey').addClass('delete');
        $('.page-right-red').removeClass('delete');
      } else {
        // otherwise, make sure the tab-right button is greyed out
        $('.page-right-grey').removeClass('delete');
        $('.page-right-red').addClass('delete');
      }
    }
  }
};

function display_correct_intros() {
  // we need to display the correct intros, hiding the others until the user tabs to them
  var first = get_first_visible_intro();
  var max = get_max_visible_intros();
  var total = get_total_intros();

  show_intros(first, max, total);
};

function tab_left() {
  // we show the updated intros after tabbing over the appropriate amount
  var first = get_first_visible_intro();
  var max = get_max_visible_intros();
  var total = get_total_intros();

  var new_first = Math.max(first - max, 0);

  show_intros(new_first, max, total);
};

function tab_right() {
  // we show the updated intros after tabbing over the appropriate amount
  var first = get_first_visible_intro();
  var max = get_max_visible_intros();
  var total = get_total_intros();

  var new_first = Math.min(first + max, total);

  show_intros(new_first, max, total);
};

window.onload = display_correct_intros;
window.onresize = display_correct_intros;

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

function total_hide_review(clicked_id) {
  var to_hide = "#r" + clicked_id.substring(1);
  $(to_hide).addClass('delete');

  // hide the x icon
  var x_hide = "#" + clicked_id;
  $(x_hide).addClass('delete');

  // hide even the pencil icon (this is for tabbing over)
  var pencil_show = "#w" + clicked_id.substring(1);
  $(pencil_show).addClass('delete');
}

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

function scroll_and_first_save(i) {
  // keeping track of scroll height for backend redirect
  $("#scroll_top" + String(i)).val($(this).scrollTop());
  $("#first" + String(i)).val(get_first_visible_intro());
};

function unhide() {
  // make body visible after running all scripts
  $('body').removeClass('delete');
}
