$(document).ready(function () {

    if ($('#leadDataTable').length > 0) {
        $('#leadDataTable').DataTable({
            "bFilter": true,
            "aaSorting": [],
            'columnDefs': [{
                'targets': [0,9],
                'orderable': false,
            }]
        });
    }

  
    $('#leadDataTable').on('click', '.deleteLeadBtn', function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var url = base_url + 'leads/delete/'+id;
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this lead details!",
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
                        $('#leadrow_' + id).remove();
                        swal("Deleted! Lead has been deleted!", {
                            icon: "success",
                        });
                    } else {
                        swal("Oops! Error in deleting Lead. Please try again.", {
                            icon: "error",
                        });
                    }
                });
            }
        });
    });

    $('#addEditLead').validate({
        rules: {
            company_name: {
                required: true
            },
            is_status: {
                required: true
            },
            client_mobile:{
                pattern:/^[0-9\s()]*$/,
            }
        },
        messages: {
            company_name: {
                required: 'Please enter company name'
            },
            is_status: {
                required: 'Please select status'
            },
            client_mobile:{
                pattern:'Please enter valid number'
            }
        }
    });

    $('.followDatetimepicker').datetimepicker({
        format: 'DD-MM-YYYY',
        sideBySide: true,
        icons: {
          up: "fa fa-angle-up",
          down: "fa fa-angle-down",
          next: 'fa fa-angle-right',
          previous: 'fa fa-angle-left'
        }
    });
    
    //Lead followup details popup
    $('#leadDataTable').on('click', '.viewfollowUpDetails', function () { 
        var id = $(this).attr('data-id');
        var url = base_url + 'leads/followup-details/'+id;
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: 'GET',
            dataType: "html",
        }).done(function (data) {
            $('#view_followup_details').modal("show");
            $('#response').html(data);
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
        var url = base_url + 'leads/delete-selected-records';
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
                    } else {
                        swal("Oops! Error in deleting selected records. Please try again.", {
                            icon: "error",
                        });
                    }
                });
            }
        });
    });

});