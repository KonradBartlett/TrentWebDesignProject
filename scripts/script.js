"use strict"

$(document).ready(function() {

  // When delete icon is clicked, open a confirmation dialog
  $("#main-page .fa-trash-alt").click(function(ev) {
    var item = this;  // save which delete button was clicked
    ev.preventDefault();
    // Create a confirmation dialog
    var deleteDialog = $("<div>").dialog({
      modal: true,
      title: "Confirm deletion",
      buttons: [
        { // When delete is clicked, delete the movie entry
          text: "Delete",
          click: function() {
            var id = $(item).parent().parent().parent().attr("id"); // get the movie's id
            $.post("deletevid.php", { id: id })
            // then close dialog and refresh the page
            .done(function() {
              deleteDialog.dialog("destroy");
              location.reload();
            })
            .fail(function(jqXHR, textStatus, errorThrown) { console.log(jqXHR.responseText); });
          }
        },
        { // When the cancel button is pressed, close the dialog
          text: "Cancel",
          click: function() { deleteDialog.dialog("destroy"); }
        }
      ]
    });
  }); // Delete icon on click

  $("#main-page .fa-info-circle").click(function(ev) {
    var item = this;  // save which movie's details to display
    var id = $(item).parent().parent().parent().attr("id"); // get the movie's id
    ev.preventDefault();
    // Create a modal window with the movie details
    var detailsDialog = $("<div>").dialog({
      modal: true,
      title: "Details",
      width: 800,
      height: 600,
      create: function (event, ui) {},
      close: function () { $(this).remove(); location.reload(); },
      open: function (event, ui) {},
      buttons: [
        { // When close is clicked, destroy the dialog and refresh
          text: "Close",
          click: function() { detailsDialog.dialog("destroy"); location.reload(); }
        }
      ]
    })
    .load("modaldetails.php?id="+id); // load the appropriate movie data into the dialog
  }); // details icon on click

  // Check for existing username on account creation
  $("#account-page #username").blur(function(ev) {

    // remove warning message
    $("span:contains('already exists')").removeClass("error");
    $("span:contains('already exists')").addClass("noerror");
    let valid = true;

    // check for username already in database
    $.get("checkusername.php", { username: $("#username").val() } )
      .done(function(data) {
        if(data) {
          // display warning message
          $("span:contains('already exists')").addClass("error");
          $("span:contains('already exists')").removeClass("noerror");
          valid = false;
        }
      })
      .fail(function(jqXHR, textStatus, errorThrown) {
        $("main").prepend("<span class=\"error\">Error</span>");
      });
  }); // create account username on blur

  // Display password strength - using Strength.js plugin
  $("#account-page #password").strength();

  // Validate account information when an account is being created
  $("#account-page input[type='submit']").on("click", function(ev) {

    let valid = true;

    // Validate Name
    let name = $("#name").val();
    if(name == "")
    {
      // display warning
      $("#name+span").addClass("error");
      $("#name+span").removeClass("noerror");
      valid = false;
    } else {
      // hide warning
      $("#name+span").addClass("noerror");
      $("#name+span").removeClass("error");
    }

    // Validate email
    let email = $("#email").val();
    if(!emailIsValid(email) || email == "")
    {
      // display warning
      $("span:contains('valid email')").addClass("error");
      $("span:contains('valid email')").removeClass("noerror");
      valid = false;
    } else {
      // hide warning
      $("span:contains('valid email')").addClass("noerror");
      $("span:contains('valid email')").removeClass("error");
    }

    // Check password strength
    // if the password is very weak or weak, display an error message
    if($(".strength_meter").find(".weak").length != 0 || $(".strength_meter").find(".veryweak").length != 0) {
      $("#password-error").addClass("error");
      $("#password-error").removeClass("noerror");
      valid = false;
    } else {
      // hide the message
      $("#password-error").addClass("noerror");
      $("#password-error").removeClass("error");
    }

    if(!valid) {
      ev.preventDefault();
    }

  }); // account page submit on click

  // set datepicker format
  $.datepicker.setDefaults({
    dateFormat: "yy-mm-dd"
  });
  // set datepicker for theatre and dvd date inputs
  $("#theatre-release").datepicker();
  $("#dvd-release").datepicker();

  // Plot summary character counter
  $("#plot").keyup(function() {
    let charCount = $(this).val().length;
    $("#plot+span").text(2500 - charCount);
  });


  // On add video form submission
  $("#add-vid-page input[type='submit']").on("click", function(ev) {

    let valid = validateMovie();  // validate movie details using a function

    if(!valid) {
      ev.preventDefault();
    }
  }); // add video page submit on click

  // On edit video form submission
  $("#edit-page input[type='submit']").on("click", function(ev) {

    let valid = validateMovie();  // validate movie details using a function

    if(!valid) {
      ev.preventDefault();
    }
  }); // edit video page submit on click

}); // document ready


