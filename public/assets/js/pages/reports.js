$(document).ready(function () {
    
    $('.select_year_value').select2();

    $(document).on('select2:open', () => {
        document.querySelector('.select2-search__field').focus();
    });
    
    //Quarter comparison report
    $(document).on('click', '#download_quater_comparison', function (e) {
        e.preventDefault();
        var year = $('#year_value').val();
        if(!year) {
            swal("Oops! Please select year to download report.", {
                icon: "error",
            });
            return false;
        }
        var url = $(this).attr('href')+'?year='+year;
        window.location.href = url;
    });

    //Quarter report
    $(document).on('click', '#download_quater_report', function (e) {
        e.preventDefault();
        var quarter = $('#select_quarter').val();
        var year = $('#select_quarter_year').val();
        if(!quarter || !year ) {
            swal("Oops! Please select year and quarter to download report.", {
                icon: "error",
            });
            return false; 
        }
        var url = $(this).attr('href')+'?year='+year+'&quarter='+quarter;
        window.location.href = url;
    });

    //PNL Report
    $(document).on('click', '#download_pnl_report', function (e) {
        e.preventDefault();
        var from_date = $('#from_date').val();
        var to_date = $('#to_date').val();
        if(from_date == '' || to_date == '') {
            swal("Oops! Please select from and to date to download report.", {
                icon: "error",
            });
            return false;
        }
        var url = $(this).attr('href')+'?from_date='+from_date+'&to_date='+to_date;
        window.location.href = url;
    });
    //PROJECT REPORT
    $(document).on('click', '#download_project_report', function (e) {
        e.preventDefault();
        var project_id= $('#project_id').val();
        if(!project_id) {
            swal("Oops! Please select  project id to download report.", {
                icon: "error",
            });
            return false;
        }
        var url = $(this).attr('href')+'?from_date='+from_date+'&to_date='+to_date+'&project_id='+project_id;
        window.location.href = url;
    });

    $('.forecastDateTimePicker').datetimepicker({
        format: 'DD-MM-YYYY',
        icons: {
          up: "fa fa-angle-up",
          down: "fa fa-angle-down",
          next: 'fa fa-angle-right',
          previous: 'fa fa-angle-left'
        },
        minDate: new Date()
    });

    $(document).on('click', '#download_forecast_report', function (e) {
        e.preventDefault();
        var to_date = $('#forecast_to_date').val();
        if(to_date == '') {
            swal("Oops! Please select to date to download report.", {
                icon: "error",
            });
            return false;
        }
        var url = $(this).attr('href')+'?to_date='+to_date;
        window.location.href = url;
    });

    //Cashflow Report
    $('.cashflowDateTimePicker').datetimepicker({
        format: 'MM-YYYY',
        icons: {
          up: "fa fa-angle-up",
          down: "fa fa-angle-down",
          next: 'fa fa-angle-right',
          previous: 'fa fa-angle-left'
        }
    });

    $(document).on('click', '#download_cashflow_report', function (e) {
        e.preventDefault();
        var from_year_month = $('#from_year_month').val();
        var to_year_month = $('#to_year_month').val();
        if(from_year_month == '' || to_year_month == '') {
            swal("Oops! Please select from and to year to download report.", {
                icon: "error",
            });
            return false;
        }
        var url = $(this).attr('href')+'?from_year_month='+from_year_month+'&to_year_month='+to_year_month;
        window.location.href = url;
    });

    $(document).on('click', '#download_expected_expense', function (e) {
        e.preventDefault();
        var expected_expense_year = $('#expected_expense_year').val();
        if(!expected_expense_year) {
            swal("Oops! Please select year to download report.", {
                icon: "error",
            });
            return false;
        }
        var url = $(this).attr('href')+'?expected_expense_year='+expected_expense_year;
        window.location.href = url;
    });

    $(document).on('click', '#download_expense_report', function (e) {
        e.preventDefault();
        var expense_year = $('#expense_year').val();
        
        var category_ids = $('#category_id').val();
        console.log("category_ids",category_ids);
        if(!expense_year) {
            swal("Oops! Please select year to download report.", {
                icon: "error",
            });
            return false;
        }
        var url = $(this).attr('href')+'?expense_year='+expense_year+'&category_id='+ category_ids.join(',');
        window.location.href = url;
    });
});