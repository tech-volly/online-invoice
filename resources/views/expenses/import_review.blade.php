<?php $page = "expenses"; ?>
@extends('layout.mainlayout')
@section('css')
<style>
.match-badge {
    font-size: 11px;
    padding: 2px 8px;
    border-radius: 10px;
}
.match-badge.matched {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}
.match-badge.unmatched {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeeba;
}
.review-table th {
    background: #3d5a99;
    color: #fff;
    font-size: 12px;
    white-space: nowrap;
    position: sticky;
    top: 0;
    z-index: 5;
}
.review-table td {
    font-size: 13px;
    vertical-align: middle;
}
.review-table .form-control {
    font-size: 12px;
    padding: 4px 8px;
    height: auto;
}
.description-cell {
    max-width: 180px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.row-skipped td {
    opacity: 0.45;
    text-decoration: line-through;
}
.row-skipped td input,
.row-skipped td select,
.row-skipped td .select2-container {
    pointer-events: none;
}
.amount-negative { color: #dc3545; font-weight: 600; }
.amount-positive { color: #198754; font-weight: 600; }
.sticky-summary {
    position: sticky;
    bottom: 0;
    background: #fff;
    border-top: 2px solid #3d5a99;
    z-index: 10;
    padding: 12px 20px;
}
.bulk-bar {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 10px 16px;
    margin-bottom: 12px;
}
.review-table .select2-container,
.bulk-bar .select2-container {
    min-width: 100%;
}
.review-table .select2-container--default .select2-selection--single,
.bulk-bar .select2-container--default .select2-selection--single {
    height: 32px;
    border: 1px solid #ced4da;
    border-radius: 4px;
}
.review-table .select2-container--default .select2-selection--single .select2-selection__rendered,
.bulk-bar .select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 30px;
    padding-left: 8px;
    padding-right: 26px;
}
.review-table .select2-container--default .select2-selection--single .select2-selection__arrow,
.bulk-bar .select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 30px;
    right: 4px;
}
</style>
@endsection
@section('content')
@component('components.breadcrumb')
    @slot('title') Review CSV Import @endslot
    @slot('li_1') Dashboard @endslot
    @slot('li_2') Expenses @endslot
    @slot('li_3') Review CSV Import @endslot
@endcomponent

@include('layout.flash-message')

<form method="POST" action="{{ route('expenses.import-from-keys.save') }}" id="reviewSaveForm">
    @csrf
    <input type="hidden" name="default_tax" value="{{ $defaults['tax'] ?? 'GST Inclusive' }}">

    {{-- Top header row --}}
    <div class="row mb-3 align-items-center">
        <div class="col-md-8">
            <h4 class="mb-1">
                Review Import &nbsp;
                <span class="badge bg-primary">{{ count($rows) }} rows</span>&nbsp;
                <span class="badge bg-success" id="matchedCount">0 matched</span>&nbsp;
                <span class="badge bg-warning text-dark" id="unmatchedCount">0 unmatched</span>&nbsp;
                <span class="badge bg-danger" id="skippedCount">0 skipped</span>
            </h4>
            <small class="text-muted">
                Matched categories/suppliers are pre-selected. Set Payment Method per row using the dropdown in the table.
            </small>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('expenses') }}" class="btn btn-secondary me-2">
                <i class="fa fa-arrow-left"></i> Back
            </a>
            <button type="submit" class="btn btn-success btn-lg">
                <i class="la la-save"></i> Save Active Rows
            </button>
        </div>
    </div>

    {{-- Bulk tools bar --}}
    <div class="bulk-bar">
        <div class="row align-items-center g-2">
            <div class="col-auto">
                <strong style="font-size:13px;">Bulk Set:</strong>
            </div>

            {{-- Bulk payment method --}}
            <div class="col-auto">
                <select class="form-control form-control-sm searchable-select" id="bulkPaymentMethod" style="min-width:170px;">
                    <option value="">— Payment Method for All —</option>
                    @foreach($payment_methods as $pm)
                        <option value="{{ $pm->id }}">{{ $pm->payment_method_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="applyBulkPaymentMethod()">Apply</button>
            </div>

            <div class="col-auto ms-3">
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-primary active" id="filterAll"     onclick="filterRows('all')">All</button>
                    <button type="button" class="btn btn-outline-success"  id="filterMatched"  onclick="filterRows('matched')">Matched</button>
                    <button type="button" class="btn btn-outline-warning"  id="filterUnmatched" onclick="filterRows('unmatched')">Unmatched</button>
                </div>
            </div>

            <div class="col-auto ms-2">
                <div class="btn-group btn-group-sm">
                    <button type="button" class="btn btn-outline-secondary" onclick="skipNegativeRows()">Skip Negatives</button>
                    <button type="button" class="btn btn-outline-danger"    onclick="skipAllRows()">Skip All</button>
                    <button type="button" class="btn btn-outline-success"   onclick="unskipAllRows()">Unskip All</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Review table --}}
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive" style="max-height:600px; overflow-y:auto;">
                <table class="table table-striped table-bordered review-table" id="reviewTable">
                    <thead>
                        <tr>
                            <th style="width:36px;">#</th>
                            <th style="width:88px;">Date</th>
                            <th style="width:195px;">Description (Invoice No.)</th>
                            <th style="width:88px;">Amount</th>
                            <th style="width:155px;">Supplier</th>
                            <th style="width:155px;">Category <span class="text-warning">*</span></th>
                            <th style="width:155px;">Payment Method <span class="text-warning">*</span></th>
                            <th style="width:100px;">Tax</th>
                            <th style="width:80px;">Match</th>
                            <th style="width:68px;">Skip</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($rows as $i => $row)
                        @php
                            $isMatched  = !empty($row['matched_supplier_id']) || !empty($row['matched_category_id']);
                            $isNegative = $row['amount'] < 0;
                            $matchClass = $isMatched ? 'matched' : 'unmatched';
                        @endphp
                        <tr class="review-row"
                            data-index="{{ $i }}"
                            data-matched="{{ $isMatched ? 1 : 0 }}"
                            data-skipped="0">

                            <td>{{ $i + 1 }}</td>

                            {{-- Date --}}
                            <td>
                                <input type="text"
                                    class="form-control"
                                    name="rows[{{ $i }}][date]"
                                    value="{{ $row['date'] }}"
                                    style="width:84px;">
                            </td>

                            {{-- Description --}}
                            <td>
                                <div class="description-cell" title="{{ $row['description'] }}">
                                    {{ $row['description'] }}
                                </div>
                                <input type="hidden" name="rows[{{ $i }}][description]" value="{{ $row['description'] }}">
                                <input type="hidden" name="rows[{{ $i }}][matched_key]" value="{{ $row['matched_key'] ?? '' }}">
                            </td>

                            {{-- Amount --}}
                            <td class="{{ $isNegative ? 'amount-negative' : 'amount-positive' }}">
                                <input type="text"
                                    class="form-control"
                                    name="rows[{{ $i }}][amount]"
                                    value="{{ $row['amount'] }}"
                                    style="width:82px;">
                            </td>

                            {{-- Supplier dropdown --}}
                            <td>
                                <select class="form-control supplier-select searchable-select" name="rows[{{ $i }}][supplier_id]" style="min-width:148px;">
                                    <option value="">-- Select --</option>
                                    @foreach($suppliers as $s)
                                        <option value="{{ $s->id }}"
                                            {{ (isset($row['matched_supplier_id']) && $row['matched_supplier_id'] == $s->id) ? 'selected' : '' }}>
                                            {{ $s->supplier_business_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            {{-- Category dropdown --}}
                            <td>
                                <select class="form-control category-select searchable-select" name="rows[{{ $i }}][category_id]" style="min-width:148px;">
                                    <option value="">-- Select --</option>
                                    @foreach($categories as $c)
                                        <option value="{{ $c->id }}"
                                            {{ (isset($row['matched_category_id']) && $row['matched_category_id'] == $c->id) ? 'selected' : '' }}>
                                            {{ $c->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            {{-- Payment Method dropdown (per row) --}}
                            <td>
                                <select class="form-control payment-method-select searchable-select" name="rows[{{ $i }}][payment_method_id]" style="min-width:148px;">
                                    <option value="">-- Select --</option>
                                    @foreach($payment_methods as $pm)
                                        <option value="{{ $pm->id }}">
                                            {{ $pm->payment_method_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>

                            {{-- Tax dropdown --}}
                            <td>
                                <select class="form-control searchable-select" name="rows[{{ $i }}][tax]" style="min-width:98px;">
                                    <option value="GST Inclusive" {{ ($defaults['tax'] ?? 'GST Inclusive') === 'GST Inclusive' ? 'selected' : '' }}>GST Inclusive</option>
                                    <option value="No GST"        {{ ($defaults['tax'] ?? '') === 'No GST' ? 'selected' : '' }}>No GST</option>
                                </select>
                            </td>

                            {{-- Match badge --}}
                            <td class="text-center">
                                <span class="match-badge {{ $matchClass }}">
                                    {{ $isMatched ? '✓ Match' : '? None' }}
                                </span>
                                @if(!empty($row['matched_key']))
                                    <br>
                                    <small class="text-muted" style="font-size:10px;" title="Key: {{ $row['matched_key'] }}">
                                        🔑 {{ Str::limit($row['matched_key'], 12) }}
                                    </small>
                                @endif
                            </td>

                            {{-- Skip toggle --}}
                            <td class="text-center">
                                <input type="hidden" name="rows[{{ $i }}][skip]" value="{{ $isNegative ? 1 : 0 }}" class="skip-hidden">
                                <button type="button"
                                    class="btn btn-sm {{ $isNegative ? 'btn-danger' : 'btn-outline-secondary' }} skip-btn"
                                    data-index="{{ $i }}">
                                    {{ $isNegative ? 'Unskip' : 'Skip' }}
                                </button>
                                @if($isNegative)
                                    <br><small class="text-danger" style="font-size:10px;">Credit</small>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Sticky summary footer --}}
    <div class="sticky-summary mt-2">
        <div class="row align-items-center">
            <div class="col-md-6">
                <strong>Summary:</strong> &nbsp;
                <span class="badge bg-success"  id="summaryActive">0 Active</span>&nbsp;
                <span class="badge bg-danger"   id="summarySkipped">0 Skipped</span>&nbsp;
                <strong>Total:</strong> <span id="summaryAmount" class="text-primary fw-bold">$0.00</span>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('expenses') }}" class="btn btn-secondary me-2">Cancel</a>
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="la la-save"></i> Save Active Rows
                </button>
            </div>
        </div>
    </div>

</form>
@endsection

@section('script')
<script>
$(document).ready(function () {
    $('.searchable-select').select2({
        width: '100%',
        minimumResultsForSearch: 0
    });

    $(document).on('select2:open', function () {
        setTimeout(function () {
            const searchField = document.querySelector('.select2-container--open .select2-search__field');
            if (searchField) {
                searchField.focus();
            }
        }, 0);
    });

    // Auto-skip negative rows
    $('.review-row').each(function () {
        if ($(this).data('skipped') == 1 ||
            parseFloat($(this).find('input[name*="[amount]"]').val()) < 0) {
            skipRow($(this));
        }
    });

    updateCounts();

    // Skip button click
    $(document).on('click', '.skip-btn', function () {
        const idx  = $(this).data('index');
        const $row = $('tr[data-index="' + idx + '"]');
        ($row.data('skipped') == 1) ? unskipRow($row) : skipRow($row);
        updateCounts();
    });

    $(document).on('change', 'select, input', updateCounts);

    // Form validation on submit
    $('#reviewSaveForm').on('submit', function (e) {
        let hasError = false;
        $('.review-row').each(function () {
            if ($(this).data('skipped') == 1) return;
            const $cat = $(this).find('.category-select');
            const $pm  = $(this).find('.payment-method-select');

            $cat.toggleClass('is-invalid', !$cat.val());
            $pm.toggleClass('is-invalid',  !$pm.val());

            if (!$cat.val() || !$pm.val()) hasError = true;
        });

        if (hasError) {
            e.preventDefault();
            alert('Please select Category and Payment Method for all active rows.');
            $('html, body').animate({ scrollTop: $('.is-invalid:first').closest('tr').offset().top - 120 }, 400);
        }
    });
});

/* ── Row helpers ── */
function skipRow($row) {
    $row.addClass('row-skipped').data('skipped', 1);
    $row.find('.skip-hidden').val(1);
    $row.find('.skip-btn').removeClass('btn-outline-secondary').addClass('btn-danger').text('Unskip');
}
function unskipRow($row) {
    $row.removeClass('row-skipped').data('skipped', 0);
    $row.find('.skip-hidden').val(0);
    $row.find('.skip-btn').removeClass('btn-danger').addClass('btn-outline-secondary').text('Skip');
}

/* ── Counts ── */
function updateCounts() {
    let matched = 0, unmatched = 0, skipped = 0, total = 0;

    $('.review-row').each(function () {
        if ($(this).data('skipped') == 1) { skipped++; return; }
        $(this).data('matched') == 1 ? matched++ : unmatched++;
        const amt = parseFloat($(this).find('input[name*="[amount]"]').val()) || 0;
        if (amt > 0) total += amt;
    });

    const active = matched + unmatched;
    $('#matchedCount').text(matched  + ' matched');
    $('#unmatchedCount').text(unmatched + ' unmatched');
    $('#skippedCount').text(skipped  + ' skipped');
    $('#summaryActive').text(active  + ' Active');
    $('#summarySkipped').text(skipped + ' Skipped');
    $('#summaryAmount').text('$' + total.toFixed(2));
}

/* ── Bulk payment method ── */
function applyBulkPaymentMethod() {
    const val = $('#bulkPaymentMethod').val();
    if (!val) { alert('Please select a payment method first.'); return; }
    $('.review-row').each(function () {
        if ($(this).data('skipped') == 1) return;
        $(this).find('.payment-method-select').val(val).trigger('change');
    });
}

/* ── Filter rows ── */
function filterRows(type) {
    $('.review-row').each(function () {
        if      (type === 'all')       $(this).show();
        else if (type === 'matched')   $(this).data('matched') == 1 ? $(this).show() : $(this).hide();
        else if (type === 'unmatched') $(this).data('matched') == 0 ? $(this).show() : $(this).hide();
    });
    $('.btn-group .btn').removeClass('active');
    $('#filter' + type.charAt(0).toUpperCase() + type.slice(1)).addClass('active');
}

/* ── Bulk skip helpers ── */
function skipNegativeRows() {
    $('.review-row').each(function () {
        if (parseFloat($(this).find('input[name*="[amount]"]').val()) < 0) skipRow($(this));
    });
    updateCounts();
}
function skipAllRows()   { $('.review-row').each(function () { skipRow($(this)); });   updateCounts(); }
function unskipAllRows() { $('.review-row').each(function () { unskipRow($(this)); }); updateCounts(); }
</script>
@endsection
