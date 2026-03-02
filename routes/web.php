<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\CustomAuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\TwoFAController;
use App\Http\Controllers\Auth\RoleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\Masters\CategoryController;
use App\Http\Controllers\Masters\ExpenseCategoryController;
use App\Http\Controllers\Masters\DepartmentController;
use App\Http\Controllers\Masters\PaymentMethodController;
use App\Http\Controllers\Masters\BrandController;
use App\Http\Controllers\Masters\PaymentStatusController;
use App\Http\Controllers\Masters\InvoiceSettingsController;
use App\Http\Controllers\Masters\EstimateSettingController;
use App\Http\Controllers\Masters\OpeningBalanceController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\InvoiceResourceController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\EmailLogController;
use App\Http\Controllers\ExpectedExpenseController;
use App\Http\Controllers\PendingExpenseController;
use App\Http\Controllers\Masters\ProjectController;

Route::get('/', [CustomAuthController::class, 'index'])->middleware('guest')->name('login');
Route::post('custom-login', [CustomAuthController::class, 'customLogin'])->name('login.custom'); 
//Route::get('registration', [CustomAuthController::class, 'registration'])->name('register-user');
Route::post('custom-registration', [CustomAuthController::class, 'customRegistration'])->name('register.custom'); 

Route::get('user/verify/{token}/{email}', [CustomAuthController::class, 'verifyUser'])->name('user.verify');
Route::get('leads/followup-reminder', [LeadController::class, 'leadFollowUpCron'])->name('leads.followup-reminder');
Route::get('forgot-password', [ForgotPasswordController::class, 'index'])->name('forgot-password');
Route::post('forgot-password-action', [ForgotPasswordController::class, 'forgotPasswordAction'])->name('forgot-password.action');
Route::get('reset-password/{token}/{email}',[ResetPasswordController::class, 'index'])->name('reset-password');
Route::post('reset-password-action',[ResetPasswordController::class, 'resetPasswordAction'])->name('reset-password.action');
Route::get('verify-account', [TwoFAController::class, 'index'])->name('verify-account');
Route::post('verify-account-action', [TwoFAController::class, 'store'])->name('verify-account.action');
Route::get('verify-account-resend', [TwoFAController::class, 'resend'])->name('verify-account.resend');
Route::get('subscriptions/recurring-subscription-cron', [SubscriptionController::class, 'recurringSubscriptionCron'])->name('subscriptions.recurring-subscription-cron');
Route::get('subscriptions/change-subscription-due-date', [SubscriptionController::class, 'changeSubscriptionDueDate'])->name('subscriptions.change-subscription-due-date');
Route::get('email-logs/delete-logs-cron', [EmailLogController::class, 'deleteOlderEmailLogs'])->name('email-logs.delete-logs-cron');

