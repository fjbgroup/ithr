<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IT\ActivityController;
use App\Http\Controllers\IT\AssetClassController;
use App\Http\Controllers\IT\BrandController;
use App\Http\Controllers\IT\LocationController;
use App\Http\Controllers\IT\MasterdataController;
use App\Http\Controllers\IT\RequestController;
use App\Http\Controllers\IT\AssetController;
use App\Http\Controllers\IT\Auth\ChangePasswordController;
use App\Http\Controllers\IT\Auth\ForgotPasswordController;
use App\Http\Controllers\IT\Auth\LoginController;
use App\Http\Controllers\IT\Auth\LogoutController;
use App\Http\Controllers\IT\DashboardController;
use App\Http\Controllers\IT\DisposalController;
use App\Http\Controllers\IT\EmailSettingController;
use App\Http\Controllers\IT\EwasteController;
use App\Http\Controllers\IT\InventoryController;
use App\Http\Controllers\IT\ItRequestFormController;
use App\Http\Controllers\IT\NonItAssetController;
use App\Http\Controllers\IT\NotificationController;
use App\Http\Controllers\IT\ProfileController;
use App\Http\Controllers\IT\ReportController;
use App\Http\Controllers\IT\RoleMetricController;
use App\Http\Controllers\IT\UserController;
use App\Http\Controllers\IT\UserManualController;
use App\Http\Controllers\IT\WriteoffController;
use App\Http\Controllers\IT\WriteoffInventoryController;

Route::redirect('/it', '/it/login');

