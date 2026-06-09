$(document).ready(function () {
    if ($.fn.select2) {
        $('.expense-key-select').select2({
            width: '100%',
            minimumResultsForSearch: 0,
            dropdownParent: $('#add_expected_expense')
        });

        $('.edit-expense-key-select').select2({
            width: '100%',
            minimumResultsForSearch: 0,
            dropdownParent: $('#edit_expense_key')
        });

        $(document).on('select2:open', function () {
            setTimeout(function () {
                var searchField = document.querySelector('.select2-container--open .select2-search__field');
                if (searchField) {
                    searchField.focus();
                }
            }, 0);
        });
    }

    // ── DataTable (blank column removed — was causing the warning) ────
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
                { data: 'checkbox',  orderable: false, searchable: false },
                { data: 'key' },
                { data: 'category' },
                { data: 'supplier' },
                { data: 'action',    orderable: false, searchable: false }
            ]
        });
    }

    // ── EDIT: populate modal from data-* attributes and open ──────────
    $('#expenseDataTable').on('click', '.editKeyBtn', function (e) {
        e.preventDefault();

        var id       = $(this).data('id');
        var key      = $(this).data('key');
        var catId    = $(this).data('category');
        var supId    = $(this).data('supplier');

        // Fill the edit modal fields
        $('#edit_key_id').val(id);
        $('#edit_key').val(key);
        $('#edit_category_id').val(catId);
        $('#edit_supplier_id').val(supId);

        // Trigger select2/chosen refresh if used
        if ($.fn.select2) {
            $('#edit_category_id').trigger('change');
            $('#edit_supplier_id').trigger('change');
        }

        // Set the form action dynamically
        $('#editExpenseKeyForm').attr('action', base_url + 'expense-keys/update/' + id);

        $('#edit_expense_key').modal('show');
    });

    // ── EDIT FORM: submit via AJAX so table reloads without page refresh
    $('#editExpenseKeyForm').on('submit', function (e) {
        e.preventDefault();

        var form    = $(this);
        var url     = form.attr('action');
        var formData = form.serialize();

        $.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            url: url,
            type: 'POST',          // Laravel route accepts POST with _method=PUT spoofing
            data: formData,
            success: function (res) {
                if (res.success == 1) {
                    $('#edit_expense_key').modal('hide');
                    swal("Updated!", res.message || "Key updated successfully.", "success");
                    $('#expenseDataTable').DataTable().ajax.reload(null, false);
                } else {
                    swal("Error!", "Could not update the key.", "error");
                }
            },
            error: function (xhr) {
                // Show Laravel validation errors if any
                var errors = xhr.responseJSON && xhr.responseJSON.errors
                    ? Object.values(xhr.responseJSON.errors).flat().join('\n')
                    : "Server error. Please try again.";
                swal("Validation Error!", errors, "error");
            }
        });
    });

    // ── DELETE: sweetalert confirm then AJAX DELETE ───────────────────
    $('#expenseDataTable').on('click', '.deleteKeyBtn', function (e) {
        e.preventDefault();

        var id  = $(this).data('id');
        var url = base_url + 'expense-keys/delete/' + id;

        swal({
            title: "Are you sure?",
            text: "This key will be permanently deleted!",
            icon: "warning",
            buttons: ["Cancel", "Yes, delete it!"],
            dangerMode: true,
        }).then(function (willDelete) {
            if (willDelete) {
                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    url: url,
                    type: 'DELETE',
                    success: function (data) {
                        if (data.success == 1) {
                            swal("Deleted!", "Key has been deleted.", "success");
                            $('#expenseDataTable').DataTable().ajax.reload(null, false);
                        } else {
                            swal("Error!", "Could not delete the key.", "error");
                        }
                    },
                    error: function () {
                        swal("Error!", "Server error. Please try again.", "error");
                    }
                });
            }
        });
    });

});