Route::group(['middleware' => ['auth']], function() {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('profile', [CustomAuthController::class, 'profile'])->name('profile');
    Route::post('profile-action', [CustomAuthController::class, 'profileAction'])->name('profile.action');
    Route::get('signout', [CustomAuthController::class, 'signOut'])->name('signout');

    Route::group(['prefix' => 'users'], function() {
        Route::get('/', [UserController::class, 'index'])->name('users');
        Route::get('/add', [UserController::class, 'addUser'])->name('users.add');
        Route::get('/edit/{id}', [UserController::class, 'addUser'])->name('users.edit');
        Route::post('/store/action', [UserController::class, 'addUserAction'])->name('users.add.action');
        Route::delete('/delete/{id}', [UserController::class, 'deleteUser'])->name('users.delete');
        Route::post('/delete-selected-records', [UserController::class, 'deleteSelectedUserRecords'])->name('users.delete-selected-records');
        Route::post('/import-users', [UserController::class, 'importUsers'])->name('users.import-users');
        Route::get('/export-users', [UserController::class, 'exportUsers'])->name('users.export-users');
    });

    Route::group(['prefix' => 'clients'], function() {
        Route::get('/', [ClientController::class, 'index'])->name('clients');
        Route::get('/add', [ClientController::class, 'addClient'])->name('clients.add');
        Route::get('/edit/{id}', [ClientController::class, 'addClient'])->name('clients.edit');
        Route::post('/store/action', [ClientController::class, 'addClientAction'])->name('clients.add.action');
        Route::delete('/delete/{id}', [ClientController::class, 'deleteClient'])->name('clients.delete');
        Route::post('/delete-selected-records', [ClientController::class, 'deleteSelectedClientRecords'])->name('clients.delete-selected-records');
        Route::get('/view-client-note/{id}', [ClientController::class, 'viewClientNote'])->name('clients.client-note');
        Route::post('/import-clients', [ClientController::class, 'importClients'])->name('clients.import-clients');
        Route::get('/export-clients', [ClientController::class, 'exportClients'])->name('clients.export-clients');
        Route::get('/export-clients-as-pdf', [ClientController::class, 'exportClientsAsPdf'])->name('clients.export-clients-pdf');
        Route::post('/import-temp-clients', [ClientController::class, 'importTemporaryClient'])->name('clients.import-temp-clients');

    });

    Route::group(['prefix' => 'suppliers'], function() {
        Route::get('/', [SupplierController::class, 'index'])->name('suppliers');
        Route::get('/add', [SupplierController::class, 'addSupplier'])->name('suppliers.add');
        Route::get('/edit/{id}', [SupplierController::class, 'addSupplier'])->name('suppliers.edit');
        Route::post('/store/action', [SupplierController::class, 'addSupplierAction'])->name('suppliers.add.action');
        Route::delete('/delete/{id}', [SupplierController::class, 'deleteSupplier'])->name('suppliers.delete');
        Route::post('/delete-selected-records', [SupplierController::class, 'deleteSelectedSupplierRecords'])->name('suppliers.delete-selected-records');
        Route::post('/import-suppliers', [SupplierController::class, 'importSuppliers'])->name('suppliers.import-suppliers');
        Route::get('/export-suppliers', [SupplierController::class, 'exportSuppliers'])->name('suppliers.export-suppliers');
        Route::get('/view-supplier-note/{id}', [SupplierController::class, 'viewSupplierNote'])->name('suppliers.suppllier-note');
        Route::post('/import-temp-suppliers', [SupplierController::class, 'importTemporarySupplier'])->name('suppliers.import-temp-suppliers');
    });

    Route::group(['prefix' => 'products'], function() {
        Route::get('/', [ProductController::class, 'index'])->name('products');
        Route::get('/add', [ProductController::class, 'addProduct'])->name('products.add');
        Route::get('/edit/{id}', [ProductController::class, 'addProduct'])->name('products.edit');
        Route::post('/store/action', [ProductController::class, 'addProductAction'])->name('products.add.action');
        Route::delete('/delete/{id}', [ProductController::class, 'deleteProduct'])->name('products.delete');
        Route::get('/delete-product-image/{id}', [ProductController::class, 'deleteProductImage'])->name('products.delete-product-image');
        Route::post('/delete-selected-records', [ProductController::class, 'deleteSelectedProductRecords'])->name('products.delete-selected-records');
        Route::post('/import-products', [ProductController::class, 'importProducts'])->name('products.import-products');
        Route::get('/export-products', [ProductController::class, 'exportProducts'])->name('products.export-products');
    });

    Route::group(['prefix' => 'expenses'], function() {
        Route::get('/', [ExpenseController::class, 'index'])->name('expenses');
        Route::get('/add', [ExpenseController::class, 'addExpense'])->name('expenses.add');
        Route::get('/edit/{id}', [ExpenseController::class, 'addExpense'])->name('expenses.edit');
        Route::post('/store/action', [ExpenseController::class, 'addExpenseAction'])->name('expenses.add.action');
        Route::delete('/delete/{id}', [ExpenseController::class, 'deleteExpense'])->name('expenses.delete');
        Route::get('/get-supplier-categories/{id}', [ExpenseController::class, 'getSupplierExpenseCategories'])->name('expenses.get-supplier-categories');
        Route::get('/delete-attached-image/{id}', [ExpenseController::class, 'deleteAttachedImage'])->name('expenses.delete-attached-image');
        Route::post('/delete-selected-records', [ExpenseController::class, 'deleteSelectedExpenseRecords'])->name('expenses.delete-selected-records');
        Route::get('/export-expenses', [ExpenseController::class, 'exportExpenses'])->name('expenses.export-expenses');
        Route::get('/export-sample-file', [ExpenseController::class, 'exportSampleExpenseFile'])->name('expenses.export-sample-file');
        Route::post('/import-expenses', [ExpenseController::class, 'importExpenses'])->name('expenses.import-expenses');
    });

    Route::group(['prefix' => 'expected-expenses'], function() {
        Route::get('/', [ExpectedExpenseController::class, 'index'])->name('expected-expenses');
        Route::post('/create', [ExpectedExpenseController::class, 'createExpectedExpense'])->name('expected-expenses.create');
        Route::get('/edit/{id}', [ExpectedExpenseController::class, 'editExpectedExpense'])->name('expected-expenses.edit');
        Route::post('/update', [ExpectedExpenseController::class, 'updateExpectedExpense'])->name('expected-expenses.update');
        Route::get('/clone-expected-expense/{id}', [ExpectedExpenseController::class, 'cloneExpectedExpense'])->name('expected-expenses.clone');    
        Route::delete('/delete/{id}', [ExpectedExpenseController::class, 'deleteExpectedExpense'])->name('expected-expenses.delete');
        Route::post('/delete-selected-records', [ExpectedExpenseController::class, 'deleteSelectedExpectedExpenseRecords'])->name('expected-expenses.delete-selected-records');
    });

    Route::group(['prefix' => 'invoices'], function() {
        Route::get('/', [InvoiceController::class, 'index'])->name('invoices');
        Route::get('/add', [InvoiceController::class, 'addInvoice'])->name('invoices.add');
        Route::get('/edit/{id}', [InvoiceController::class, 'addInvoice'])->name('invoices.edit');
        Route::get('/view/{id}',[InvoiceController::class, 'addInvoice'])->name('invoices.view');
        Route::post('/store/action', [InvoiceController::class, 'addInvoiceAction'])->name('invoices.add.action');
        Route::delete('/delete/{id}', [InvoiceController::class, 'deleteInvoice'])->name('invoices.delete');
        Route::get('/product-details/{id}', [InvoiceController::class, 'getProductDetails'])->name('invoices.product.details');
        Route::get('/clone-invoice/{id}', [InvoiceController::class, 'cloneInvoice'])->name('invoice.clone');      
        Route::post('/delete-selected-records', [InvoiceController::class, 'deleteSelectedInvoiceRecords'])->name('invoices.delete-selected-records');  
        Route::post('/invoice-due-date', [InvoiceController::class, 'getInvoiceDueDate'])->name('invoice.invoice-due-date');
        Route::get('/invoice/generate-pdf/{id}', [InvoiceController::class, 'generateInvoicePDF'])->name('invoices.download-invoice');
        Route::post('/change-payment_status', [InvoiceController::class, 'changeInvoicePaymentStatus'])->name('invoice.change-payment-status');
        Route::get('/view-invoice-note/{id}', [InvoiceController::class, 'viewInvoiceNote'])->name('invoices.invoice-note');
        Route::get('/export-invoices', [InvoiceController::class, 'exportInvoices'])->name('invoices.export-invoices');
        Route::get('/send-email-to-client/{id}', [InvoiceController::class, 'sendEmailToClient'])->name('invoices.send-email-to-client');
        Route::get('/export-invoice-by-status', [InvoiceController::class, 'exportInvoiceByStatus'])->name('invoices.export-invoice-by-status');
        Route::post('/edit-project', [InvoiceController::class, 'EditProject'])->name('invoice.edit-project');
    });

    Route::group(['prefix' => 'subscriptions'], function() {
        Route::get('/', [SubscriptionController::class, 'index'])->name('subscriptions');
        Route::get('/add', [SubscriptionController::class, 'addSubscription'])->name('subscriptions.add');
        Route::get('/edit/{id}', [SubscriptionController::class, 'addSubscription'])->name('subscriptions.edit');
        Route::post('/store/action', [SubscriptionController::class, 'addSubscriptionAction'])->name('subscriptions.add.action');
        Route::delete('/delete/{id}', [SubscriptionController::class, 'deleteSubscription'])->name('subscriptions.delete');
        Route::post('/delete-selected-records', [SubscriptionController::class, 'deleteSelectedSubscriptionRecords'])->name('subscriptions.delete-selected-records');  
        Route::get('/product-details/{id}', [SubscriptionController::class, 'getProductDetails'])->name('subscriptions.product.details'); 
        Route::post('/subscription-due-date', [SubscriptionController::class, 'getSubscriptionDueDate'])->name('subscriptions.invoice-due-date');
        Route::get('/export-subscriptions', [SubscriptionController::class, 'exportSubscriptions'])->name('subscriptions.export-subscriptions');
    });

    Route::group(['prefix' => 'quotes'], function() {
        Route::get('/', [QuoteController::class, 'index'])->name('quotes');
        Route::get('/add', [QuoteController::class, 'addQuote'])->name('quotes.add');
        Route::get('/edit/{id}', [QuoteController::class, 'addQuote'])->name('quotes.edit');
        Route::post('/store/action', [QuoteController::class, 'addQuoteAction'])->name('quotes.add.action');
        Route::delete('/delete/{id}', [QuoteController::class, 'deleteQuote'])->name('quotes.delete');
        Route::get('/product-details/{id}', [QuoteController::class, 'getProductDetails'])->name('quotes.product.details');
        Route::get('/clone-quote/{id}', [QuoteController::class, 'cloneQuote'])->name('quote.clone');      
        Route::post('/delete-selected-records', [QuoteController::class, 'deleteSelectedQuoteRecords'])->name('quotes.delete-selected-records');  
        Route::get('/quote/generate-pdf/{id}', [QuoteController::class, 'generateQuotePDF'])->name('quotes.download-quote');
        Route::get('/export-quotes', [QuoteController::class, 'exportQuotes'])->name('quotes.export-quotes');
        Route::get('/export-quotes-filter', [QuoteController::class, 'exportQuotesByOptions'])->name('quotes.export-quotes-filter');
    });

    Route::group(['prefix' => 'leads'], function() {
        Route::get('/', [LeadController::class, 'index'])->name('leads');
        Route::get('/add', [LeadController::class, 'addLead'])->name('leads.add');
        Route::get('/edit/{id}', [LeadController::class, 'addLead'])->name('leads.edit');
        Route::post('/store/action', [LeadController::class, 'addLeadAction'])->name('leads.add.action');
        Route::delete('/delete/{id}', [LeadController::class, 'deleteLead'])->name('leads.delete');
        Route::get('/followup-details/{id}', [LeadController::class, 'leadFollowUpDetails'])->name('leads.followup-details');
        Route::post('/delete-selected-records', [LeadController::class, 'deleteSelectedLeadRecords'])->name('leads.delete-selected-records');  
        Route::get('/export-leads', [LeadController::class, 'exportLeads'])->name('leads.export-leads');
    });

    Route::group(['prefix' => 'invoice-resources'], function() {
        Route::get('/', [InvoiceResourceController::class, 'index'])->name('invoice-resources');
        Route::get('/add', [InvoiceResourceController::class, 'addResource'])->name('invoice-resources.add');
        Route::get('/edit/{id}', [InvoiceResourceController::class, 'addResource'])->name('invoice-resources.edit');
        Route::post('/store/action', [InvoiceResourceController::class, 'addResourceAction'])->name('invoice-resources.add.action');
        Route::delete('/delete/{id}', [InvoiceResourceController::class, 'deleteResource'])->name('invoice-resources.delete');
        Route::get('/delete-resource-image/{id}', [InvoiceResourceController::class, 'deleteInvoiceResourceImage'])->name('invoice-resources.delete-invoice-resource-image');
        Route::post('/delete-selected-records', [InvoiceResourceController::class, 'deleteSelectedResourceRecords'])->name('invoice-resources.delete-selected-records');  
        Route::get('/download/{id}', [InvoiceResourceController::class, 'downloadInvoiceResources'])->name('invoice-resources.download');
    });

    Route::group(['prefix' => 'roles'], function() {
        Route::get('/', [RoleController::class, 'index'])->name('roles');
        Route::post('/create', [RoleController::class, 'createRole'])->name('roles.create');
        Route::get('/edit/{id}', [RoleController::class, 'editRole'])->name('roles.edit');
        Route::post('/update', [RoleController::class, 'updateRole'])->name('roles.update');
        Route::delete('/delete/{id}', [RoleController::class, 'deleteRole'])->name('roles.delete');
        Route::post('/delete-selected-records', [RoleController::class, 'deleteSelectedRoleRecords'])->name('roles.delete-selected-records');  
    });

    Route::group(['prefix' => 'reports'], function() {
        Route::get('/', [ReportController::class, 'index'])->name('reports');
        Route::get('/quater-comparison', [ReportController::class, 'quaterComparison'])->name('reports.quarter-comparison');
        Route::get('/quarter-report', [ReportController::class, 'quaterReport'])->name('reports.quarter-report');
        Route::get('/pnl-report', [ReportController::class, 'pnlReport'])->name('reports.pnl-report');
        Route::get('/forecast-report', [ReportController::class, 'forecastReport'])->name('reports.forecast-report');
        Route::get('/cashflow-report', [ReportController::class, 'cashflowReport'])->name('reports.cashflow-report');
        Route::get('/expected-expense-report', [ReportController::class, 'expectedExpenseReport'])->name('reports.expected-expense-report');
        Route::get('/project-report', [ReportController::class, 'projectReport'])->name('reports.project-report');
        Route::get('/expense-report', [ReportController::class, 'expenseReport'])->name('reports.expense-report');
        
    });

    Route::group(['prefix' => 'email-logs'], function() {
        Route::get('/', [EmailLogController::class, 'index'])->name('email-logs');
        Route::delete('/delete/{id}', [EmailLogController::class, 'deleteEmailLog'])->name('email-logs.delete');
        Route::post('/delete-selected-records', [EmailLogController::class, 'deleteSelectedEmailRecords'])->name('email-logs.delete-selected-records');
        Route::get('/export-email-logs', [EmailLogController::class, 'exportEmailLogs'])->name('email-logs.export');
    });

    Route::group(['prefix' => 'pending-expense'], function() {
        Route::get('/', [PendingExpenseController::class, 'index'])->name('pending-expense');
        Route::post('/import-pending-expense', [PendingExpenseController::class, 'importPendingExpense'])->name('pending-expense.import-pending-expense');
        Route::post('/confirm-pending-expense', [PendingExpenseController::class, 'confirmPendingExpense'])->name('pending-expense.confirm-pending-expense');
    });

    Route::group(['prefix' => 'master'], function() {
        Route::get('/product/categories', [CategoryController::class, 'index'])->name('categories');
        Route::post('/product/categories/create', [CategoryController::class, 'createCategory'])->name('categories.create');
        Route::get('product/categories/edit/{id}', [CategoryController::class, 'editCategory'])->name('categories.edit');
        Route::post('/product/categories/update', [CategoryController::class, 'updateCategory'])->name('categories.update');
        Route::delete('/product/categories/delete/{id}', [CategoryController::class, 'deleteCategory'])->name('categories.delete');
        Route::post('/product/categories/delete-selected-records', [CategoryController::class, 'deleteSelectedProductCategoryRecords'])->name('categories.delete-selected-records');  
        Route::post('/product/categories/import-product-categories', [CategoryController::class, 'importProductCategories'])->name('categories.import-categories');

        Route::get('/expense/categories', [ExpenseCategoryController::class, 'index'])->name('expense.categories');
        Route::post('/expense/categories/create', [ExpenseCategoryController::class, 'createCategory'])->name('expense.categories.create');
        Route::get('/expense/categories/edit/{id}', [ExpenseCategoryController::class, 'editCategory'])->name('expense.categories.edit');
        Route::post('/expense/categories/update', [ExpenseCategoryController::class, 'updateCategory'])->name('expense.categories.update');
        Route::delete('/expense/categories/delete/{id}', [ExpenseCategoryController::class, 'deleteCategory'])->name('expense.categories.delete');
        Route::post('/expense/categories/delete-selected-records', [ExpenseCategoryController::class, 'deleteSelectedExpenseCategoryRecords'])->name('expense.categories.delete-selected-records');  
        Route::post('/expense/categories/import-expense-categories', [ExpenseCategoryController::class, 'importExpenseCategories'])->name('expense.categories.import-categories');

        Route::get('/departments', [DepartmentController::class, 'index'])->name('departments');
        Route::post('/departments/create', [DepartmentController::class, 'createDepartment'])->name('departments.create');
        Route::get('/departments/edit/{id}', [DepartmentController::class, 'editDepartment'])->name('departments.edit');
        Route::post('/departments/update', [DepartmentController::class, 'updateDepartment'])->name('departments.update');
        Route::delete('/departments/delete/{id}', [DepartmentController::class, 'deleteDepartment'])->name('departments.delete');
        Route::post('/departments/delete-selected-records', [DepartmentController::class, 'deleteSelectedDepartmentRecords'])->name('departments.delete-selected-records');  
        Route::post('/departments/import-departments', [DepartmentController::class, 'importDepartments'])->name('departments.import-departments');

        Route::get('/payment-methods', [PaymentMethodController::class, 'index'])->name('payment-methods');
        Route::post('/payment-methods/create', [PaymentMethodController::class, 'createPaymentMethod'])->name('payment-methods.create');
        Route::get('/payment-methods/edit/{id}', [PaymentMethodController::class, 'editPaymentMethod'])->name('payment-methods.edit');
        Route::post('/payment-methods/update', [PaymentMethodController::class, 'updatePaymentMethod'])->name('payment-methods.update');
        Route::delete('/payment-methods/delete/{id}', [PaymentMethodController::class, 'deletePaymentMethod'])->name('payment-methods.delete');
        Route::post('/payment-methods/delete-selected-records', [PaymentMethodController::class, 'deleteSelectedPaymentMethodRecords'])->name('payment-methods.delete-selected-records');  
        Route::post('/payment-methods/import-payment-methods', [PaymentMethodController::class, 'importPaymetMethods'])->name('payment-methods.import-payment-method');

        Route::get('/brands', [BrandController::class, 'index'])->name('brands');
        Route::get('/brands/add', [BrandController::class, 'addBrand'])->name('brands.add');
        Route::get('/brands/edit/{id}', [BrandController::class, 'addBrand'])->name('brands.edit');
        Route::post('/brands/store/action', [BrandController::class, 'addBrandAction'])->name('brands.add.action');
        Route::delete('/brands/delete/{id}', [BrandController::class, 'deleteBrand'])->name('brands.delete');
        Route::get('/brands/delete-brand-logo/{id}', [BrandController::class, 'deleteBrandLogo'])->name('brands.delete-brand-logo');
        Route::post('/brands/delete-selected-records', [BrandController::class, 'deleteSelectedBrandRecords'])->name('brands.delete-selected-records');
       
        Route::get('/payment-statuses', [PaymentStatusController::class, 'index'])->name('payment-statuses');
        Route::post('/payment-statuses/create', [PaymentStatusController::class, 'createPaymentStatus'])->name('payment-statuses.create');
        Route::get('/payment-statuses/edit/{id}', [PaymentStatusController::class, 'editPaymentStatus'])->name('payment-statuses.edit');
        Route::post('/payment-statuses/update', [PaymentStatusController::class, 'updatePaymentStatus'])->name('payment-statuses.update');
        Route::delete('/payment-statuses/delete/{id}', [PaymentStatusController::class, 'deletePaymentStatus'])->name('payment-statuses.delete');
        Route::post('/payment-statuses/delete-selected-records', [PaymentStatusController::class, 'deleteSelectedPaymentStatusRecords'])->name('payment-statuses.delete-selected-records');  

        Route::get('/invoice-settings', [InvoiceSettingsController::class, 'index'])->name('invoice-settings');
        Route::post('/invoice-settings/save', [InvoiceSettingsController::class, 'saveInvoiceSettings'])->name('invoice-settings.save');

        Route::get('/estimate-settings', [EstimateSettingController::class, 'index'])->name('estimate-settings');
        Route::post('/estimate-settings/save', [EstimateSettingController::class, 'saveEstimateSettings'])->name('estimate-settings.save');

        Route::get('/opening-balance', [OpeningBalanceController::class, 'index'])->name('opening-balance');
        Route::post('/opening-balance/create', [OpeningBalanceController::class, 'createOpeningBalance'])->name('opening-balance.create');
        Route::get('/opening-balance/edit/{id}', [OpeningBalanceController::class, 'editOpeningBalance'])->name('opening-balance.edit');
        Route::post('/opening-balance/update', [OpeningBalanceController::class, 'updateOpeningBalance'])->name('opening-balance.update');
        Route::delete('/opening-balance/delete/{id}', [OpeningBalanceController::class, 'deleteOpeningBalance'])->name('opening-balance.delete');
        Route::post('/opening-balance/delete-selected-records', [OpeningBalanceController::class, 'deleteSelectedOpeningBalanceRecords'])->name('opening-balance.delete-selected-records');  

        Route::get('/projects', [ProjectController::class, 'index'])->name('projects');
        Route::post('/projects/create', [ProjectController::class, 'createProject'])->name('projects.create');
        Route::get('projects/edit/{id}', [ProjectController::class, 'editProject'])->name('projects.edit');
        Route::post('/projects/update', [ProjectController::class, 'updateProject'])->name('projects.update');
        Route::delete('/projects/delete/{id}', [ProjectController::class, 'deleteProject'])->name('projects.delete');
        Route::post('/projects/delete-selected-records', [ProjectController::class, 'deleteSelectedProjectRecords'])->name('projects.delete-selected-records');  
        Route::post('/projects/import-projects', [ProjectController::class, 'importProjects'])->name('projects.import-projects');
    });

    Route::get('/theme-settings', function () {
        return view('theme-settings');
    })->name('theme-settings');
    
    Route::get('/email-settings', function () {
        return view('email-settings');
    })->name('email-settings');
    
    Route::get('/change-password', function () {
        return view('change-password');
    })->name('change-password');
    
});

