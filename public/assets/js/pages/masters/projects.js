$(document).ready(function () {
    
    if($('#projectDataTable').length > 0) {
        var projects = $('#projectDataTable').DataTable({
            "bFilter": true,
            "aaSorting": [],
            'columnDefs': [{
                'targets': [0,3],
                'orderable': false,
            }],
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="las la-file-export"></i> Export Prodect List',
                    className: 'btn btn-primary',
                    exportOptions: {
                        columns: [1,2] 
                    }
                }
            ]
        });
        projects.buttons().container().appendTo( '.datatableButtons' );
    }
    
    $('#edit_projects, #add_projects, #import_projects').modal({
        backdrop: 'static',
        keyboard: false
    });

    $('#addProjectForm').validate({
        rules: {
            project_name: {
                required: true
            },
            is_status: { 
                required: true, 
            }
        },
        messages: {
            project_name: {
                required: 'Please enter project name'
            },
            is_status: { 
                required: 'Please select status', 
            }
            
        }
    });

    $('#editProjectForm').validate({
        rules: {
            project_name: {
                required: true
            },
            is_status: { 
                required: true, 
            }
        },
        messages: {
            project_name: {
                required: 'Please enter project name'
            },
            is_status: { 
                required: 'Please select status', 
            }
        }
    });

    $('#importProjectForm').validate({
        rules: {
            import_projects_file: {
                required: true,
                extension: "csv"
            }
        },
        messages: {
            import_projects_file: {
                required: 'Please upload file to import project list',
                extension: 'Please upload csv only'
            }
        }
    });
    
    $('#projectDataTable').on('click', '.editProject', function () { 
        var id = $(this).attr('data-id');
        var url = base_url + 'master/projects/edit/'+id;
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: 'GET',
            dataType: "json",
        }).done(function (data) {
            if(data.success) {
                $('#project_id').val(data.project.id);
                $('#update_project_name').val(data.project.name);
                let html;
                html += '';
                html += '<option>Select Status</option>';
                html += `<option value="1" ${data.project.is_status === 1 ? 'selected' : '' }>Active</option>`;
                html += `<option value="0" ${data.project.is_status === 0 ? 'selected' : '' }>Inactive</option>`;
                $('#update_is_status').html(html);
                $('#edit_projects').modal('show');
            }
        });
    });
    
    $('#projectDataTable').on('click', '.deleteProjectBtn', function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var url = base_url + 'master/projects/delete/'+id;
       
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this project",
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
                        $('#projectrow_' + id).remove();
                        swal("Deleted! Project has been deleted!", {
                            icon: "success",
                        });
                    }else if(data.success == 2) {
                        swal("Oops! You can not delete this project project as it is used.", {
                            icon: "error",
                        });
                    } else {
                        swal("Oops! Error in deleting Category. Please try again.", {
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
        var url = base_url + 'master/projects/delete-selected-records';
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
                    }else if(data.success == 2) {
                        swal("Oops! You can not delete this project.", {
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