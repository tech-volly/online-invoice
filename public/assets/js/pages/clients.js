$(document).ready(function () {  
    var clientTable = $('#clientDataTable').DataTable({
        "aaSorting": [],
        'columnDefs': [{
            'targets': [0,5],
            'orderable': false,
        }]
    });

    $('#import_clients').modal({
        backdrop: 'static',
        keyboard: false
    });

    $('#clientDataTable').on('click', '.deleteClientBtn', function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var url = base_url + 'clients/delete/'+id;
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this client details!",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    type: 'DELETE'
                }).done(function (data) {
                    if (data.success == 1) {
                        $('#clientrow_' + id).remove();
                        swal("Deleted! Client has been deleted!", {
                            icon: "success",
                        });
                    } else if(data.success == 2) {
                        swal("Oops! You can not delete this client details.", {
                            icon: "error",
                        });
                    } else {
                        swal("Oops! Error in deleting client. Please try again.", {
                            icon: "error",
                        });
                    }
                });
            }
        });
    });

    //Delete selected records
    $("#checkall").change(function () {
        if ($("#checkall").is(':checked')) {
            $(".cb-element").each(function () {
                $(this).prop("checked", true);
            });
        } else {
            $(".cb-element").each(function () {
                $(this).prop("checked", false);
            })
        }
    });

    $('#btnAllDelete').on('click', function () {
        var myCheckboxes = new Array();
        $("input.cb-element:checked").each(function () {
            myCheckboxes.push($(this).val());
        });
        if (myCheckboxes.length == 0) {
            swal("Alert!", "Please select atleast one record to delete.", "warning");
            return false;
        }
        var url = base_url + 'clients/delete-selected-records';
        swal({
            title: "Are you sure?",
            text: "You want to delete the selected record(s)?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: url,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        ids: myCheckboxes
                    },
                }).done(function (data) {
                    console.log(data);
                    console.log(data.success);
                    if (data.success == 1) {
                        swal("Selected records  has been deleted!", {
                            icon: "success",
                        });
                        window.location.reload();
                    } else if(data.success == 2) {
                        swal("Oops! You can not delete this client details.", {
                            icon: "error",
                        });
                    } else {
                        swal("Oops! Error in deleting selected records. Please try again.", {
                            icon: "error",
                        });
                    }
                });
            }
        });
    });

    //View Supplier Notes
    $('#clientDataTable').on('click', '.viewNote', function () { 
        var id = $(this).attr('data-id');
        var url = base_url + 'clients/view-client-note/'+id;
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: 'GET',
            dataType: "json",
        }).done(function (data) {
            if(data.success) {
                if(data.client_notes) {
                    $('#client_notes').html(data.client_notes);
                }else {
                    $('#client_notes').html('No notes are added for this client.');
                }
                $('#view_client_notes').modal('show');
            }
        });
    });

    $('#importClientForm').validate({
        rules: {
            import_clients_file: {
                required: true,
                extension: "csv"
            }
        },
        messages: {
            import_clients_file: {
                required: 'Please upload file to import clients',
                extension: 'Please upload csv only'
            }
        }
    });

});