//regular expression check for valid email address
function emailIsValid (email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)
}


// regular expression check for valid date format
function validDate (date) {
  return /(^(\d{4}-(0[1-9]|1[0-2])-(0[1-9]|[12]\d|3[01]))$|(^$))/.test(date);
}

// Movie details validation function
function validateMovie() {

  let valid = true;

  // Get entered information from the form
  var title = $("#title").val();
  var mpaa = $("input[name='mpaa']:checked").val();
  var genre = []
  $.each($("#genre").children("option:selected"), function() {
    genre.push($(this).val());
  });
  var type = []
  $.each($("input[name='type[]']:checked"), function() {
    type.push($(this).val());
  });

  // if there is no title, display a warning message
  if(!title) {
    $("#title+span").addClass("error");
    $("#title+span").removeClass("noerror");
    valid = false;
  } else { // hide the message
    $("#title+span").addClass("noerror");
    $("#title+span").removeClass("error");
  }

  // if there is no mpaa selection, display a warning message
  if(!mpaa) {
    $("input[name='mpaa']").parent().siblings("div").addClass("error");
    $("input[name='mpaa']").parent().siblings("div").removeClass("noerror");
    valid = false;
  } else { // hide the message
    $("input[name='mpaa']").parent().siblings("div").addClass("noerror");
    $("input[name='mpaa']").parent().siblings("div").removeClass("error");
  }

  // if there are no genres selected, display a warning message
  if(genre.length == 0) {
    $("#genre+span").addClass("error");
    $("#genre+span").removeClass("noerror");
    valid = false;
  } else { // hide the message
    $("#genre+span").addClass("noerror");
    $("#genre+span").removeClass("error");
  }

  // if there is no movie type selected, display a warning message
  if(type.length == 0) {
    $("input[name='type[]']").parent().siblings("div").addClass("error");
    $("input[name='type[]']").parent().siblings("div").removeClass("noerror");
    valid = false;
  } else { // hide the message
    $("input[name='type[]']").parent().siblings("div").addClass("noerror");
    $("input[name='type[]']").parent().siblings("div").removeClass("error");
  }

  // Validate theatre date
  let theatreDate = $("#theatre-release").val();
  if(!validDate(theatreDate))
  {
    // display warning
    $("#theatre-release").siblings("span:contains('valid date')").addClass("error");
    $("#theatre-release").siblings("span:contains('valid date')").removeClass("noerror");
    valid = false;
  } else {
    // hide warning
    $("#theatre-release").siblings("span:contains('valid date')").addClass("noerror");
    $("#theatre-release").siblings("span:contains('valid date')").removeClass("error");
  }

  // Validate dvd date
  let dvdDate = $("#dvd-release").val();
  if(!validDate(dvdDate))
  {
    // display warning
    $("#dvd-release").siblings("span:contains('valid date')").addClass("error");
    $("#dvd-release").siblings("span:contains('valid date')").removeClass("noerror");
    valid = false;
  } else {
    // hide warning
    $("#dvd-release").siblings("span:contains('valid date')").addClass("noerror");
    $("#dvd-release").siblings("span:contains('valid date')").removeClass("error");
  }

  return valid;
}
