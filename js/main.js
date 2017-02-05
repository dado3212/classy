$(document).ready(function() {
  $('form').submit(function(e) {
    e.preventDefault();

    $.post('php/search.php', {
      depts: [['COSC', 'MATH']],
      distribs: [['LIT', 'NW']],
      periods: [['10','11','2'],['12']],
      overall: {
        depts: 3,
        distribs: 0,
        periods: 1,
      },
      sessid: $('form input[name="sessid"]').val(),
    }, function(data) {
      console.log(data);
    });
  });
});