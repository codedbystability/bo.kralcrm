<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {

    return view('welcome');
})->name('welcome');


Route::get('/test-permissions/{financierName}', function ($financierName) {
    $financier = \App\Models\Financier::where('username', $financierName)
        ->first();

    if (!$financier) return 'financier not found !';


    return response()->json([
        'financier' => $financierName,
        'status' => 200,
        'permissions' => $financier->getAllPermissions()
    ]);
});



Route::prefix('admin')->group(function () {

    Route::get('login', [\App\Http\Controllers\Admin\Auth\LoginController::class, 'loginUI'])->name('admin.loginUI');
    Route::get('logout', [\App\Http\Controllers\Admin\Auth\LoginController::class, 'logout'])->name('admin.logout');
    Route::post('login', [\App\Http\Controllers\Admin\Auth\LoginController::class, 'login'])->name('admin.login');

    // ADMIN ROUTES !
    Route::group(['middleware' => 'auth'], function () {

        Route::get('/home', [\App\Http\Controllers\Admin\HomeController::class, 'index'])->name('admin.home');

        Route::prefix('profile')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ProfileController::class, 'profile'])->name('admin.profile');
            Route::post('update', [\App\Http\Controllers\Admin\ProfileController::class, 'profileUpdate'])->name('admin.profile.update');
        });


        Route::prefix('transactions')->group(function () {
            Route::prefix('reports')->group(function () {
                Route::get('/', [\App\Http\Controllers\Admin\ReportController::class, 'index'])->name('admin.transactions.reports.index');
                Route::get('detail/{id}', [\App\Http\Controllers\Admin\ReportController::class, 'detail'])->name('admin.transactions.reports.detail');
                Route::any('filter', [\App\Http\Controllers\Admin\ReportController::class, 'filter'])->name('admin.transactions.reports.filter');
            });
        });

        Route::prefix('managers')->group(function () {
//                Route::get('/', [\App\Http\Controllers\Admin\NoteController::class, 'index'])->name('admin.managers.notes.index');
//                Route::post('store', [\App\Http\Controllers\Admin\NoteController::class, 'index'])->name('admin.managers.notes.store');
            Route::resource('notes', \App\Http\Controllers\Admin\NoteController::class, [
                'as' => 'admin.managers',
                'only' => ['index', 'store']
            ]);
        });


        Route::resource('financiers', \App\Http\Controllers\Admin\FinancierController::class, ['as' => 'admin']);
        Route::post('financiers/customers/store-customer', [\App\Http\Controllers\Admin\FinancierController::class, 'storeCustomer'])->name('admin.financiers.customer-store');
        Route::get('financiers/activate/{id}', [\App\Http\Controllers\Admin\FinancierController::class, 'activate'])->name('admin.financiers.activate');
        Route::get('financiers/customers/create/{id}', [\App\Http\Controllers\Admin\FinancierController::class, 'createCustomer'])->name('admin.financiers.customer-create');
        Route::get('financiers/customers/{id}', [\App\Http\Controllers\Admin\FinancierController::class, 'customers'])->name('admin.financiers.customers');
        Route::get('financiers/permissions/{id}', [\App\Http\Controllers\Admin\FinancierController::class, 'permissions'])->name('admin.financiers.permissions');
        Route::delete('destroy-customer/{id}', [\App\Http\Controllers\Admin\FinancierController::class, 'destroyCustomer'])->name('admin.financiers.customer-destroy');
        Route::resource('clients', \App\Http\Controllers\Admin\ClientController::class, ['as' => 'admin']);
    });

});

Route::prefix('client')->group(function () {
    Route::get('login', [\App\Http\Controllers\Client\Auth\LoginController::class, 'loginUI'])->name('client.loginUI');
    Route::get('logout', [\App\Http\Controllers\Client\Auth\LoginController::class, 'logout'])->name('client.logout');
    Route::post('login', [\App\Http\Controllers\Client\Auth\LoginController::class, 'login'])->name('client.login');

    // CLIENT ROUTES !
    Route::group(['middleware' => 'auth:client'], function () {
        Route::get('/home', [\App\Http\Controllers\Client\HomeController::class, 'index'])->name('client.home');
        Route::prefix('profile')->group(function () {
            Route::get('/', [\App\Http\Controllers\Client\ProfileController::class, 'profile'])->name('client.profile');
            Route::post('update', [\App\Http\Controllers\Client\ProfileController::class, 'profileUpdate'])->name('client.profile.update');
        });

        Route::prefix('transactions')->group(function () {
            Route::get('reports', [\App\Http\Controllers\Client\TransactionController::class, 'reports'])->name('client.transactions.reports.index');
            Route::get('detail/{id}', [\App\Http\Controllers\Client\TransactionController::class, 'reports'])->name('client.transactions.detail');
            Route::any('reports/filter', [\App\Http\Controllers\Client\TransactionController::class, 'filter'])->name('client.transactions.reports.filter');


            Route::prefix('reports')->group(function () {
                Route::get('/', [\App\Http\Controllers\Client\ReportController::class, 'index'])->name('client.transactions.reports.index');
                Route::get('detail/{id}', [\App\Http\Controllers\Client\ReportController::class, 'detail'])->name('client.transactions.reports.detail');
                Route::any('filter', [\App\Http\Controllers\Client\ReportController::class, 'filter'])->name('client.transactions.reports.filter');
                Route::post('get-websites', [\App\Http\Controllers\Client\ReportController::class, 'getWebsites'])->name('client.transactions.reports.get-websites');

            });
        });

        Route::resource('websites', \App\Http\Controllers\Client\WebsiteController::class, ['as' => 'client']);
    });
});

