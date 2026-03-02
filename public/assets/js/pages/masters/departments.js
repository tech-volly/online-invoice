$(document).ready(function () {
    if($('#departmentDataTable').length > 0) {
        var departmentTable = $('#departmentDataTable').DataTable({
            "bFilter": true,
            "aaSorting": [],
            'columnDefs': [{
                'targets': [0,3],
                'orderable': false,
            }],
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="las la-file-export"></i> Export Departments',
                    className: 'btn btn-primary',
                    exportOptions: {
                        columns: [1,2]
                    }
                }
            ]
        });
        departmentTable.buttons().container().appendTo( '.datatableButtons' );
    }

    $('#add_departments, #edit_departments, #import_departments').modal({
        backdrop: 'static',
        keyboard: false
    });

    $('#addDepartmentForm').validate({
        rules: {
            department_name: {
                required: true
            },
            is_status: { 
                required: true, 
            }
        },
        messages: {
            department_name: {
                required: 'Please enter department name'
            },
            is_status: { 
                required: 'Please select status', 
            }
        }
    });

    $('#editCategoryForm').validate({
        rules: {
            department_name: {
                required: true
            },
            is_status: { 
                required: true, 
            }
        },
        messages: {
            department_name: {
                required: 'Please enter department name'
            },
            is_status: { 
                required: 'Please select status', 
            }
        }
    });
    
    $('#departmentDataTable').on('click', '.editDepartment', function () { 
        var id = $(this).attr('data-id');
        var url = base_url + 'master/departments/edit/'+id;
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: 'GET',
            dataType: "json",
        }).done(function (data) {
            console.log(data);
            if(data.success) {
                $('#department_id').val(data.category.id);
                $('#update_department_name').val(data.category.name);
                let html;
                html += '';
                html += '<option>Select Status</option>';
                html += `<option value="1" ${data.category.is_status === 1 ? 'selected' : '' }>Active</option>`;
                html += `<option value="0" ${data.category.is_status === 0 ? 'selected' : '' }>Inactive</option>`;
                $('#update_is_status').html(html);
                $('#edit_departments').modal('show');
            }
        });
    });

    $('#importDepartmentForm').validate({
        rules: {
            import_department_file: {
                required: true,
                extension: "csv"
            }
        },
        messages: {
            import_department_file: {
                required: 'Please upload file to import departments',
                extension: 'Please upload csv only'
            }
        }
    });
    
    $('#departmentDataTable').on('click', '.deleteDepartmentBtn', function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var url = base_url + 'master/departments/delete/'+id;
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this department",
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
                        $('#departmentrow_' + id).remove();
                        swal("Deleted! Department has been deleted!", {
                            icon: "success",
                        });
                    } else if(data.success == 2) {
                        swal("Oops! You can not delete this department as it is used in Users.", {
                            icon: "error",
                        });
                    } else {
                        swal("Oops! Error in deleting Department. Please try again.", {
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
        var url = base_url + 'master/departments/delete-selected-records';
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
                        swal("Oops! You can not delete this department as it is used in Users.", {
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
});