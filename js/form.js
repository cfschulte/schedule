/************

  form.js -  Thu Aug 29 10:43:37 CDT 2019

*************/

// Use this for undoing changes.
var previousAjaxDBVal;

////////////////////////////////
// CHECK THE UNDO STATUS. Enable the undo button if there changes
$(document).ready(function() {
    var db_data = {};
    db_data['id'] = $('form.ajax_form input[name=id]').val();
    db_data['table'] = $('form.ajax_form input[name=table]').val();
    db_data['is_new'] = $('form.ajax_form input[name=is_new]').val();
    
    console.log(db_data);
    
    if(db_data['is_new'] == "0"){
        // do an ajax check
        $.ajax({
        url:'/schedule/ajax_parser.php',
        method: 'POST',
        dataType: 'json',
            data: {
                id: 'form_needs_undo',
                data: db_data
            }
        }).done(function(json_response){
            console.log(json_response);
            if(json_response > 0) {
                $("#undo_button").prop('disabled', false); 
            } else {
                $("#undo_button").prop('disabled', true); 
            }
        });
     } else { 
        $("#undo_button").prop('disabled', true); 
     }
});


$(document).ready(function(){
////////////////////////////////
// INIT AJAX  with all the stuff that every ajax call has 
    $.ajaxSetup({
        url:'/schedule/ajax_parser.php',
        method: 'POST',
        dataType: 'json',
        error: function(xhr, status, errorThrown) {
//            alert( "There has been a problem." );
            console.log( "Error: " + errorThrown );
            console.log( "Status: " + status );
            console.dir( xhr );
        }
    });

////////////////////////////////
// SAVE the state of the previous value on any element clicked.
    $("form.ajax_form input[type=text], form.ajax_form select").on('focusin', function(){
        previousAjaxDBVal = $(this).val();
    });



////////////////////////////////
// UPDATE the an ajax form. There might be multiple forms on a page.
    $("form.ajax_form input[type=text], form.ajax_form select").change(function(){
        var db_data = {};
        
        db_data['previousAjaxDBVal'] = previousAjaxDBVal;
        db_data['is_new'] = $(this).closest("form").find('input[name=is_new]').val();
        db_data['table'] = $(this).closest("form").find('input[name=table]').val();
        db_data['id']    = $(this).closest("form").find('input[name=id]').val();
        db_data['name']  = $(this).attr('name');
        db_data['value'] = $(this).val();
        
        console.log(db_data);
        $.ajax({
            data: {
                id: 'update_db_record',
                data: db_data
            }
        }).done(function(json_response){
            console.log(json_response);
            if(db_data['is_new'] == '1'){
                $(this).closest("form").find('input[name=is_new]').val("0");
            } else {
                if (json_response.update_result == '1') {
                    $("#undo_button").prop('disabled', false); 
                }
            }
        });

    });

////////////////////////////////
});

