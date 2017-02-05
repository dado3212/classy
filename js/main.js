$(document).ready(function() {
  $('code#js').on('click', function() {
    var range = document.createRange();
    var selection = window.getSelection();
    range.selectNodeContents($(this)[0]);

    selection.removeAllRanges();
    selection.addRange(range);
  });

  $('form').submit(function(e) {
    e.preventDefault();

    $.post('php/search.php', {
      criteria: [
        {
          type: 'departments',
          value: ['COSC', 'MATH'],
          weight: 3,
        },
        {
          type: 'distributives',
          value: ['LIT', 'NW'],
          weight: 3,
        },
        {
          type: 'periods',
          value: ['10', '11', '2'],
          weight: 2,
        },
        {
          type: 'periods',
          value: ['2'],
          weight: 1,
        },
      ],
      sessid: $('form input[name="sessid"]').val(),
    }, function(data) {
      $('#output').html(data);
    });
  });
});