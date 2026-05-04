$(document).ready(function () {

    if ($('#usersDataTable').length > 0) {
        var userTable = $('#usersDataTable').DataTable({
            "bFilter": true,
            "aaSorting": [],
            'columnDefs': [{
                'targets': [0,7],
                'orderable': false,
            }],
        });
    }

    $('.dept_list').select2();
    $('.role_list').select2();
    
    $(document).on('select2:open', () => {
        document.querySelector('.select2-search__field').focus();
    });


    $('#addEditUser').validate({
        rules: {
            first_name: {
                required: true
            },
            last_name: {
                required: true
            },
            email: {
            	email: true,
                required: true
            },
            phone_number: {
            	//number: true,
                pattern:/^[0-9\s()]*$/,
                required: true
            },
            department_id: {
                required: true,
            },
            is_role: {
                required: true,
            },
            user_confirm_password: {
                required: function () {
                    if ($('#user_password').val() == '') {
                        return false;
                    } else {
                        return true;
                    }
                },
                equalTo: "#user_password"
            },
            is_status: {
                required: true
            }
        },
        messages: {
            first_name: {
                required: 'Please enter first name'
            },
            last_name: {
                required: 'Please enter first name'
            },
            email: {
                required: 'Please enter email',
                email: 'Please enter valid email'
            },
            phone_number: {
            	pattern: 'Please enter valid number',
                required: 'Please enter phone number'
            },
            department_id: {
                required: 'Please select department'
            },
            is_role: {
                required: 'Please select role for user'
            },
            user_confirm_password: {
                required: 'Please enter confirm password',
                equalTo: 'Password and confirm password should be same'
            },
            is_status: {
                required: 'Please select status'
            }
        },
        errorPlacement: function (error, element) {
            if (element.attr("name") == "is_role") {
                error.appendTo("#errorToShowRole");
            }else if(element.attr("name") == "department_id"){
                error.appendTo("#errorToShowDepartment");
            }else {
                error.insertAfter(element);
            }
        }
    });

    $('#usersDataTable').on('click', '.deleteUserBtn', function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var url = base_url + 'users/delete/'+id;
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this user details!",
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
                        $('#userrow_' + id).remove();
                        swal("Deleted! User has been deleted!", {
                            icon: "success",
                        });
                    } else {
                        swal("Oops! Error in deleting User. Please try again.", {
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
        var url = base_url + 'users/delete-selected-records';
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

    $('#importUserForm').validate({
        rules: {
            import_users_file: {
                required: true,
                extension: "csv"
            }
        },
        messages: {
            import_users_file: {
                required: 'Please upload file to import users',
                extension: 'Please upload csv only'
            }
        }
    });
});