Route::prefix('financier')->group(function () {

    Route::get('login', [\App\Http\Controllers\Financier\Auth\LoginController::class, 'loginUI'])->name('financier.loginUI');
    Route::get('logout', [\App\Http\Controllers\Financier\Auth\LoginController::class, 'logout'])->name('financier.logout');
    Route::post('login', [\App\Http\Controllers\Financier\Auth\LoginController::class, 'login'])->name('financier.login');

    // CLIENT ROUTES !
    Route::group(['middleware' => 'auth:financier'], function () {
        Route::get('/home', [\App\Http\Controllers\Financier\HomeController::class, 'index'])->name('financier.home');
        Route::prefix('profile')->group(function () {
            Route::get('/', [\App\Http\Controllers\Financier\ProfileController::class, 'profile'])->name('financier.profile');
            Route::post('update', [\App\Http\Controllers\Financier\ProfileController::class, 'profileUpdate'])->name('financier.profile.update');
        });

        Route::resource('banks', \App\Http\Controllers\Financier\BankController::class, ['as' => 'financier']);
        Route::get('banks/passive/{id}', [\App\Http\Controllers\Financier\BankController::class, 'deactivate'])->name('financier.banks.passive');
        Route::resource('bank-accounts', \App\Http\Controllers\Financier\BankAccountController::class, ['as' => 'financier']);
        Route::post('/bank-accounts/filter',[ \App\Http\Controllers\Financier\BankAccountController::class,'filter']);
        Route::get('/bank-accounts/activate/{id}', [\App\Http\Controllers\Financier\BankAccountController::class, 'activate'])->name('financier.bank-accounts.activate');
        Route::resource('papara-accounts', \App\Http\Controllers\Financier\PaparaAccountController::class, ['as' => 'financier']);

        Route::get('notes/mark/{id}', [\App\Http\Controllers\Financier\NoteController::class, 'mark'])->name('financier.notes.mark');

        Route::prefix('transactions')->group(function () {

            Route::get('detail/{id}', [\App\Http\Controllers\Financier\TransactionController::class, 'detail'])
                ->name('financier.transactions.detail');
            Route::get('letclient/{id}', [\App\Http\Controllers\Financier\TransactionController::class, 'letclient'])
                ->name('financier.transactions.letclient');

            Route::post('add/note', [\App\Http\Controllers\Financier\TransactionController::class, 'addNote'])
                ->name('financier.transactions.add-note');
            Route::post('approve/{id}', [\App\Http\Controllers\Financier\TransactionController::class, 'approve'])
                ->name('financier.transactions.approve');
            Route::get('direct-approve/{id}', [\App\Http\Controllers\Financier\TransactionController::class, 'directApprove'])
                ->name('financier.transactions.direct-approve');
            Route::post('complete/{id}', [\App\Http\Controllers\Financier\TransactionController::class, 'complete'])
                ->name('financier.transactions.complete');
            Route::post('cancel', [\App\Http\Controllers\Financier\TransactionController::class, 'cancel'])
                ->name('financier.transactions.cancel');
            Route::any('assign/{id}', [\App\Http\Controllers\Financier\TransactionController::class, 'assign'])
                ->name('financier.transactions.assign');
            Route::post('bank-info/{id}', [\App\Http\Controllers\Financier\TransactionController::class, 'bankInfo'])
                ->name('financier.transactions.bank-info');
            Route::post('assign-complete/{id}', [\App\Http\Controllers\Financier\TransactionController::class, 'assignComplete'])
                ->name('financier.transactions.assign.complete');
            Route::post('oto-assign/{id}', [\App\Http\Controllers\Financier\TransactionController::class, 'otoAssign'])
                ->name('financier.transactions.oto-assign');


            Route::prefix('reports')->group(function () {
                Route::get('/', [\App\Http\Controllers\Financier\ReportController::class, 'index'])->name('financier.transactions.reports.index');
                Route::get('accounts', [\App\Http\Controllers\Financier\ReportController::class, 'accounts'])->name('financier.transactions.reports.accounts');
                Route::get('detail/{id}', [\App\Http\Controllers\Financier\ReportController::class, 'detail'])->name('financier.transactions.reports.detail');
                Route::any('filter', [\App\Http\Controllers\Financier\ReportController::class, 'filter'])->name('financier.transactions.reports.filter');
                Route::any('accounts-filter', [\App\Http\Controllers\Financier\ReportController::class, 'accountsFilter'])->name('financier.transactions.reports.accounts-filter');
                Route::post('get-websites', [\App\Http\Controllers\Financier\ReportController::class, 'getWebsites'])->name('financier.transactions.reports.get-websites');

            });

            Route::prefix('papara')->group(function () {

                Route::get('name', function (\Illuminate\Http\Request $request) {
                    dd($request->all());
                })->name('financier.transactions.papara.test');

                Route::get('waiting-withdraws', [\App\Http\Controllers\Financier\PaparaTransactionController::class, 'waitingWithdraws'])
                    ->name('financier.transactions.papara.waiting-withdraws');

                Route::get('waiting-deposits', [\App\Http\Controllers\Financier\PaparaTransactionController::class, 'waitingDeposits'])
                    ->name('financier.transactions.papara.waiting-deposits');

                Route::get('waiting-deposits-approve-checked', [\App\Http\Controllers\Financier\PaparaTransactionController::class, 'waitingDepositsApproveChecked'])
                    ->name('financier.transactions.papara.waiting-deposits-approve-checked');


                Route::get('approved-withdraws', [\App\Http\Controllers\Financier\PaparaTransactionController::class, 'approvedWithdraws'])
                    ->name('financier.transactions.papara.approved-withdraws');
                Route::get('approved-deposits', [\App\Http\Controllers\Financier\PaparaTransactionController::class, 'approvedDeposits'])
                    ->name('financier.transactions.papara.approved-deposits');

                Route::get('completed-withdraws', [\App\Http\Controllers\Financier\PaparaTransactionController::class, 'completedWithdraws'])
                    ->name('financier.transactions.papara.completed-withdraws');
                Route::get('completed-deposits', [\App\Http\Controllers\Financier\PaparaTransactionController::class, 'completedDeposits'])
                    ->name('financier.transactions.papara.completed-deposits');


                Route::get('cancelled-withdraws', [\App\Http\Controllers\Financier\PaparaTransactionController::class, 'cancelledWithdraws'])
                    ->name('financier.transactions.papara.cancelled-withdraws');
                Route::get('cancelled-deposits', [\App\Http\Controllers\Financier\PaparaTransactionController::class, 'cancelledDeposits'])
                    ->name('financier.transactions.papara.cancelled-deposits');

            });

            Route::prefix('havale')->group(function () {

                Route::get('waiting-withdraws', [\App\Http\Controllers\Financier\HavaleTransactionController::class, 'waitingWithdraws'])
                    ->name('financier.transactions.havale.waiting-withdraws');
                Route::get('waiting-deposits', [\App\Http\Controllers\Financier\HavaleTransactionController::class, 'waitingDeposits'])
                    ->name('financier.transactions.havale.waiting-deposits');


                Route::get('waiting-deposits-approve-checked', [\App\Http\Controllers\Financier\HavaleTransactionController::class, 'waitingDepositsApproveChecked'])
                    ->name('financier.transactions.havale.waiting-deposits-approve-checked');


                Route::get('approved-withdraws', [\App\Http\Controllers\Financier\HavaleTransactionController::class, 'approvedWithdraws'])
                    ->name('financier.transactions.havale.approved-withdraws');
                Route::get('approved-deposits', [\App\Http\Controllers\Financier\HavaleTransactionController::class, 'approvedDeposits'])
                    ->name('financier.transactions.havale.approved-deposits');

                Route::get('completed-withdraws', [\App\Http\Controllers\Financier\HavaleTransactionController::class, 'completedWithdraws'])
                    ->name('financier.transactions.havale.completed-withdraws');
                Route::get('completed-deposits', [\App\Http\Controllers\Financier\HavaleTransactionController::class, 'completedDeposits'])
                    ->name('financier.transactions.havale.completed-deposits');


                Route::get('cancelled-withdraws', [\App\Http\Controllers\Financier\HavaleTransactionController::class, 'cancelledWithdraws'])
                    ->name('financier.transactions.havale.cancelled-withdraws');
                Route::get('cancelled-deposits', [\App\Http\Controllers\Financier\HavaleTransactionController::class, 'cancelledDeposits'])
                    ->name('financier.transactions.havale.cancelled-deposits');


            });


        });
    });
});


Route::post('get-websites', [\App\Http\Controllers\Admin\ReportController::class, 'getWebsites'])->name('admin.transactions.reports.get-websites');
Route::post('get-bank-accounts', [\App\Http\Controllers\Admin\ReportController::class, 'getBankAccounts'])->name('admin.transactions.reports.get-bank-accounts');
