$(document).ready(function () {

    if ($('#brandDataTable').length > 0) {
        var brandTable = $('#brandDataTable').DataTable({
            "bFilter": true,
            "aaSorting": [],
            'columnDefs': [{
                'targets': [0,1,4],
                'orderable': false,
            }],
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="las la-file-export"></i> Export Brands',
                    className: 'btn btn-primary',
                    exportOptions: {
                        columns: [2,3] 
                    }
                }
            ]
        });
        brandTable.buttons().container().appendTo( '.datatableButtons' );
    }

    $('#brandDataTable').on('click', '.deleteBrandBtn', function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var url = base_url + 'master/brands/delete/'+id;
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this brand details!",
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
                        $('#brandrow_' + id).remove();
                        swal("Deleted! Brand has been deleted!", {
                            icon: "success",
                        });
                    } else if(data.success == 2) {
                        swal("Oops! You can not delete this brand as it is used in Invoice.", {
                            icon: "error",
                        });
                    } else {
                        swal("Oops! Error in deleting Brand. Please try again.", {
                            icon: "error",
                        });
                    }
                });
            }
        });
    });

    $(document).on('click', '.removeBrandImage', function (e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var url = base_url + 'master/brands/delete-brand-logo/'+id;
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
                        $('#attachedImageDiv').remove();
                        swal("Deleted! Logo has been deleted!", {
                            icon: "success",
                        });
                    } else {
                        swal("Oops! Error in deleting Logo. Please try again.", {
                            icon: "error",
                        });
                    }
                });
            }
        });
    });

    $('#addEditBrand').validate({
        rules: {
            name: {
                required: true,
            },
            image: {
                required: function () {
                    if ($('#brand_image_old').val() == "") {
                        return true;
                    } else {
                        return false;
                    }
                },
                accept: "image/*"
            },
            is_status: {
                required: true
            }
        },
        messages: {
            name: {
                required: 'Please enter brand name'
            },
            image: {
                required: 'Please select brand logo',
                accept: "Please select image only"
            },
            is_status: {
                required: 'Please select status'
            }
        }
    });
    
    $('.image-link').magnificPopup({
        type:'image',
        closeOnContentClick: true,
		mainClass: 'mfp-img-mobile',
		image: {
			verticalFit: true
		}
    });

    $('#image').change(function () {
        var file = $("input[type=file]").get(0).files[0];
        if(file){
            var reader = new FileReader();
            reader.onload = function(){
                $("#previewImg").attr("src", reader.result);
                $('#previewImg').show();
            } 
            reader.readAsDataURL(file);
        }
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
        var url = base_url + 'master/brands/delete-selected-records';
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
                        swal("Oops! You can not delete this brand as it is used in Invoice.", {
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