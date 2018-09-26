// when we scroll from the top, make the explanation disappear, remove the buttons from the body, put buttons in the navbar
$(document).ready(function() {
  $(window).scroll(function() {
    // assuming the 1em = 16px standard
    if($(this).scrollTop() > (.7 * document.documentElement.clientHeight - 14 * 16)) {
      // making nav elements visible
      $('.nav-log-in-div').removeClass('disappear');
      $('.nav-sign-up-div').removeClass('disappear');
      $('.nav-log-in-div').addClass('appear');
      $('.nav-sign-up-div').addClass('appear');
      // making body elements invisible
      $('.explanation').removeClass('appear');
      $('.body-log-in-div').removeClass('appear');
      $('.body-sign-up-div').removeClass('appear');
      $('.explanation').addClass('disappear');
      $('.body-log-in-div').addClass('disappear');
      $('.body-sign-up-div').addClass('disappear');
      // enabling nav links
      $('.nav-log-in-link').removeClass('disable-link');
      $('.nav-sign-up-link').removeClass('disable-link');
      $('.nav-log-in-link').addClass('enable-link');
      $('.nav-sign-up-link').addClass('enable-link');
      // disabling body links
      $('.body-log-in-link').removeClass('enable-link');
      $('.body-sign-up-link').removeClass('enable-link');
      $('.body-log-in-link').addClass('disable-link');
      $('.body-sign-up-link').addClass('disable-link');
    } else {
      // making body elements visible
      $('.explanation').removeClass('disappear');
      $('.body-log-in-div').removeClass('disappear');
      $('.body-sign-up-div').removeClass('disappear');
      $('.explanation').addClass('appear');
      $('.body-log-in-div').addClass('appear');
      $('.body-sign-up-div').addClass('appear');
      // making nav elements invisible
      $('.nav-log-in-div').removeClass('appear');
      $('.nav-sign-up-div').removeClass('appear');
      $('.nav-log-in-div').addClass('disappear');
      $('.nav-sign-up-div').addClass('disappear');
      // enabling body links
      $('.body-log-in-link').removeClass('disable-link');
      $('.body-sign-up-link').removeClass('disable-link');
      $('.body-log-in-link').addClass('enable-link');
      $('.body-sign-up-link').addClass('enable-link');
      // disabling nav links
      $('.nav-log-in-link').removeClass('enable-link');
      $('.nav-sign-up-link').removeClass('enable-link');
      $('.nav-log-in-link').addClass('disable-link');
      $('.nav-sign-up-link').addClass('disable-link');
    }
  });
});

// when we scroll to the middle, make the in-depth appear. when we're not in the middle, make it disappear
$(document).ready(function() {
  $(window).scroll(function() {
    // assuming the 1em = 16px standard
    if(
      $(this).scrollTop() > (.45 * document.documentElement.clientWidth - 14 * 16) &&
      $(this).scrollTop() < (.45 * document.documentElement.clientWidth + 30 * 16)
    ) {
      // making in-depth visible
      $('.in-depth').removeClass('disappear');
      $('.in-depth').addClass('appear');
    } else {
      // making in-depth invisible
      $('.in-depth').removeClass('appear');
      $('.in-depth').addClass('disappear');
    }
  });
});
