<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WT\AuthController;
use App\Http\Controllers\WT\AdminGeneralController;
use App\Http\Controllers\WT\AdminITController;
use App\Http\Controllers\WT\MasterDataController;
use App\Http\Controllers\WT\NotificationController;
use App\Http\Controllers\WT\WalkieTalkieController;
use App\Http\Controllers\WT\MaintenanceController;
use App\Http\Controllers\WT\ReportController;
use App\Http\Controllers\WT\ActivityLogController;
use App\Http\Controllers\WT\AdminDashboardController;
use App\Http\Controllers\WT\UserDashboardController;
use App\Http\Controllers\WT\Admin\RequestController;
use App\Http\Controllers\WT\Admin\InterfaceSwitchController;
use App\Http\Controllers\WT\Admin\DatabaseBackupController;
use App\Http\Controllers\WT\User\InteractionController;
use App\Http\Controllers\WT\User\HandoverController;

Route::prefix('wt')->name('wt.')->group(function () {

    Route::middleware('wt.guest')->group(function () {
        Route::get('/', [AuthController::class, 'showLoginForm'])->name('login');
        Route::post('/', [AuthController::class, 'login']);
        Route::post('/forgot-password', [AuthController::class, 'resetPassword'])->name('password.reset');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('wt.auth');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.read_all')->middleware('wt.auth');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read')->middleware('wt.auth');
    Route::get('/switch-view/{role}', [InterfaceSwitchController::class, 'switch'])->name('switch_view')->middleware('wt.auth');
    Route::post('/switch-executive-account', [InterfaceSwitchController::class, 'impersonateExecutive'])->name('switch_executive_account')->middleware('wt.auth');
    Route::post('/return-to-ict-account', [InterfaceSwitchController::class, 'stopImpersonating'])->name('return_to_ict_account')->middleware('wt.auth');

    // Authenticated routes — ICT-only sub-sections are further guarded by wt.role:admin_it
    Route::middleware(['wt.auth'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->middleware('wt.role:admin_it,admin')
            ->name('dashboard');

        // Walkie Talkie Management
        Route::get('/repair-faulty', [WalkieTalkieController::class, 'repairFaulty'])->name('walkies.repairFaulty');

        Route::middleware('wt.role:admin_it')->group(function () {
            Route::get('/walkies', [WalkieTalkieController::class, 'index'])->name('walkies.index');
            Route::get('/walkies/unused', [WalkieTalkieController::class, 'unused'])->name('walkies.unused');
            Route::get('/walkies/create', [WalkieTalkieController::class, 'create'])->name('walkies.create');
            Route::get('/walkies/create/unassigned', [WalkieTalkieController::class, 'createUnassigned'])->name('walkies.create.unassigned');
            Route::get('/walkies/create/special-use', [WalkieTalkieController::class, 'createSpecialUse'])->name('walkies.create.specialUse');
            Route::get('/walkies/create/duplicate', [WalkieTalkieController::class, 'createDuplicate'])->name('walkies.create.duplicate');
            Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
            Route::get('/maintenance/create', [MaintenanceController::class, 'create'])->name('maintenance.create');
            Route::get('/maintenance/{maintenance}/timeline', [WalkieTalkieController::class, 'timelineFromMaintenance'])->name('maintenance.timeline');
            Route::get('/maintenance/{maintenance}/edit', [MaintenanceController::class, 'edit'])->name('maintenance.edit');
            Route::get('/faulty-reports', [MaintenanceController::class, 'faultyReports'])->name('faultyReports.index');
            Route::post('/faulty-reports/{maintenance}/receive-wt', [MaintenanceController::class, 'receiveFaultyWalkie'])->name('faultyReports.receiveWt');
            Route::post('/faulty-reports/{maintenance}/return-original', [MaintenanceController::class, 'returnOriginalWalkie'])->name('faultyReports.returnOriginal');
            Route::patch('/faulty-reports/{maintenance}', [MaintenanceController::class, 'updateFaultyReport'])->name('faultyReports.update');
            Route::get('/duplicate-ids', [WalkieTalkieController::class, 'duplicateIds'])->name('walkies.duplicateIds');
            Route::get('/special-use', [WalkieTalkieController::class, 'specialUse'])->name('walkies.specialUse');
            Route::get('/walkies/{walkie}/timeline', [WalkieTalkieController::class, 'timeline'])->name('walkies.timeline');
            Route::get('/walkies/{walkie}/edit', [WalkieTalkieController::class, 'edit'])->name('walkies.edit');
        });

        Route::get('/reports/summary', [ReportController::class, 'summary'])->name('reports.summary');
        Route::get('/reports/faulty-3-months', [ReportController::class, 'faultyThreeMonths'])->middleware('wt.role:admin_it')->name('reports.faulty3Months');

        Route::get('/requests', [RequestController::class, 'index'])->name('requests.index');
        Route::get('/requests/history', [RequestController::class, 'history'])->middleware('wt.role:admin_it')->name('requests.history');
        Route::get('/requests/create', [RequestController::class, 'createMenu'])->name('requests.create');
        Route::get('/requests/staff-search', [RequestController::class, 'staffSearch'])->name('requests.staffSearch');
        Route::get('/requests/shared', [RequestController::class, 'createShared'])->name('requests.create.shared');
        Route::get('/requests/individual', fn () => redirect()->route('wt.admin.requests.create.shared'))->name('requests.create.individual');
        Route::get('/requests/temporary', [RequestController::class, 'createTemporaryShared'])->name('requests.create.temporary');
        Route::post('/requests/admin-store', [RequestController::class, 'store'])->name('requests.store');
        Route::post('/requests/temporary-store', [RequestController::class, 'storeTemporary'])->name('requests.store.temporary');
        Route::get('/handover', [HandoverController::class, 'index'])->middleware('wt.role:admin_it')->name('handover.index');
        Route::post('/handover', [HandoverController::class, 'store'])->name('handover.store');
        Route::get('/returns/create', [InteractionController::class, 'createReturn'])->name('returns.create');
        Route::get('/returns/search', [InteractionController::class, 'searchReturn'])->name('returns.search');
        Route::post('/returns', [InteractionController::class, 'storeReturn'])->name('returns.store');
        Route::get('/damages/create', [InteractionController::class, 'createDamage'])->name('damages.create');
        Route::get('/damages/new', [InteractionController::class, 'createDamageForm'])->name('damages.form');
        Route::get('/damages/view/{damage}', [InteractionController::class, 'showDamageRecord'])->name('damages.show');
        Route::post('/damages/{damage}/temporary-spare', [InteractionController::class, 'requestTemporarySpare'])->name('damages.temporarySpare');
        Route::get('/damages/{bucket}', [InteractionController::class, 'damageStatusPage'])->whereIn('bucket', ['pending', 'drafts', 'completed'])->name('damages.status');
        Route::post('/damages', [InteractionController::class, 'storeDamage'])->name('damages.store');
        Route::get('/manual', [UserDashboardController::class, 'manual'])->name('manual');
        Route::get('/policies', [UserDashboardController::class, 'policies'])->name('policies');

        Route::post('/requests/{id}/reject', [RequestController::class, 'reject'])->name('requests.reject');
        Route::post('/requests/{id}/confirm-return', [RequestController::class, 'confirmReturn'])->name('requests.confirmReturn');
        Route::post('/requests/{id}/forward-to-it', [RequestController::class, 'forwardToIT'])->name('requests.forwardToIT');
        Route::post('/damage-reports/{id}/forward-to-it', [RequestController::class, 'forwardDamageToIT'])->name('damageReports.forwardToIT');
        Route::post('/damage-reports/{id}/reject', [RequestController::class, 'rejectDamage'])->name('damageReports.reject');
        Route::get('/request-status', [RequestController::class, 'requestStatus'])->name('requests.status');
        Route::get('/all-status', [RequestController::class, 'allStatus'])->name('all.status');
        Route::get('/my-inventory', [WalkieTalkieController::class, 'myInventory'])->name('walkies.myInventory');

        Route::get('/general', [AdminGeneralController::class, 'index'])->name('general.index');
        Route::get('/profile', [AdminGeneralController::class, 'profile'])->name('profile');
        Route::post('/profile', [AdminGeneralController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/signature', [UserDashboardController::class, 'updateSignature'])->name('profile.signature');
        Route::post('/profile/signature/clear', [UserDashboardController::class, 'clearSignature'])->name('profile.signature.clear');
        Route::get('/profile/signature-image', [UserDashboardController::class, 'serveSignature'])->name('profile.signature.image');

        Route::middleware('wt.role:admin_it')->group(function () {
            Route::post('/maintenance', [MaintenanceController::class, 'store'])->name('maintenance.store');
            Route::patch('/maintenance/{maintenance}/update', [MaintenanceController::class, 'update'])->name('maintenance.update');
            Route::patch('/maintenance/{maintenance}/update-status', [MaintenanceController::class, 'updateStatus'])->name('maintenance.update.status');
            Route::delete('/maintenance/{maintenance}', [MaintenanceController::class, 'destroy'])->name('maintenance.destroy');
            Route::post('/walkies', [WalkieTalkieController::class, 'store'])->name('walkies.store');
            Route::post('/walkies/bulk-action', [WalkieTalkieController::class, 'bulkAction'])->name('walkies.bulkAction');
            Route::post('/walkies/{walkie}/update-meta', [WalkieTalkieController::class, 'updateMeta'])->name('walkies.updateMeta');
            Route::post('/walkies/{walkie}/update-returned', [WalkieTalkieController::class, 'updateReturned'])->name('walkies.update.returned');
            Route::post('/walkies/{walkie}/update-status', [WalkieTalkieController::class, 'updateStatus'])->name('walkies.update.status');
            Route::post('/walkies/{walkie}/update-change-done', [WalkieTalkieController::class, 'updateChangeDone'])->name('walkies.update.change_done');
            Route::delete('/walkies/{walkie}/force-delete', [WalkieTalkieController::class, 'forceDelete'])->name('walkies.forceDelete');
            Route::delete('/walkies/{walkie}', [WalkieTalkieController::class, 'destroy'])->name('walkies.destroy');
            Route::post('/walkies/import', [WalkieTalkieController::class, 'import'])->name('walkies.import');
            Route::post('/requests/{id}/approve', [RequestController::class, 'approve'])->name('requests.approve');
            Route::post('/damage-reports/{id}/approve', [RequestController::class, 'approveDamage'])->name('damageReports.approve');
            Route::get('/users', [AdminITController::class, 'users'])->name('users.index');
            Route::get('/users/staff-search', [AdminITController::class, 'staffSearch'])->name('users.staffSearch');
            Route::post('/users/{user}/update', [AdminITController::class, 'updateUser'])->name('users.update');
            Route::post('/users/{user}/reset-password', [AdminITController::class, 'resetUserPassword'])->name('users.resetPassword');
            Route::delete('/users/{user}', [AdminITController::class, 'destroyUser'])->name('users.destroy');
            Route::post('/password-reset-requests/{passwordResetRequest}/approve', [AdminITController::class, 'approvePasswordReset'])->name('passwordResetRequests.approve');
            Route::post('/password-reset-requests/{passwordResetRequest}/reject', [AdminITController::class, 'rejectPasswordReset'])->name('passwordResetRequests.reject');
            Route::post('/users/create-manager', [AdminITController::class, 'storeManager'])->name('users.storeManager');
            Route::get('/master-data', [MasterDataController::class, 'index'])->name('masterData.index');
            Route::get('/master-data/usage', [MasterDataController::class, 'usage'])->name('masterData.usage');
            Route::post('/master-data', [MasterDataController::class, 'store'])->name('masterData.store');
            Route::put('/master-data/{masterData}', [MasterDataController::class, 'update'])->name('masterData.update');
            Route::delete('/master-data/{masterData}', [MasterDataController::class, 'destroy'])->name('masterData.destroy');
            Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity.index');
            Route::get('/database-backup', [DatabaseBackupController::class, 'download'])->name('database.backup');
            Route::post('/policies', [UserDashboardController::class, 'updatePolicies'])->name('policies.update');
            Route::get('/it', [AdminITController::class, 'index'])->name('it.index');
        });
    });

    // User routes
    Route::middleware(['wt.auth', 'wt.role:user'])->prefix('user')->name('user.')->group(function () {
        Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');
        Route::get('/profile', [UserDashboardController::class, 'profile'])->name('profile');
        Route::post('/profile', [UserDashboardController::class, 'updateProfile'])->name('profile.update');
        Route::post('/profile/signature', [UserDashboardController::class, 'updateSignature'])->name('profile.signature');
        Route::post('/profile/signature/clear', [UserDashboardController::class, 'clearSignature'])->name('profile.signature.clear');
        Route::get('/profile/signature-image', [UserDashboardController::class, 'serveSignature'])->name('profile.signature.image');

        Route::get('/requests/create', [InteractionController::class, 'createRequest'])->name('requests.create');
        Route::post('/requests', [InteractionController::class, 'storeRequest'])->name('requests.store');
        Route::get('/request-status', [InteractionController::class, 'requestStatus'])->name('requests.status');

        Route::get('/handover', [HandoverController::class, 'index'])->name('handover.index');
        Route::post('/handover', [HandoverController::class, 'store'])->name('handover.store');

        Route::get('/returns/create', [InteractionController::class, 'createReturn'])->name('returns.create');
        Route::get('/returns/search', [InteractionController::class, 'searchReturn'])->name('returns.search');
        Route::post('/returns', [InteractionController::class, 'storeReturn'])->name('returns.store');

        Route::get('/damages/create', [InteractionController::class, 'createDamage'])->name('damages.create');
        Route::get('/damages/new', [InteractionController::class, 'createDamageForm'])->name('damages.form');
        Route::get('/damages/view/{damage}', [InteractionController::class, 'showDamageRecord'])->name('damages.show');
        Route::post('/damages/{damage}/temporary-spare', [InteractionController::class, 'requestTemporarySpare'])->name('damages.temporarySpare');
        Route::get('/damages/{bucket}', [InteractionController::class, 'damageStatusPage'])->whereIn('bucket', ['pending', 'drafts', 'completed'])->name('damages.status');
        Route::post('/damages', [InteractionController::class, 'storeDamage'])->name('damages.store');

        Route::get('/manual', [UserDashboardController::class, 'manual'])->name('manual');
        Route::get('/policies', [UserDashboardController::class, 'policies'])->name('policies');
    });
});
