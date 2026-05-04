$(document).ready(function () {
    if ($('#quoteDataTable').length > 0) {
        var quoteTable = $('#quoteDataTable').DataTable({
            "bFilter": true,
            "aaSorting": [],
            'columnDefs': [{
                'targets': [0,7],
                'orderable': false,
            }],
            processing: true,
            serverSide: true,
            pageLength: 50,
            ajax: {
                url: base_url + 'quotes/get-data',
                type: 'GET',
                data: function(d) {
                    d.quote_status = $('#quoteStatus').val();
                    d.client = $('#client').val();
                }
            },
            columns: [
                { data: 'id', orderable: false, searchable: false },
                { data: 'quote_number' },
                { data: 'client' },
                { data: 'quote_date' },
                { data: 'amount' },
                { data: 'quote_status', orderable: false, searchable: false },
                { data: 'is_status', orderable: false, searchable: false },
                { data: 'action', orderable: false, searchable: false }
            ]
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
            // reload datatable with filters
            if (typeof quoteTable !== 'undefined') {
                quoteTable.ajax.reload();
            }
        }
    });

    $(document).on('click', '#resetQuoteResult', function (e) {
        e.preventDefault();
        $('#quoteStatus').val(null).trigger('change');
        $('#client').val(null).trigger('change');
        if (typeof quoteTable !== 'undefined') {
            quoteTable.ajax.reload();
        } else {
            window.location.href = $(this).attr('href');
        }
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