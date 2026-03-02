$(document).ready(function () {

    if ($('#resourceDataTable').length > 0) {
        $('#resourceDataTable').DataTable({
            "bFilter": true,
            "aaSorting": [],
            'columnDefs': [{
                'targets': [0,3,4],
                'orderable': false,
            }],
        });
    }

    //Validate Resource Form
    $('#addEditResource').validate({
        rules: {
            resource_name: {
                required: true
            },
            'resource_image[]': {
                required: function () {
                    if ($('#id').val() == "") {
                        return true;
                    } else {
                        return false;
                    }
                },
                //extension: "jpg|jpeg|png"
                extension: "docx|doc|odt|pdf|jpg|jpeg|png|xlsx|txt|csv"
            },
            is_status: {
                required: true
            }
        },
        messages: {
            resource_name: {
                required: 'Please enter resource name'
            },
            'resource_image[]': {
                required: 'Please select files to upload',
                extension: "Please upload valid file format"
            },
            is_status: {
                required: 'Please select status'
            }
        }
    });

    //Delete Resource
    $('#resourceDataTable').on('click', '.deleteResourceBtn', function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var url = base_url + 'invoice-resources/delete/'+id;
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this Resource details!",
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
                        $('#resourcerow_' + id).remove();
                        swal("Deleted! Resource has been deleted!", {
                            icon: "success",
                        });
                    } else {
                        swal("Oops! Error in deleting Resource. Please try again.", {
                            icon: "error",
                        });
                    }
                });
            }
        });
    });

    //Delete Resource Image
    $('.deleteResourceImage').on("click", function() {
        var id = $(this).attr('data-id');
        console.log("id : ",id);
        var url = base_url + 'invoice-resources/delete-resource-image/'+id;
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this!",
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
                    type: 'GET'
                }).done(function (data) {
                    if (data.success == 1) {
                        $('#imageDiv_'+id).remove();
                        swal("Deleted! Image has been deleted!", {
                            icon: "success",
                        });
                    } else {
                        swal("Oops! Error in deleting Image. Please try again.", {
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
        var url = base_url + 'invoice-resources/delete-selected-records';
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