var email_to_create = 4;
var occupation_to_create = 3;

function delete_this(clicked_id) {
  class_to_delete = "." + clicked_id.substring(1);
  $(class_to_delete).addClass('delete');
};

function add_email() {
  var email_digit0 = Math.floor(email_to_create / 10);
  var email_digit1 = email_to_create % 10;
  var email_to_reveal = ".e" + email_digit0.toString() + email_digit1.toString();
  $(email_to_reveal).removeClass('delete');
  email_to_create++;
};

function add_occupation() {
  var occupation_digit0 = Math.floor(occupation_to_create / 10);
  var occupation_digit1 = occupation_to_create % 10;
  var occupation_to_reveal = ".o" + occupation_digit0.toString() + occupation_digit1.toString();
  $(occupation_to_reveal).removeClass('delete');
  occupation_to_create++;
};
