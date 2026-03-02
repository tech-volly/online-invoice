@if(Route::is(['dashboard','clients', 'clients.add', 'clients.edit', 'suppliers', 'suppliers.add', 'suppliers.edit', 'roles', 'profile', 
'users', 'users.add', 'users.edit', 'products', 'products.add', 'products.edit', 'services', 'services.add', 'services.edit', 
'expenses', 'expenses.add', 'expenses.edit', 'expenses.import-expenses', 'categories', 'expense.categories', 'departments', 'payment-methods', 'payment-statuses', 'payment-methods.add',
'payment-methods.edit', 'leads', 'leads.add', 'leads.edit', 'invoices', 'invoices.add', 'invoices.edit', 'invoices.view', 'brands', 'brands.add', 'brands.edit',
'invoice-resources', 'invoice-resources.add', 'invoice-resources.edit', 'invoice-settings', 'estimate-settings', 'subscriptions', 
'subscriptions.add', 'subscriptions.edit', 'quotes', 'quotes.add', 'quotes.edit', 'reports', 'opening-balance', 'email-logs', 'expected-expenses', 
'expected-expenses.edit', 'pending-expense','projects']))
<div class="sidebar" id="sidebar">
	<div class="sidebar-inner slimscroll">
		<div id="sidebar-menu" class="sidebar-menu">
			<ul>
				<li class="menu-title"> 
					<!-- <span>Main</span> -->
				</li>
				<li class="{{ Route::is('dashboard') ? 'active' : '' }}"> 
					<a href="{{ route('dashboard') }}"><i class="la la-dashboard"></i> <span>Dashboard</span></a>
				</li>
				@can('client-list')
				<li class="submenu">
					<a href="{{route('clients')}}"><i class="la la-users"></i> <span> Clients</span> <span class="menu-arrow"></span></a>
					<ul style="display: none;">
						<li>
							<a class="{{ Request::is('clients') ? 'active' : '' }}"  href="{{ route('clients') }}">
								All Clients
							</a>
						</li>
						@can('client-create')
						<li>
							<a class="{{ Route::is('clients.add', 'clients.edit') ? 'active' : '' }}"  href="{{ route('clients.add') }}">
								{{ Route::currentRouteName() == 'clients.edit' ? 'Edit Client' : 'Add Client' }}
							</a>
						</li>
						@endcan
					</ul>
				</li>
				@endcan
				@can('supplier-list')
				<li class="submenu">
					<a href="{{route('suppliers')}}"><i class="la la-users"></i> <span> Suppliers</span> <span class="menu-arrow"></span></a>
					<ul style="display: none;">
						<li>
							<a class="{{ Request::is('suppliers') ? 'active' : '' }}"  href="{{ route('suppliers') }}">
								All Suppliers
							</a>
						</li>
						@can('supplier-create')
						<li>
							<a class="{{ Route::is('suppliers.add', 'suppliers.edit') ? 'active' : '' }}"  href="{{ route('suppliers.add') }}">
								{{ Route::currentRouteName() == 'suppliers.edit' ? 'Edit Supplier' : 'Add Supplier' }}
							</a>
						</li>
						@endcan
					</ul>
				</li>
				@endcan

				@can('user-list')
				<li class="submenu">
					<a href="{{route('users')}}"><i class="la la-user-plus"></i> <span> Users</span> <span class="menu-arrow"></span></a>
					<ul style="display: none;">
						<li>
							<a class="{{ Request::is('users') ? 'active' : '' }}"  href="{{ route('users') }}">
								All Users
							</a>
						</li>
						@can('user-create')
						<li>
							<a class="{{ Route::is('users.add', 'users.edit') ? 'active' : '' }}"  href="{{ route('users.add') }}">
								{{ Route::currentRouteName() == 'users.edit' ? 'Edit User' : 'Add User' }}
							</a>
						</li>
						@endcan
					</ul>
				</li>
				@endcan
				@can('product-list')
				<li class="submenu">
					<a href="{{route('products')}}"><i class="la la-object-ungroup"></i> <span> Products</span> <span class="menu-arrow"></span></a>
					<ul style="display: none;">
						<li>
							<a class="{{ Request::is('products') ? 'active' : '' }}"  href="{{ route('products') }}">
								All Products
							</a>
						</li>
						@can('product-create')
						<li>
							<a class="{{ Route::is('products.add', 'products.edit') ? 'active' : '' }}"  href="{{ route('products.add') }}">
								{{ Route::currentRouteName() == 'products.edit' ? 'Edit Product' : 'Add Product' }}
							</a>
						</li>
						@endcan
					</ul>
				</li>
				@endcan
				@can('expense-list')
				<li class="submenu">
					<a href="{{route('expenses')}}"><i class="la la-pie-chart"></i> <span> Expenses</span> <span class="menu-arrow"></span></a>
					<ul style="display: none;">
						@can('expense-list')
						<li>
							<a class="{{ Request::is('expenses') ? 'active' : '' }}"  href="{{ route('expenses') }}">
								All Expenses
							</a>
						</li>
						@endcan
						@can('expense-create')
						<li>
							<a class="{{ Route::is('expenses.add', 'expenses.edit') ? 'active' : '' }}"  href="{{ route('expenses.add') }}">
								{{ Route::currentRouteName() == 'expenses.edit' ? 'Edit Expense' : 'Add Expense' }}
							</a>
						</li>
						@endcan
					</ul>
				</li>
				@endcan
