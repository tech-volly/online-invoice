$(document).ready(function () {
    if ($('#rolesDataTable').length > 0) {
       $('#rolesDataTable').DataTable({
            "bFilter": true,
            "aaSorting": [],
            'columnDefs': [{
                'targets': [0,2],
                'orderable': false,
            }]
        });
    }

    $('#add_role, #edit_role').modal({
        backdrop: 'static',
        keyboard: false
    });

    $('#addRoleForm').validate({
        rules: {
            name: {
                required: true
            },
            'permission[]': { 
                required: true, 
                minlength: 1 
            }
        },
        messages: {
            name: {
                required: 'Please enter role name'
            },
            'permission[]': { 
                required: 'Please select permission option', 
                minlength: 'Please select atleast one checkbox' 
            }
        }
    });

    $('#editRoleForm').validate({
        rules: {
            name: {
                required: true
            },
            'permission[]': { 
                required: true, 
                minlength: 1 
            }
        },
        messages: {
            name: {
                required: 'Please enter role name'
            },
            'permission[]': { 
                required: 'Please select permission option', 
                minlength: 'Please select atleast one checkbox' 
            }
        }
    });
    
    $('#rolesDataTable').on('click', '.editRole', function () { 
        var id = $(this).attr('data-id');
        var url = base_url + 'roles/edit/'+id;
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
                $('#name').val(data.role.name);
                $('#role_id').val(data.role.id);
                addPermissionBlock(data.permission, data.rolePermissions)
                $('#edit_role').modal('show');
            }
        });
    });
    
    $('#rolesDataTable').on('click', '.deleteRole', function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var url = base_url + 'roles/delete/'+id;
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this role",
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
                        $('#rolerow_' + id).remove();
                        swal("Deleted! Role has been deleted!", {
                            icon: "success",
                        });
                    } else {
                        swal("Oops! Error in deleting role. Please try again.", {
                            icon: "error",
                        });
                    }
                });
            }
        });
    });

    function addPermissionBlock(permission, rolePermissions) {        
        $(".permissions").find("div").html('');
        for (let i = 0; i < permission.length; i += 4) {
            const chunk = permission.slice(i, i + 4);
            let html = '<div class="row table table-striped">';
            $.each( chunk, function( key, value ) {
                if(Object.values(rolePermissions).includes(value.id)) {
                    var checked = 'checked';
                }else {
                    var checked = '';
                }
                html += '<div class="col-3"><div class="checkbox"><label>';
                html += '<input type="checkbox" value="'+value.id+'" name="permission[]"  '+checked+'    >  '+value.name;
                html += '</label></div></div>';
            });
            html+= ' </div>';
            $(".permissions").append(html)
        }
    }

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
        var url = base_url + 'roles/delete-selected-records';
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