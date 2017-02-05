// Handles adding new criteria
function addCriteria() {
  var newRow =  
    '<div class="row">' + 
      '<div class="col-md-3">' + 
        '<div class="form-group">' + 
          '<label>Type</label>' + 
          '<select name="type" class="form-control">' + 
            '<option value="departments">Departments</option>' + 
            '<option value="distributives">Distributives</option>' + 
            '<option value="periods">Periods</option>' + 
          '</select>' + 
        '</div>' + 
      '</div>' + 
      '<div class="col-md-6">' + 
        '<div class="form-group">' + 
          '<label>Choices</label>' + 
          '<select name="choices[]" class="form-control" multiple>' +
            departmentOptions + 
          '</select>' + 
        '</div>' + 
      '</div>' + 
      '<div class="col-md-2">' + 
        '<div class="form-group">' + 
          '<label>Weight</label>' + 
          '<input name="weight" class="form-control" type="number" min="0" placeholder="3">' + 
        '</div>' + 
      '</div>' + 
      '<div class="col-md-1">' + 
        '<div class="form-group">' + 
          '<label>&nbsp;</label>' + 
          '<button type="button" class="btn btn-danger">🗑️</button>' + 
        '</div>' + 
      '</div>' + 
    '</div>';

  var rowElem = $.parseHTML(newRow);
  $('#criteria').append(rowElem);

  $(rowElem).find('select').chosen({});

  // Add a listener to update the choices
  $(rowElem).find('select[name="type"]').on('change', function() {
    var selected = $(this).val();
    if (selected == "departments") {
      $(rowElem).find('select[name^="choices"]').html($.parseHTML(departmentOptions));
    } else if (selected == "distributives") {
      $(rowElem).find('select[name^="choices"]').html($.parseHTML(distributiveOptions));
    } else if (selected == "periods") {
      $(rowElem).find('select[name^="choices"]').html($.parseHTML(periodOptions));
    }
    $(rowElem).find('select[name^="choices"]').trigger("chosen:updated");
  });
}

$(document).ready(function() {
  // Handles click to select on the javascript code
  $('code#js').on('click', function() {
    var range = document.createRange();
    var selection = window.getSelection();
    range.selectNodeContents($(this)[0]);

    selection.removeAllRanges();
    selection.addRange(range);
  });

  // Handles submitting the form
  $('form').submit(function(e) {
    e.preventDefault();

    console.log($(this).serialize());

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