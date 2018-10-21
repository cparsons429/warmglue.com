var email_to_create = 4;
var occupation_to_create = 3;

var visible_emails = 4;
var visible_occupations = 3;

const MAX_EMAILS = 10;
const MAX_OCCUPATIONS = 50;

function delete_this(clicked_id) {
  // delete the elements from view
  var class_to_delete = "." + clicked_id.substring(1);
  $(class_to_delete).addClass('delete');
  var elements = document.getElementsByClassName(class_to_delete.substring(1));

  // clear the values in these elements
  for each (element in elements) {
    element.value = '';
  }

  // decrement the number of visible elements
  if (clicked_id.substring(0, 1) == "e") {
    visible_emails--;
  }
  else {
    visible_occupations--;
  }

  // if we just decremented away from MAX_EMAILS or MAX_OCCUPATIONS, show the plus again
  if (visible_emails == MAX_EMAILS - 1 && clicked_id.substring(0, 1) == "e") {
    document.getElementById("plus-email").removeClass('delete');
  }
  else if (){
    document.getElementById("plus-occupation").removeClass('delete');
  }
};

function add_email() {
  var email_digit0 = Math.floor(email_to_create / 10);
  var email_digit1 = email_to_create % 10;
  var email_to_reveal = ".e" + email_digit0.toString() + email_digit1.toString();
  $(email_to_reveal).removeClass('delete');
  email_to_create++;
  visible_emails++;

  // we don't want to show more than MAX_EMAILS, so delete the plus when we reach this limit
  if (visible_emails == MAX_EMAILS) {
    document.getElementById("plus-email").addClass('delete');
  }
};

function add_occupation() {
  var occupation_digit0 = Math.floor(occupation_to_create / 10);
  var occupation_digit1 = occupation_to_create % 10;
  var occupation_to_reveal = ".o" + occupation_digit0.toString() + occupation_digit1.toString();
  $(occupation_to_reveal).removeClass('delete');
  occupation_to_create++;
  visible_occupations++;
};
