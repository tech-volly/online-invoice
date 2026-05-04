$(document).ready(function () {

    if ($('#expenseDataTable').length > 0) {
        var table = $('#expenseDataTable').DataTable({
            "bFilter": true,
            "aaSorting": [],
            processing: true,
            serverSide: true,
            pageLength: 50,
            ajax: {
                url: base_url + 'expense-keys/get-data',
                type: 'GET'
            },
            columns: [
                { data: 'checkbox', orderable: false, searchable: false },
                { data: 'key' },
                { data: 'category' },
                { data: 'blank', orderable: false, searchable: false },
                { data: 'action', orderable: false, searchable: false }
            ]
        });
    }

    // Delete single record
    $('#expenseDataTable').on('click', '.deleteKeyBtn', function (e) {
        e.preventDefault();

        var id = $(this).data('id');
        var url = base_url + 'expense-keys/delete/' + id;

        swal({
            title: "Are you sure?",
            text: "This key will be deleted!",
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
                        swal("Deleted successfully!", {
                            icon: "success",
                        });
                        $('#expenseDataTable').DataTable().ajax.reload();
                    } else {
                        swal("Error deleting record!", {
                            icon: "error",
                        });
                    }
                });
            }
        });
    });

});