<!--				<li class="{{ Route::is('pending-expense') ? 'active' : '' }}"> 
					<a href="{{ route('pending-expense') }}"><i class="la la-pie-chart"></i> <span>Pending Expense</span></a>
				</li>-->
				@can('expected-expense-list')
				<li class="{{ Route::is('expected-expenses') ? 'active' : '' }}"> 
					<a href="{{ route('expected-expenses') }}"><i class="la la-pie-chart"></i> <span>Expected Expenses</span></a>
				</li>
				@endcan
				@can('invoice-list')
				<li class="submenu">
					<a href="{{route('invoices')}}"><i class="la la-file-invoice"></i> <span> Invoices</span> <span class="menu-arrow"></span></a>
					<ul style="display: none;">
						@can('invoice-list')
						<li>
							<a class="{{ Request::is('invoices') ? 'active' : '' }}"  href="{{ route('invoices') }}">
								All Invoices
							</a>
						</li>
						@endcan
						@can('invoice-create')
						<li>
							<a class="{{ Route::is('invoices.add', 'invoices.edit') ? 'active' : '' }}"  href="{{ route('invoices.add') }}">
								{{ Route::currentRouteName() == 'invoices.edit' ? 'Edit Invoice' : 'Add Invoice' }}
							</a>
						</li>
						@endcan
					</ul>
				</li>
				@endcan
				@can('subscription-list')
				<li class="submenu">
					<a href="{{route('subscriptions')}}"><i class="la la-file-invoice"></i> <span> Subscriptions</span> <span class="menu-arrow"></span></a>
					<ul style="display: none;">
						@can('subscription-list')
						<li>
							<a class="{{ Request::is('subscriptions') ? 'active' : '' }}"  href="{{ route('subscriptions') }}">
								All Subscriptions
							</a>
						</li>
						@endcan
						@can('subscription-create')
						<li>
							<a class="{{ Route::is('subscriptions.add', 'subscriptions.edit') ? 'active' : '' }}"  href="{{ route('subscriptions.add') }}">
								{{ Route::currentRouteName() == 'subscriptions.edit' ? 'Edit Subscription' : 'Add Subscription' }}
							</a>
						</li>
						@endcan
					</ul>
				</li>
				@endcan
				@can('quote-list')
				<li class="submenu">
					<a href="{{route('quotes')}}"><i class="la la-file-invoice"></i> <span> Quotes</span> <span class="menu-arrow"></span></a>
					<ul style="display: none;">
						@can('quote-list')
						<li>
							<a class="{{ Request::is('quotes') ? 'active' : '' }}"  href="{{ route('quotes') }}">
								All Quotes
							</a>
						</li>
						@endcan
						@can('quote-create')
						<li>
							<a class="{{ Route::is('quotes.add', 'quotes.edit') ? 'active' : '' }}"  href="{{ route('quotes.add') }}">
								{{ Route::currentRouteName() == 'quotes.edit' ? 'Edit Quote' : 'Add Quote' }}
							</a>
						</li>
						@endcan
					</ul>
				</li>
				@endcan
				@can('lead-list')
				<li class="submenu">
					<a href="{{route('leads')}}"><i class="la la-user-secret"></i> <span> Leads</span> <span class="menu-arrow"></span></a>
					<ul style="display: none;">
						<li>
							<a class="{{ Request::is('leads') ? 'active' : '' }}"  href="{{ route('leads') }}">
								All Leads
							</a>
						</li>
						@can('lead-create')
						<li>
							<a class="{{ Route::is('leads.add', 'leads.edit') ? 'active' : '' }}"  href="{{ route('leads.add') }}">
								{{ Route::currentRouteName() == 'leads.edit' ? 'Edit Lead' : 'Add Lead' }}
							</a>
						</li>
						@endcan
					</ul>
				</li>
				@endcan
				@can('resource-list')
				<li class="submenu">
					<a href="{{route('invoice-resources')}}"><i class="la la-folder"></i> <span> Resources</span> <span class="menu-arrow"></span></a>
					<ul style="display: none;">
						<li>
							<a class="{{ Request::is('invoice-resources') ? 'active' : '' }}"  href="{{ route('invoice-resources') }}">
								All Resources
							</a>
						</li>
						@can('resource-create')
						<li>
							<a class="{{ Route::is('invoice-resources.add', 'invoice-resources.edit') ? 'active' : '' }}"  href="{{ route('invoice-resources.add') }}">
								{{ Route::currentRouteName() == 'invoice-resources.edit' ? 'Edit Resource' : 'Add Resource' }}
							</a>
						</li>
						@endcan
					</ul>
				</li>
				@endcan

				@can('role-list')
				<li class="{{ Route::is('roles') ? 'active' : '' }}"> 
					<a href="{{ route('roles') }}"><i class="la la-key"></i> <span>Roles & Permissions</span></a>
				</li>
				@endcan
				@can('report-list')
				<li class="{{ Route::is('reports') ? 'active' : '' }}"> 
					<a href="{{ route('reports') }}"><i class="la la-pie-chart"></i> <span>Reports</span></a>
				</li>
				@endcan
				@can('email-log-list')
				<li class="{{ Route::is('email-logs') ? 'active' : '' }}">
					<a href="{{ route('email-logs') }}"><i class="las la-history"></i> <span>EmailLogs</span></a>
				</li>
				@endcan
				@can('product-category-list', 'expense-category-list', 'department-list', 'payment-method-list', 'brand-list')
				<li class="submenu">
					<a href="#"><i class="la la-files-o"></i> <span> Masters </span> <span class="menu-arrow"></span></a>
					<ul style="display: none;">
						@can('product-category-list')
						<li>
							<a class="{{ Route::is('categories') ? 'active' : '' }}" href="{{ route('categories') }}">
								Product Categories
							</a>
						</li>
						@endcan
						@can('expense-category-list')
						<li>
							<a class="{{ Route::is('expense.categories') ? 'active' : '' }}" href="{{ route('expense.categories') }}">
								Expense Categories
							</a>
						</li>
						@endcan
						@can('department-list')
						<li>
							<a class="{{ Route::is('departments') ? 'active' : '' }}" href="{{ route('departments') }}">
								Departments
							</a>
						</li>
						@endcan
						@can('payment-method-list')
						<li>
							<a class="{{ Route::is('payment-methods') ? 'active' : '' }}" href="{{ route('payment-methods') }}">
								Payment Methods
							</a>
						</li>
						@endcan
						@can('payment-status-list')
						<li>
							<a class="{{ Route::is('payment-statuses') ? 'active' : '' }}" href="{{ route('payment-statuses') }}">
								Payment Status
							</a>
						</li>
						@endcan
						@can('brand-list')
						<li>
							<a class="{{ Route::is('brands','brands.add', 'brands.edit') ? 'active' : '' }}" href="{{ route('brands') }}">
								Brands
							</a>
						</li>
						@endcan
						@can('invoice-setting-list')
						<li>
							<a class="{{ Route::is('invoice-settings') ? 'active' : '' }}" href="{{ route('invoice-settings') }}">
								Invoice Settings
							</a>
						</li>
						@endcan
						@can('estimate-setting-list')
						<li>
							<a class="{{ Route::is('estimate-settings') ? 'active' : '' }}" href="{{ route('estimate-settings') }}">
								Estimate Settings
							</a>
						</li>
						@endcan
						@can('opening-balance-list')
						<li>
							<a class="{{ Route::is('opening-balance') ? 'active' : '' }}" href="{{ route('opening-balance') }}">
								Opening Balance
							</a>
						</li>
						@endcan
						@can('project-list')
						<li>
							<a class="{{ Route::is('projects') ? 'active' : '' }}" href="{{ route('projects') }}">
								Project List
							</a>
						</li>
						@endcan
					</ul>
				</li>
				@endcan
			</ul>
		</div>
	</div>
</div>
@endif
		

		
		
			
			