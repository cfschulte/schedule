/************

  form.js -  Thu Aug 29 10:43:37 CDT 2019

*************/

// Use this for undoing changes.
var previousAjaxDBVal;
var previousCommentVal;

////////////////////////////////
// CHECK THE UNDO STATUS. Enable the undo button if there changes
$(document).ready(function() {
    var db_data = {};
    db_data['id'] = $('form.ajax_form input[name=id]').val();
    db_data['table'] = $('form.ajax_form input[name=table]').val();
    db_data['is_new'] = $('form.ajax_form input[name=is_new]').val();
    
//     console.log(db_data);
    
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
//             console.log(json_response);
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

        if($(this).is('input[type=text]')) {
            db_data['type'] = 'text';
        } else if($(this).is('input[type=radio]')) {  // RADIO 
            db_data['type'] = 'radio';
          // WE CAN'T ACCESS THE PREVIOUS value directly so we save it in a hidden field.
           // First, get the name of the input holding the previous value.
           var name_prev_val_input = $(this).attr('name');
           name_prev_val_input += '_prev';
           // then set the data to that
            db_data['previousGenericVal'] = $("form.ajax_form input[name=" + name_prev_val_input +"]").val();
           // and then set the hidden variable to this current one.
            $("form.ajax_form input[name=" + name_prev_val_input +"]").val( $(this).val() );
        } else if($(this).is('select')) {
            db_data['type'] = 'select';
        } else if($(this).is('textarea')) {
            db_data['type'] = 'textarea';
        }

        
        db_data['previousAjaxDBVal'] = previousAjaxDBVal;
        db_data['is_new'] = $("#is_new").val();
        db_data['table'] = $("#table").val();
        db_data['id']    = $("#id").val();
        
        db_data['name']  = $(this).attr('name');
        db_data['value'] = $(this).val();
        
        // in case we need to update the title
        var title_first_name = $("#first_name").val();
        var title_last_name = $("#last_name").val();
        
//         console.log('title_first_name ' + title_first_name + "," + 'title_last_name ' + title_last_name);
//         console.log(db_data);
        $.ajax({
            data: {
                id: 'update_db_record',
                data: db_data
            }
        }).done(function(json_response){
            
            console.log(json_response);
            if(db_data['is_new'] == '1'){
                $("#is_new").val("0");
                $("id").val(json_response.id);
            } else {
                if (json_response.update_result == '1') {
                    $("#undo_button").prop('disabled', false); 
                }
            }
            
            // reset the titles if the names have been changed.
            if(db_data['name'] == 'first_name' ){
                var title = db_data['value'] + " " + title_last_name;
                $("title").text(title);
                $("h1").text(title);
            }else if( db_data['name'] == 'last_name'){
                var title =  title_first_name + " " + db_data['value'];
                $("title").text(title);
                $("h1").text(title);
            }
        });

    });

////////////////////////////////
// Handle the UNDO 
    $("#undo_button").click(function(event){
        var db_data = {};
        db_data['id']    = $("#id").val();
        db_data['table'] = $("#table").val();
        
//         console.log(db_data);
        $.ajax({
            data:{
                id: 'undo_last_change',
                data: db_data
            }
        }).done(function(json_response){
            // change the input that has been undone 
            var input_name  = json_response['column'];
            var input_accessor ;
            
           if( json_response.form_type == 'text' ){
               console.log('hello');
               input_accessor = 'form.ajax_form input[name=' + input_name + ']';
               $(input_accessor).val(json_response['value']);
           } else if( json_response.form_type == 'textarea' ){
               input_accessor = 'form.ajax_form textarea[name=' + input_name + ']';
              $(input_accessor).val(son_response['value']);
             
           } else if( json_response.form_type == 'select' ){
               input_accessor = 'form.ajax_form select[name=' + input_name + ']';
               $(input_accessor).val(json_response['value']); 
           } else if( json_response.form_type == 'radio' ){
               var radio_group = $('form.ajax_form input:radio[name=' + input_name + ']');
               // set true for radio_group of the correct value to true. The others will
               // be set to false by the group
                radio_group.filter('[value='+ value +']').prop('checked', true);
           }
            
           // should the undo button be enabled?
             if(json_response.undo_size < 1) {
                 $("#undo_button").prop('disabled', true); 
             }   
            
        });
        
        event.preventDefault();
    });
    
////////////////////////////////
// Add a new comment
    $("#new_comment").click(function(event){
        var db_data = {};
        db_data['member_id'] = $("#id").val();
        db_data['posted_by'] = $("#user_id").val();
        db_data['table_name'] = $("#table_name").val();
        
        $.ajax({
            data:{
                id: 'new_comment',
                data: db_data
            }
        }).done(function(json_response){
            // insert the new comment after the last one.
            var last_commet = $(".comment").last();
            console.log(last_commet);
            $(json_response.comment).insertAfter(last_commet);
        });
    });

////////////////////////////////
// SAVE the state of the previous value on any element clicked.
    $(".comment textarea").on('focusin', function(){
        previousCommentVal = this.value;
    });


    
////////////////////////////////
// Update the comment 
    $(".comment textarea").on('change', function(event){
        var db_data = {};
        db_data['member_id'] = $("#id").val();
        db_data['posted_by'] = $("#user_id").val();
        
        db_data['the_text'] = this.value;;
        db_data['is_new'] = $(this).closest("div.comment input[name=is_new]").val();
        db_data['post_date'] = $(this).closest("div.comment h3").html();
        db_data['previousCommentVal'] = previousCommentVal;
        
        
        $.ajax({
            data:{
                id: 'update_comment',
                data: db_data
            }
        }).done(function(json_response){
            // insert the new comment after the last one.
            var last_commet = $(".comment").last();
            console.log(last_commet);
            $(json_response.comment).insertAfter(last_commet);
        });
    });
});

