$(document).ready(function () {
    $('#myTable').DataTable();
});

$('#modal').on('show.bs.modal', function(e) {
    addLoader();
    var button = $(e.relatedTarget);
    var id = button.attr('data-id');
    var action = button.attr('data-action');
    var action = button.attr('data-action');
    var DMName = button.attr('data-DM');

    $('.modal-footer').html("");
    $('.modal-title').html('<strong>'+ucFirst(DMName)+'</strong> '+ucFirst(action));
    switch(action){
        case 'create':
            $('.modal-footer').append('<button type="button" class="btn btn-success" data-id="'+id+'" data-DM="'+DMName+'" data-action="update" id="button-store">Save</button>');
            var request = $.ajax({
                url: "/"+DMName+"/"+action,
                method: "GET",
            });
            request.done(function( data ) {
                $( ".modal-body" ).html( data );
            });
            request.fail(function( jqXHR, textStatus ) {
                console.log("Request Failed: "+textStatus)
            });
            break;
        case 'edit':
            $('.modal-footer').append('<button type="button" class="btn btn-success" data-id="'+id+'" data-DM="'+DMName+'" data-action="update" id="button-update">Update</button>');
            var request = $.ajax({
                url: "/"+DMName+"/"+id+"/"+action,
                method: "GET",
            });
            request.done(function( data ) {
                $( ".modal-body" ).html( data );
            });
            request.fail(function( jqXHR, textStatus ) {
                console.log("Request Failed: "+textStatus)
            });
            break;
        case 'show':
            var request = $.ajax({
                url: "/"+DMName+"/"+id+"/",
                method: "GET",
            });
            request.done(function( data ) {
                $( ".modal-body" ).html( data );
            });

            request.fail(function( jqXHR, textStatus ) {
                console.log("Request Failed: "+textStatus)
            });
            break;
        case 'delete':
            $( ".modal-body" ).html("Do you really want to delete this item?");
            $('.modal-footer').append('<button type="button" class="btn btn-danger" data-id="'+id+'" data-DM="'+DMName+'" data-action="destroy" id="button-delete">Confirm</button>');
            break;
        default:
            console.log("Error");
            break;
    }
    $('.modal-footer').append('<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>');


    $('#button-store').click(function(event){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        event.preventDefault();
        var formFields = $('#form').serialize();
        var request = $.ajax({
            type: "POST",
            url: "/"+DMName+"/",
            data: formFields
        });
        request.done(function( data ) {
            if(data == 1){
                $('#modal').modal('hide');
                addAlert("Item has been created.",'success');
                $('.alert').show();
                setTimeout(function() {
                    $(".alert").fadeOut(1000);
                    location.reload();
                }, 2000);
            }
            else{
                $( ".modal-body" ).html(data);
                addAlert("Your data is wrong.", 'warning');
                $('.alert').show();
                setTimeout(function() {
                    $(".alert").fadeOut(1000);
                }, 2000);
            }

        });
        request.fail(function( jqXHR, textStatus ) {
            addAlert("There is an error.", 'error');
            $('.alert').show();
            setTimeout(function() {
                $(".alert").fadeOut(1000);
            }, 2000);
        });
    });

    $('#button-update').click(function(event){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        event.preventDefault();
        var formFields = $('#form').serialize();
        var request = $.ajax({
            type: "PUT",
            url: "/"+DMName+"/"+id,
            data: formFields
        });
        request.done(function( data ) {
            if(data == 1){
                $('#modal').modal('hide');
                addAlert("Item has been updated.", "success");
                $('.alert').show();
                setTimeout(function() {
                    $(".alert").fadeOut(1000);
                    location.reload();
                }, 2000);
            }
            else{
                $( ".modal-body" ).html(data);
                addAlert("Your data is wrong.", 'warning');
                $('.alert').show();
                setTimeout(function() {
                    $(".alert").fadeOut(1000);
                }, 2000);
            }

        });
        request.fail(function( jqXHR, textStatus ) {
            //$('#modal').modal('hide');
            addAlert("There is an error.", 'error');
            $('.alert').show();
            setTimeout(function() {
                $(".alert").fadeOut(1000);
            }, 2000);
        });
    });

    $('#button-delete').click(function(){
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        var request = $.ajax({
            type: "DELETE",
            url: "/"+DMName+"/"+id
        });
        request.done(function( data ) {
            if(data == 1){
                $('#modal').modal('hide');
                addAlert("Item has been deleted.", 'success');
                $('.alert').show();
                setTimeout(function() {
                    $(".alert").fadeOut(1000);
                    location.reload();
                }, 2000);
            }
            else{
                $( ".modal-body" ).html(data);
                addAlert("Something goes wrong.", 'warning');
                $('.alert').show();
                setTimeout(function() {
                    $(".alert").fadeOut(1000);
                }, 2000);
            }
        });
        request.fail(function( jqXHR, textStatus ) {
            //$('#modal').modal('hide');
            addAlert("There is an error.", 'error');
            $('.alert').show();
            setTimeout(function() {
                $(".alert").fadeOut(1000);
            }, 2000);
        });
    });

})


function addLoader(){
    $(".modal-body").html('<img src="images/ajax-loader.gif" class="d-block mx-auto"/>');
}

function ucFirst(string){
    return string.charAt(0).toUpperCase() + string.slice(1);
}

function addAlert(text, type) {
    var alertClass = "";
    switch (type) {
        case 'success':
            alertClass = "alert-success";
            break;
        case 'error':
            alertClass = "alert-danger";
            break;
        case 'warning':
            alertClass = "alert-warning";
            break;
    }
    $('#alert-slot').html('<div class="alert ' + alertClass + ' alert-dismissible" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"> <span aria-hidden="true">&times;</span> </button> <span id="alert-inside">' + text + '</span> </div>');
}