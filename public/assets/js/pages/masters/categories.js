$(document).ready(function () {
    
    if($('#categoryDataTable').length > 0) {
        var categories = $('#categoryDataTable').DataTable({
            "bFilter": true,
            "aaSorting": [],
            'columnDefs': [{
                'targets': [0,3],
                'orderable': false,
            }],
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="las la-file-export"></i> Export Product Categories',
                    className: 'btn btn-primary',
                    exportOptions: {
                        columns: [1,2] 
                    }
                }
            ]
        });
        categories.buttons().container().appendTo( '.datatableButtons' );
    }
    
    $('#edit_categories, #add_categories, #import_categories').modal({
        backdrop: 'static',
        keyboard: false
    });

    $('#addCategoryForm').validate({
        rules: {
            category_name: {
                required: true
            },
            is_status: { 
                required: true, 
            }
        },
        messages: {
            category_name: {
                required: 'Please enter category name'
            },
            is_status: { 
                required: 'Please select status', 
            }
        }
    });

    $('#editCategoryForm').validate({
        rules: {
            category_name: {
                required: true
            },
            is_status: { 
                required: true, 
            }
        },
        messages: {
            category_name: {
                required: 'Please enter category name'
            },
            is_status: { 
                required: 'Please select status', 
            }
        }
    });

    $('#importCategoryForm').validate({
        rules: {
            import_categories_file: {
                required: true,
                extension: "csv"
            }
        },
        messages: {
            import_categories_file: {
                required: 'Please upload file to import product categories',
                extension: 'Please upload csv only'
            }
        }
    });
    
    $('#categoryDataTable').on('click', '.editCategory', function () { 
        var id = $(this).attr('data-id');
        var url = base_url + 'master/product/categories/edit/'+id;
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: 'GET',
            dataType: "json",
        }).done(function (data) {
            if(data.success) {
                $('#category_id').val(data.category.id);
                $('#update_category_name').val(data.category.name);
                let html;
                html += '';
                html += '<option>Select Status</option>';
                html += `<option value="1" ${data.category.is_status === 1 ? 'selected' : '' }>Active</option>`;
                html += `<option value="0" ${data.category.is_status === 0 ? 'selected' : '' }>Inactive</option>`;
                $('#update_is_status').html(html);
                $('#edit_categories').modal('show');
            }
        });
    });
    
    $('#categoryDataTable').on('click', '.deleteCategoryBtn', function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var url = base_url + 'master/product/categories/delete/'+id;
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this category",
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
                        $('#categoryrow_' + id).remove();
                        swal("Deleted! Category has been deleted!", {
                            icon: "success",
                        });
                    }else if(data.success == 2) {
                        swal("Oops! You can not delete this product category as it is used in Products.", {
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
        var url = base_url + 'master/product/categories/delete-selected-records';
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
                        swal("Oops! You can not delete this product category as it is used in Products.", {
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