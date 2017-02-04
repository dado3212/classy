$(document).ready(function() {
  $('form').submit(function(e) {
    e.preventDefault();

    $.post('php/getClasses.php', $(this).serialize(), function(data) {
      console.log(data);
    });
  });
});