$(document).ready(function () {
    if ($('#quoteDataTable').length > 0) {
        var quoteTable = $('#quoteDataTable').DataTable({
            "bFilter": true,
            "aaSorting": [],
            'columnDefs': [{
                'targets': [0,7],
                'orderable': false,
            }]
        });
    }

    $(document).on('select2:open', () => {
        document.querySelector('.select2-search__field').focus();
    });

    $('#quoteDataTable').on('click', '.deleteQuoteBtn', function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var url = base_url + 'quotes/delete/'+id;
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this quote details!",
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
                        $('#quoterow_' + id).remove();
                        swal("Deleted! Quote has been deleted!", {
                            icon: "success",
                        });
                    } else {
                        swal("Oops! Error in deleting Quote. Please try again.", {
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
        var url = base_url + 'quotes/delete-selected-records';
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

    //Quote Filter starts
    $('.quote_options').select2();
    $('.client_list').select2();

    $(document).on('click', '#searchQuoteResult', function (e) {
        e.preventDefault();
        var quote_status = $('#quoteStatus').val();
        var client = $('#client').val();
        if(quote_status == null && client == null) {
            swal("Oops! Please select atleast one search option to filter record.", {
                icon: "error",
            });
        }else {
            var url = $(this).attr('href')+'?quote_status='+quote_status+'&client='+client;
            window.location.href = url;
        }
    });

    $(document).on('click', '#resetQuoteResult', function (e) {
        e.preventDefault();
        var url = $(this).attr('href');
        window.location.href = url;
    });

    $(document).on('click', '#exportQuotesByOptions', function (e) {
        e.preventDefault();
        var quote_status = $('#quoteStatus').val();
        var client = $('#client').val();
        if(quote_status == null && client == null) {
            swal("Oops! Please select atleast one search option to filter record.", {
                icon: "error",
            });
        }else {
            var url = $(this).attr('href')+'?quote_status='+quote_status+'&client='+client;
            window.location.href = url;
        }
    });
    //Quote Filter ends

});