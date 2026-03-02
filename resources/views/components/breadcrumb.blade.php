<!-- Page Header -->
@if(Route::is(['clients', 'suppliers', 'roles', 'profile', 'users', 'products', 'services', 'expenses', 'categories', 
'payment-methods', 'leads', 'invoices', 'brands', 'invoice-resources', 'subscriptions', 'quotes', 'reports', 'opening-balance', 'email-logs', 'expected-expenses', 'pending-expense']))
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">{{ $title }}</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('dashboard')}}">{{ $li_1 }}</a></li>
                <li class="breadcrumb-item active">{{ $li_2 }}</li>
            </ul>
        </div>
        @if(Route::is(['clients'])) 
            @can('client-create')
            <div class="col-auto float-end ms-auto">
                <a href="{{route('clients.add')}}" class="btn add-btn"><i class="fa fa-plus"></i> Add Client</a>
            </div>
            @endcan
        @elseif(Route::is(['suppliers']))
            @can('supplier-create')
            <div class="col-auto float-end ms-auto">
                <a href="{{route('suppliers.add')}}" class="btn add-btn"><i class="fa fa-plus"></i> Add Supplier</a>
            </div>
            @endcan
        @elseif(Route::is(['users']))
            @can('user-create')
            <div class="col-auto float-end ms-auto">
                <a href="{{route('users.add')}}" class="btn add-btn"><i class="fa fa-plus"></i> Add User</a>
            </div>
            @endcan
        @elseif(Route::is(['products']))
            @can('product-create')
            <div class="col-auto float-end ms-auto">
                <a href="{{route('products.add')}}" class="btn add-btn"><i class="fa fa-plus"></i> Add Product</a>
            </div>
            @endcan
        @elseif(Route::is(['services']))
            <div class="col-auto float-end ms-auto">
                <a href="{{route('services.add')}}" class="btn add-btn"><i class="fa fa-plus"></i> Add Service</a>
            </div>
        @elseif(Route::is(['expenses']))
            @can('expense-create')
            <div class="col-auto float-end ms-auto">
                <a href="{{route('expenses.add')}}" class="btn add-btn"><i class="fa fa-plus"></i> Add Expense</a>
            </div>
            @endcan
        @elseif(Route::is(['leads']))
            @can('lead-create')
            <div class="col-auto float-end ms-auto">
                <a href="{{route('leads.add')}}" class="btn add-btn"><i class="fa fa-plus"></i> Add Lead</a>
            </div>
            @endcan
        @elseif(Route::is(['invoice-resources']))
            @can('resource-create')
            <div class="col-auto float-end ms-auto">
                <a href="{{route('invoice-resources.add')}}" class="btn add-btn"><i class="fa fa-plus"></i> Add Resource</a>
            </div>
            @endcan
        @elseif(Route::is(['invoices']))
            @can('invoice-create')
            <div class="col-auto float-end ms-auto">
                <a href="{{route('invoices.add')}}" class="btn add-btn"><i class="fa fa-plus"></i> Add Invoice</a>
            </div>
            @endcan
        @elseif(Route::is(['subscriptions']))
            @can('subscription-create')
            <div class="col-auto float-end ms-auto">
                <a href="{{route('subscriptions.add')}}" class="btn add-btn"><i class="fa fa-plus"></i> Add Subscription</a>
            </div>
            @endcan
        @elseif(Route::is(['quotes']))
            @can('quote-create')
            <div class="col-auto float-end ms-auto">
                <a href="{{route('quotes.add')}}" class="btn add-btn"><i class="fa fa-plus"></i> Add Quote</a>
            </div>
            @endcan
        @elseif(Route::is(['brands']))
            @can('brand-create')
            <div class="col-auto float-end ms-auto">
                <a href="{{route('brands.add')}}" class="btn add-btn"><i class="fa fa-plus"></i> Add Brand</a>
            </div>
            @endcan
        @endif
    </div>
</div>
@endif

@if(Route::is(['clients.add', 'clients.edit', 'suppliers.add', 'suppliers.edit', 'users.add', 'users.edit', 'products.add', 'products.edit', 
'services.add', 'services.edit', 'expenses.add', 'expenses.edit', 'payment-methods.add',
'payment-methods.edit', 'leads.add', 'leads.edit', 'invoices.add', 'invoices.edit', 'invoices.view', 'brands.add', 'brands.edit', 
'subscriptions.add', 'subscriptions.edit', 'expected-expenses.edit']))
<div class="page-header">
    <div class="row align-items-center">
        <div class="col">
            <h3 class="page-title">{{ $title }}</h3>
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{url('dashboard')}}">{{ $li_1 }}</a></li>
                <li class="breadcrumb-item">{{ $li_2 }}</li>
                <li class="breadcrumb-item active">{{ $li_3 }}</li>
            </ul>
        </div>
    </div>
</div>
@endif

<!-- Page Header -->
@if(Route::is(['change-password']))
<div class="page-header mb-2">
    <div class="row">
        <div class="col-sm-12">
            <h3 class="page-title">{{ $title }}</h3>
        </div>
    </div>
</div>
    <!-- /Page Header -->
@endif 

