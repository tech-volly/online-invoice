@if(Route::is(['dashboard','clients', 'clients.add', 'clients.edit', 'suppliers', 'suppliers.add', 'suppliers.edit', 'roles', 'profile',
'users', 'users.add', 'users.edit', 'products', 'products.add', 'products.edit', 'services', 'services.add', 'services.edit',
'expenses', 'expenses.add', 'expenses.edit', 'expenses.import-expenses', 'categories', 'expense.categories', 'departments', 'payment-methods', 'payment-statuses','leads', 'leads.add', 'leads.edit',
'invoices', 'invoices.add', 'invoices.edit', 'invoices.view', 'brands', 'brands.add', 'brands.edit', 'invoice-resources', 'invoice-resources.add', 'invoice-resources.edit',
'invoice-settings', 'estimate-settings', 'subscriptions', 'subscriptions.add', 'subscriptions.edit', 'quotes', 'quotes.add', 'quotes.edit', 'reports', 'opening-balance',
'email-logs', 'expected-expenses', 'expected-expenses.edit', 'pending-expense','projects.create','projects']))
<div class="header">
    <div class="header-left">
        <a href="{{url('dashboard')}}" class="logo">
            <img src="{{ URL::asset('public/assets/img/logo.png')}}?28062022"  width="50" height="50" alt="">
        </a>
    </div>  
    <a id="toggle_btn" href="javascript:void(0);">
        <span class="bar-icon">
            <span></span>
            <span></span>
            <span></span>
        </span>
    </a>
    <div class="page-title-box">
        <!-- <h3>HDS Financials</h3> -->
    </div> 
    <a id="mobile_btn" class="mobile_btn" href="#sidebar"><i class="fa fa-bars"></i></a>
    <ul class="nav user-menu">
        <li class="nav-item dropdown has-arrow main-drop">
            <a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                <span class="user-img"><img src="{{ URL::asset('public/assets/img/profiles/avatar-21.jpg')}}"  alt="">
                <span>{{Auth::user()->first_name}} {{Auth::user()->last_name}}</span>
            </a>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="{{url('profile')}}">My Profile</a>
                <a class="dropdown-item" href="{{url('signout')}}">Logout</a>
            </div>
        </li>
    </ul>
    <div class="dropdown mobile-user-menu">
        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"><i class="fa fa-ellipsis-v"></i></a>
        <div class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="{{url('profile')}}">My Profile</a>
            <a class="dropdown-item" href="{{url('signout')}}">Logout</a>
        </div>
    </div>
</div>
@endif