Route::prefix('it')->name('it.')->group(function () {

    Route::redirect('/', '/it/login');

    // Guest routes
    Route::middleware('it.guest')->group(function () {
        Route::get('/login',           [LoginController::class, 'showLogin'])->name('login');
        Route::post('/login',          [LoginController::class, 'login'])->name('login.submit');
        Route::post('/signup',         [LoginController::class, 'signup'])->name('signup');
        Route::get('/forgot-password', [ForgotPasswordController::class, 'showForm'])->name('password.forgot');
        Route::post('/forgot-password',[ForgotPasswordController::class, 'submit'])->name('password.forgot.submit');
    });

    // Logout
    Route::match(['get', 'post'], '/logout', [LogoutController::class, 'logout'])->name('logout')->middleware('it.auth');

    // Authenticated routes
    Route::middleware(['it.auth', 'it.active', 'it.session.timeout'])->group(function () {

        // Force password change
        Route::get('/password/change',  [ChangePasswordController::class, 'showForm'])->name('password.change');
        Route::post('/password/change', [ChangePasswordController::class, 'update'])->name('password.change.update');

        Route::middleware('it.must.change.pass')->group(function () {

            Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('/role-metric', [RoleMetricController::class, 'index'])->name('role-metric');
            Route::get('/user-manual', [UserManualController::class, 'index'])->name('user-manual.index');

            // IT Inventory
            Route::get('/inventory',                      [InventoryController::class, 'index'])->name('inventory.index');
            Route::post('/inventory',                     [InventoryController::class, 'store'])->name('inventory.store');
            Route::post('/inventory/bulk-delete',         [InventoryController::class, 'bulkDestroy'])->name('inventory.bulk-destroy');
            Route::post('/inventory/{id}',                [InventoryController::class, 'update'])->name('inventory.update');
            Route::delete('/inventory/{id}',              [InventoryController::class, 'destroy'])->name('inventory.destroy');
            Route::get('/inventory/template',             [InventoryController::class, 'importTemplate'])->name('inventory.template');
            Route::post('/inventory/import',              [InventoryController::class, 'import'])->name('inventory.import');
            Route::get('/inventory/search-suggestions',   [InventoryController::class, 'searchSuggestions'])->name('inventory.search-suggestions');
            Route::get('/inventory/descriptions',         [InventoryController::class, 'descriptionSuggestions'])->name('inventory.descriptions');
            Route::get('/inventory/brands',               [InventoryController::class, 'brandSuggestions'])->name('inventory.brands');
            Route::get('/inventory/models',               [InventoryController::class, 'modelSuggestions'])->name('inventory.models');

            // Asset detail
            Route::get('/asset/{id}', [AssetController::class, 'show'])->name('asset.show');

            // Non-IT Assets
            Route::get('/non-it-assets',                  [NonItAssetController::class, 'index'])->name('non-it.index');
            Route::post('/non-it-assets',                 [NonItAssetController::class, 'store'])->name('non-it.store');
            Route::get('/non-it-assets/suggestions',      [NonItAssetController::class, 'suggestions'])->name('non-it.suggestions');
            Route::get('/non-it-assets/import-template',  [NonItAssetController::class, 'importTemplate'])->name('non-it.import-template');
            Route::post('/non-it-assets/import-excel',    [NonItAssetController::class, 'importExcel'])->name('non-it.import-excel');
            Route::post('/non-it-assets/bulk-delete',     [NonItAssetController::class, 'bulkDestroy'])->name('non-it.bulk-destroy');
            Route::post('/non-it-assets/{id}',            [NonItAssetController::class, 'update'])->name('non-it.update');
            Route::delete('/non-it-assets/{id}',          [NonItAssetController::class, 'destroy'])->name('non-it.destroy');

            // Write-off
            Route::get('/writeoff',                       [WriteoffController::class, 'index'])->name('writeoff.index');
            Route::post('/writeoff/submit',               [WriteoffController::class, 'submitWriteoff'])->name('writeoff.submit');
            Route::post('/writeoff/hou-sign',             [WriteoffController::class, 'houSign'])->name('writeoff.hou-sign');
            Route::post('/writeoff/gm-sign',              [WriteoffController::class, 'gmSign'])->name('writeoff.gm-sign');
            Route::post('/writeoff/ceo-approve',          [WriteoffController::class, 'ceoApprove'])->name('writeoff.ceo-approve');
            Route::post('/writeoff/assign-hou',           [WriteoffController::class, 'assignToHOU'])->name('writeoff.assign-hou');
            Route::post('/writeoff/route-batch',          [WriteoffController::class, 'routeBatch'])->name('writeoff.route-batch');
            Route::post('/writeoff/{id}/reject',          [WriteoffController::class, 'rejectWriteoff'])->name('writeoff.reject');
            Route::post('/writeoff/{id}/approve',         [WriteoffController::class, 'approveWriteoff'])->name('writeoff.approve');
            Route::post('/writeoff/approve-all',          [WriteoffController::class, 'approveAllWriteoffs'])->name('writeoff.approve-all');
            Route::post('/writeoff/dismiss-batch',        [WriteoffController::class, 'dismissBatch'])->name('writeoff.dismiss-batch');
            Route::post('/writeoff/dismiss-all',          [WriteoffController::class, 'dismissAll'])->name('writeoff.dismiss-all');
            Route::get('/writeoff/{id}/report',           [WriteoffController::class, 'report'])->name('writeoff.report');

            // E-Waste
            Route::get('/ewaste',                         [EwasteController::class, 'index'])->name('ewaste.index');
            Route::get('/ewaste/autocomplete',            [EwasteController::class, 'autocomplete'])->name('ewaste.autocomplete');
            Route::get('/ewaste/collection-invoice',      [DisposalController::class, 'collectionInvoice'])->name('ewaste.collection-invoice');
            Route::post('/ewaste',                        [EwasteController::class, 'store'])->name('ewaste.store');
            Route::post('/ewaste/bulk',                   [EwasteController::class, 'bulk'])->name('ewaste.bulk');
            Route::get('/ewaste/import-template',         [EwasteController::class, 'importTemplate'])->name('ewaste.import-template');
            Route::post('/ewaste/import-excel',           [EwasteController::class, 'importExcel'])->name('ewaste.import-excel');
            Route::post('/ewaste/{id}',                   [EwasteController::class, 'update'])->name('ewaste.update');
            Route::delete('/ewaste/{id}',                 [EwasteController::class, 'destroy'])->name('ewaste.destroy');
            Route::post('/ewaste/{id}/collect',           [EwasteController::class, 'collect'])->name('ewaste.collect');
            Route::post('/ewaste/{id}/restore',           [EwasteController::class, 'restore'])->name('ewaste.restore');
            Route::post('/ewaste/{id}/uncollect',         [EwasteController::class, 'uncollect'])->name('ewaste.uncollect');

            // Disposal
            Route::get('/disposal',                       [DisposalController::class, 'index'])->name('disposal.index');
            Route::get('/disposal/autocomplete',          [DisposalController::class, 'autocomplete'])->name('disposal.autocomplete');
            Route::post('/disposal',                      [DisposalController::class, 'store'])->name('disposal.store');
            Route::post('/disposal/{id}/update',          [DisposalController::class, 'update'])->name('disposal.update');
            Route::get('/disposal/proofs',                [DisposalController::class, 'proofs'])->name('disposal.proofs');
            Route::get('/ewaste/collected-proofs',        [DisposalController::class, 'collected'])->name('ewaste.collected');
            Route::post('/disposal/{id}/disposed',        [DisposalController::class, 'markDisposed'])->name('disposal.disposed');
            Route::get('/disposal/import-template',       [DisposalController::class, 'importTemplate'])->name('disposal.import-template');
            Route::post('/disposal/import-excel',         [DisposalController::class, 'importExcel'])->name('disposal.import-excel');

            // IT Request Form
            Route::get('/it-request-form/drafts',              [ItRequestFormController::class, 'savedDrafts'])->name('it-request-form.drafts');
            Route::delete('/it-request-form/{id}/draft',       [ItRequestFormController::class, 'destroyDraft'])->name('it-request-form.draft.destroy');
            Route::get('/it-request-form',                     [ItRequestFormController::class, 'index'])->name('it-request-form');
            Route::post('/it-request-form',                    [ItRequestFormController::class, 'store'])->name('it-request-form.store');
            Route::get('/it-request-form/{id}/edit',           [ItRequestFormController::class, 'edit'])->name('it-request-form.edit');
            Route::put('/it-request-form/{id}',                [ItRequestFormController::class, 'update'])->name('it-request-form.update');
            Route::get('/it-request-form/{id}/staff-view',         [ItRequestFormController::class, 'staffShow'])->name('it-request-form.staff-show');
            Route::get('/it-request-form/{id}/hou-view',           [ItRequestFormController::class, 'houShow'])->name('it-request-form.hou-show');
            Route::post('/it-request-form/{id}/hou-approve',       [ItRequestFormController::class, 'houApprove'])->name('it-request-form.hou-approve');
            Route::post('/it-request-form/{id}/hou-reject',        [ItRequestFormController::class, 'houReject'])->name('it-request-form.hou-reject');
            Route::get('/it-request-form/{id}/validator-view',     [ItRequestFormController::class, 'validatorShow'])->name('it-request-form.validator-show');
            Route::post('/it-request-form/{id}/validator-approve', [ItRequestFormController::class, 'validatorApprove'])->name('it-request-form.validator-approve');
            Route::post('/it-request-form/{id}/validator-reject',  [ItRequestFormController::class, 'validatorReject'])->name('it-request-form.validator-reject');
            Route::post('/it-request-form/clear-all-decided',     [ItRequestFormController::class, 'clearAllDecided'])->name('it-request-form.clear-all');
            Route::post('/it-request-form/{id}/archive',          [ItRequestFormController::class, 'archiveRequest'])->name('it-request-form.archive');
            Route::post('/it-request-form/{id}/unarchive',        [ItRequestFormController::class, 'unarchiveRequest'])->name('it-request-form.unarchive');
            Route::post('/it-request-form/bulk-hou-approve',        [ItRequestFormController::class, 'bulkHouApprove'])->name('it-request-form.bulk-hou-approve');
            Route::post('/it-request-form/bulk-hou-reject',         [ItRequestFormController::class, 'bulkHouReject'])->name('it-request-form.bulk-hou-reject');
            Route::post('/it-request-form/bulk-admin-approve',      [ItRequestFormController::class, 'bulkAdminApprove'])->name('it-request-form.bulk-admin-approve');
            Route::post('/it-request-form/bulk-admin-reject',       [ItRequestFormController::class, 'bulkAdminReject'])->name('it-request-form.bulk-admin-reject');
            Route::post('/it-request-form/bulk-admin-archive',      [ItRequestFormController::class, 'bulkAdminArchive'])->name('it-request-form.bulk-admin-archive');
            Route::post('/it-request-form/bulk-validator-approve',  [ItRequestFormController::class, 'bulkValidatorApprove'])->name('it-request-form.bulk-validator-approve');
            Route::post('/it-request-form/bulk-validator-reject',   [ItRequestFormController::class, 'bulkValidatorReject'])->name('it-request-form.bulk-validator-reject');

            // Profile
            Route::get('/profile',                        [ProfileController::class, 'index'])->name('profile');
            Route::post('/profile',                       [ProfileController::class, 'update'])->name('profile.update');
            Route::post('/profile/password',              [ProfileController::class, 'updatePassword'])->name('profile.password');
            Route::post('/profile/signature',             [ProfileController::class, 'updateSignature'])->name('profile.signature');
            Route::post('/profile/signature/clear',       [ProfileController::class, 'clearSignature'])->name('profile.signature.clear');
            Route::get('/profile/signature-image',        [ProfileController::class, 'serveSignature'])->name('profile.signature.image');

            // Notifications (AJAX)
            Route::get('/notifications/ajax',             [NotificationController::class, 'ajax'])->name('notifications.ajax');
            Route::post('/notifications/mark-read',       [NotificationController::class, 'markRead'])->name('notifications.mark-read');

            // Request retract
            Route::delete('/requests/add/{id}',    [RequestController::class, 'retractAdd'])->name('requests.add.retract');
            Route::delete('/requests/ewaste/{id}', [RequestController::class, 'retractEwaste'])->name('requests.ewaste.retract');
            Route::delete('/requests/delete/{id}', [RequestController::class, 'retractDelete'])->name('requests.delete.retract');

            // Email settings (read-only for all; write restricted to admin inside the admin group)
            Route::get('/email-settings', [EmailSettingController::class, 'index'])->name('email-settings.index');

            // Admin only
            Route::middleware('it.role:admin')->group(function () {
                Route::get('/users',                         [UserController::class, 'index'])->name('users.index');
                Route::get('/users/staff-search',            [UserController::class, 'staffSearch'])->name('users.staff-search');
                Route::post('/users',                        [UserController::class, 'store'])->name('users.store');
                Route::post('/users/{id}',                   [UserController::class, 'update'])->name('users.update');
                Route::delete('/users/{id}',                 [UserController::class, 'destroy'])->name('users.destroy');
                Route::post('/users/{id}/toggle',            [UserController::class, 'toggle'])->name('users.toggle');
                Route::post('/users/{id}/reset-password',    [UserController::class, 'resetPassword'])->name('users.reset-password');
                Route::post('/users/reset-requests/{id}/reject', [UserController::class, 'rejectReset'])->name('users.reject-reset');

                Route::post('/disposal/{id}/restore',        [DisposalController::class, 'restore'])->name('disposal.restore');
                Route::delete('/disposal/{id}',              [DisposalController::class, 'destroy'])->name('disposal.destroy');

                Route::get('/asset-classes',                 [AssetClassController::class, 'index'])->name('asset-classes.index');
                Route::post('/asset-classes',                [AssetClassController::class, 'store'])->name('asset-classes.store');
                Route::post('/asset-classes/{id}',           [AssetClassController::class, 'update'])->name('asset-classes.update');
                Route::delete('/asset-classes/{id}',         [AssetClassController::class, 'destroy'])->name('asset-classes.destroy');

                Route::get('/brands',                        [BrandController::class, 'index'])->name('brands.index');
                Route::post('/brands',                       [BrandController::class, 'store'])->name('brands.store');
                Route::post('/brands/{id}',                  [BrandController::class, 'update'])->name('brands.update');
                Route::delete('/brands/{id}',                [BrandController::class, 'destroy'])->name('brands.destroy');

                Route::get('/locations',                     [LocationController::class, 'index'])->name('locations.index');
                Route::post('/locations',                    [LocationController::class, 'store'])->name('locations.store');
                Route::post('/locations/{id}',               [LocationController::class, 'update'])->name('locations.update');
                Route::delete('/locations/{id}',             [LocationController::class, 'destroy'])->name('locations.destroy');

                Route::post('/requests/add/{id}/approve',    [RequestController::class, 'approveAdd'])->name('requests.add.approve');
                Route::post('/requests/add/{id}/reject',     [RequestController::class, 'rejectAdd'])->name('requests.add.reject');
                Route::post('/requests/edit/{id}/approve',   [RequestController::class, 'approveEdit'])->name('requests.edit.approve');
                Route::post('/requests/edit/{id}/reject',    [RequestController::class, 'rejectEdit'])->name('requests.edit.reject');
                Route::post('/requests/delete/{id}/approve', [RequestController::class, 'approveDelete'])->name('requests.delete.approve');
                Route::post('/requests/delete/{id}/reject',  [RequestController::class, 'rejectDelete'])->name('requests.delete.reject');
                Route::post('/requests/ewaste/{id}/approve', [RequestController::class, 'approveEwaste'])->name('requests.ewaste.approve');
                Route::post('/requests/ewaste/{id}/reject',  [RequestController::class, 'rejectEwaste'])->name('requests.ewaste.reject');

                Route::get('/activity',                      [ActivityController::class, 'index'])->name('activity.index');

                Route::post('/email-settings',               [EmailSettingController::class, 'update'])->name('email-settings.update');
                Route::post('/email-settings/test',          [EmailSettingController::class, 'testEmail'])->name('email-settings.test');

                Route::get('/it-request-form/{id}',              [ItRequestFormController::class, 'show'])->name('it-request-form.show');
                Route::post('/it-request-form/{id}/approve',     [ItRequestFormController::class, 'approve'])->name('it-request-form.approve');
                Route::post('/it-request-form/{id}/reject',      [ItRequestFormController::class, 'reject'])->name('it-request-form.reject');
                Route::post('/it-request-form/{id}/request-update', [ItRequestFormController::class, 'requestUpdate'])->name('it-request-form.request-update');
            });

            // Admin + Finance
            Route::middleware('it.role:admin,finance_admin')->group(function () {
                Route::get('/masterdata',       [MasterdataController::class, 'index'])->name('masterdata.index');
                Route::get('/reports/it',         [ReportController::class, 'it'])->name('reports.it');
                Route::get('/reports/it/export',  [ReportController::class, 'exportIt'])->name('reports.it.export');
                Route::get('/reports/non-it',     [ReportController::class, 'nonIt'])->name('reports.non-it');
                Route::get('/reports/non-it/export', [ReportController::class, 'exportNonIt'])->name('reports.non-it.export');
            });

            // Finance Admin only
            Route::middleware('it.role:finance_admin')->group(function () {
                Route::get('/writeoff-inventory',                                      [WriteoffInventoryController::class, 'index'])->name('writeoff-inventory.index');
                Route::get('/writeoff-inventory/batch/{batchId}/route-ewaste',         [WriteoffInventoryController::class, 'routeEwasteBatch'])->name('writeoff-inventory.batch-route-ewaste');
                Route::get('/writeoff-inventory/batch/{batchId}/route-disposal',       [WriteoffInventoryController::class, 'routeDisposalBatch'])->name('writeoff-inventory.batch-route-disposal');
                Route::get('/writeoff-inventory/{id}/route-ewaste',                    [WriteoffInventoryController::class, 'routeEwaste'])->name('writeoff-inventory.route-ewaste');
                Route::get('/writeoff-inventory/{id}/route-disposal',                  [WriteoffInventoryController::class, 'routeDisposal'])->name('writeoff-inventory.route-disposal');
            });
        });
    });
});
