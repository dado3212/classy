// Handles adding new criteria
function addCriteria() {
  var newRow =  
    '<div class="row criteria">' + 
      '<div class="col-md-3">' + 
        '<div class="form-group">' + 
          '<label>Type</label>' + 
          '<select name="type" class="form-control" required>' + 
            '<option value="departments">Departments</option>' + 
            '<option value="distributives">Distributives</option>' + 
            '<option value="periods">Periods</option>' + 
          '</select>' + 
        '</div>' + 
      '</div>' + 
      '<div class="col-md-6">' + 
        '<div class="form-group">' + 
          '<label>Choices</label>' + 
          '<select name="choices" class="form-control" multiple required>' +
            departmentOptions + 
          '</select>' + 
        '</div>' + 
      '</div>' + 
      '<div class="col-md-2">' + 
        '<div class="form-group">' + 
          '<label>Weight</label>' + 
          '<input name="weight" class="form-control" type="number" min="0" placeholder="3" required>' + 
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

  $(rowElem).find('select').chosen({
    search_contains: true,
  });

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

  // Add a listener to delete the row
  $(rowElem).find('button').on('click', function() {
    $(rowElem).remove();
  });
}

$(document).ready(function() {
  // Handle Chosen.js override to support 'required'
  $.fn.oldChosen = $.fn.chosen;
  $.fn.chosen = function(options) {
    var select = $(this)
      , is_creating_chosen = !!options

    if (is_creating_chosen && select.css('position') === 'absolute') {
      // if we are creating a chosen and the select already has the appropriate styles added
      // we remove those (so that the select hasn't got a crazy width), then create the chosen
      // then we re-add them later
      select.removeAttr('style')
    }

    var ret = select.oldChosen(options)

    // only act if the select has display: none, otherwise chosen is unsupported (iPhone, etc)
    if (is_creating_chosen && select.css('display') === 'none') {
      // https://github.com/harvesthq/chosen/issues/515#issuecomment-33214050
      // only do this if we are initializing chosen (no params, or object params) not calling a method
      select.attr('style','display:visible; position:absolute; clip:rect(0,0,0,0); height:34px;');
      select.attr('tabindex', -1);
    }
    return ret;
  };

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

    var criteria = [];
    var rawCriteria = $('.row.criteria');

    for (var i = 0; i < rawCriteria.length; i++) {
      criteria.push({
        type: $(rawCriteria[i]).find('select[name="type"]').val(),
        value: $(rawCriteria[i]).find('select[name="choices"]').val(),
        weight: $(rawCriteria[i]).find('input[name="weight"]').val(),
      });
    }

    $.post('php/search.php', {
      criteria: criteria,
      sessid: $('form input[name="sessid"]').val(),
    }, function(data) {
      $('#output').html(data);
    });
  });
});