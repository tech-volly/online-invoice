$(document).ready(function () {
    if($('#openingBalanceDataTable').length > 0) {
        var departmentTable = $('#openingBalanceDataTable').DataTable({
            "bFilter": true,
            "aaSorting": [],
            'columnDefs': [{
                'targets': [0,3],
                'orderable': false,
            }],
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="las la-file-export"></i> Export Opening Balance',
                    className: 'btn btn-primary',
                    exportOptions: {
                        columns: [1,2]
                    }
                }
            ]
        });
        departmentTable.buttons().container().appendTo( '.datatableButtons' );
    }

    $('#add_opening_balance, #edit_opening_balance').modal({
        backdrop: 'static',
        keyboard: false
    });

    jQuery.validator.addMethod("numerical", function(value, element) {
        return this.optional(element) || /^[0-9.]*$/.test(value);
    }, "Please enter numbers only please");

    $('#addOpeningBalanceForm').validate({
        rules: {
            opening_balance_date: {
                required: true
            },
            opening_balance_value: { 
                required: true,
                numerical: true, 
            }
        },
        messages: {
            opening_balance_date: {
                required: 'Please select date'
            },
            opening_balance_value: { 
                required: 'Please enter opening balance'
            }
        },
        errorPlacement: function (error, element) {
            if (element.attr("name") == "opening_balance_value") {
                error.appendTo("#errorToShow");
            }else {
                error.insertAfter(element);
            }
        }
    });

    $('#editOpeningBalanceForm').validate({
        rules: {
            opening_balance_date: {
                required: true
            },
            opening_balance_value: { 
                required: true,
                numerical: true, 
            }
        },
        messages: {
            opening_balance_date: {
                required: 'Please select date'
            },
            opening_balance_value: { 
                required: 'Please enter opening balance'
            }
        },
        errorPlacement: function (error, element) {
            if (element.attr("name") == "opening_balance_value") {
                error.appendTo("#errorToShowUpdate");
            }else {
                error.insertAfter(element);
            }
        }
    });
    
    $('#openingBalanceDataTable').on('click', '.editOpeningBalance', function () { 
        var id = $(this).attr('data-id');
        var url = base_url + 'master/opening-balance/edit/'+id;
        $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: url,
            type: 'GET',
            dataType: "json",
        }).done(function (data) {
            if(data.success) {
                $('#opening_balance_id').val(data.opening_balance.id);
                $('#opening_balance_value_update').val(data.opening_balance.opening_balance_value);
                let html;
                html += '';
                html += '<option>Select Status</option>';
                html += `<option value="2021 - 2022" ${data.opening_balance.opening_balance_date === '2021 - 2022' ? 'selected' : '' }>2021 - 2022</option>`;
                html += `<option value="2022 - 2023" ${data.opening_balance.opening_balance_date === '2022 - 2023' ? 'selected' : '' }>2022 - 2023</option>`;
                html += `<option value="2023 - 2024" ${data.opening_balance.opening_balance_date === '2023 - 2024' ? 'selected' : '' }>2023 - 2024</option>`;
                html += `<option value="2024 - 2025" ${data.opening_balance.opening_balance_date === '2024 - 2025' ? 'selected' : '' }>2024 - 2025</option>`;                
                html += `<option value="2025 - 2026" ${data.opening_balance.opening_balance_date === '2025 - 2026' ? 'selected' : '' }>2025 - 2026</option>`;                
                html += `<option value="2026 - 2027" ${data.opening_balance.opening_balance_date === '2026 - 2027' ? 'selected' : '' }>2026 - 2027</option>`;                
                html += `<option value="2027 - 2028" ${data.opening_balance.opening_balance_date === '2027 - 2028' ? 'selected' : '' }>2027 - 2028</option>`;                
                html += `<option value="2028 - 2029" ${data.opening_balance.opening_balance_date === '2028 - 2029' ? 'selected' : '' }>2028 - 2029</option>`;                                            
                html += `<option value="2029 - 2030" ${data.opening_balance.opening_balance_date === '2029 - 2030' ? 'selected' : '' }>2029 - 2030</option>`;                
                html += `<option value="2030 - 2031" ${data.opening_balance.opening_balance_date === '2030 - 2031' ? 'selected' : '' }>2030 - 2031</option>`;                
                $('#opening_balance_date_update').html(html);
                $('#edit_opening_balance').modal('show');
            }
        });
    });

    $('#openingBalanceDataTable').on('click', '.deleteOpeningBalanceBtn', function(e) {
        e.preventDefault();
        var id = $(this).attr('data-id');
        var url = base_url + 'master/opening-balance/delete/'+id;
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this opening balance",
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
                        $('#openingbalancerow_' + id).remove();
                        swal("Deleted! Opening Balance has been deleted!", {
                            icon: "success",
                        });
                    } else {
                        swal("Oops! Error in deleting Opening Balance. Please try again.", {
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
        var url = base_url + 'master/opening-balance/delete-selected-records';
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