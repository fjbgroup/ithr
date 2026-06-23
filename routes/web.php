<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomBookingController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\TrainingController;
use App\Http\Controllers\FamilyController;
use App\Http\Controllers\TravelController;
use App\Http\Controllers\UpdateRequestController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\IRController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ChatbotController;

Route::get('/', [\App\Http\Controllers\WelcomeController::class, 'index'])->name('home');
Route::get('/hr', [RoomController::class, 'landing'])->name('hr.home');
Route::get('/rooms', [RoomController::class, 'index'])->name('rooms.home');

Route::post('rooms/bookings', [RoomBookingController::class, 'store'])->name('rooms.bookings.store');
Route::get('rooms/bookings/poll', [RoomController::class, 'pollBookings'])->name('rooms.bookings.poll');
Route::post('rooms/bookings/hold', [RoomBookingController::class, 'holdGuestBooking'])->name('rooms.bookings.hold');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login']);

    // Microsoft Authenticator (TOTP) challenge after correct credentials
    Route::get('two-factor', [AuthController::class, 'showTwoFactor'])->name('login.2fa');
    Route::post('two-factor', [AuthController::class, 'verifyTwoFactor'])->name('login.2fa.verify');

    // Forgot Password OTP flow
    Route::get('forgot-password', [AuthController::class, 'showForgot'])->name('password.request');
    Route::post('forgot-password', [AuthController::class, 'requestOtp'])->name('password.otp.request');
    Route::post('verify-otp', [AuthController::class, 'verifyOtp'])->name('password.otp.verify');
    Route::post('reset-password', [AuthController::class, 'resetPassword'])->name('password.otp.reset');
    Route::get('restart-forgot', [AuthController::class, 'restartForgot'])->name('password.otp.restart');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    Route::prefix('rooms')->name('rooms.')->group(function () {
        Route::get('/', [RoomController::class, 'index'])->name('index');
        Route::post('/', [RoomController::class, 'store'])->name('store');
        Route::put('/{room}', [RoomController::class, 'update'])->name('update');
        Route::delete('/{room}', [RoomController::class, 'destroy'])->name('destroy');
        
        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/pending', [RoomBookingController::class, 'pending'])->name('pending');
            Route::get('/process-pending', [RoomBookingController::class, 'processPendingBooking'])->name('process-pending');
            Route::post('/{booking}/cancel-request', [RoomBookingController::class, 'cancelRequest'])->name('cancel-request');
            Route::post('/{booking}/approve', [RoomBookingController::class, 'approve'])->name('approve');
            Route::post('/{booking}/reject', [RoomBookingController::class, 'reject'])->name('reject');
            Route::post('/{booking}/approve-cancel', [RoomBookingController::class, 'approveCancel'])->name('approve-cancel');
            Route::post('/{booking}/reject-cancel', [RoomBookingController::class, 'rejectCancel'])->name('reject-cancel');
            Route::post('/{booking}/approve-edit', [RoomBookingController::class, 'approveEdit'])->name('approve-edit');
            Route::post('/{booking}/reject-edit', [RoomBookingController::class, 'rejectEdit'])->name('reject-edit');
            Route::put('/{booking}', [RoomBookingController::class, 'update'])->name('update');
        });
    });

    Route::get('archived-staff', [StaffController::class, 'archivedStaff'])->name('archived-staff.index')->middleware('role:admin_it,admin_hr,ceo');
    Route::get('staff/generate-id', [StaffController::class, 'generateStaffId'])->name('staff.generateId');
    Route::post('staff/bulk', [StaffController::class, 'bulkStore'])->name('staff.bulkStore');
    Route::post('staff/bulk-delete', [StaffController::class, 'bulkDestroy'])->name('staff.bulkDestroy');
    Route::post('staff/import-preview', [StaffController::class, 'importPreview'])->name('staff.import-preview');
    Route::get('staff/import-template', [StaffController::class, 'downloadTemplate'])->name('staff.import-template');
    Route::post('staff/import', [StaffController::class, 'import'])->name('staff.import');
    Route::resource('staff', StaffController::class);

    Route::prefix('training')->name('training.')->group(function () {
        Route::get('import', [TrainingController::class, 'importPage'])->name('import-page');
        Route::get('import-template', [TrainingController::class, 'downloadTemplate'])->name('import-template');
        Route::post('import', [TrainingController::class, 'import'])->name('import');
        Route::post('delete-all', [TrainingController::class, 'deleteAll'])->name('delete-all');
        Route::post('delete-by-type', [TrainingController::class, 'deleteByType'])->name('delete-by-type');
        Route::post('courses', [TrainingController::class, 'storeCourse'])->name('courses.store');
        Route::put('courses/{course}', [TrainingController::class, 'updateCourse'])->name('courses.update');
        Route::post('attendance', [TrainingController::class, 'storeAttendance'])->name('attendance.store');
        Route::get('{course}/qr',           [TrainingController::class, 'qrPage'])->name('qr.page');
        Route::post('{course}/qr/generate', [TrainingController::class, 'qrGenerate'])->name('qr.generate');
        Route::get('{course}/report-export', [TrainingController::class, 'courseExport'])->name('course.export');
        Route::get('scan/{token}',          [TrainingController::class, 'qrScan'])->name('qr.scan');
        Route::post('scan/{token}',         [TrainingController::class, 'qrSubmit'])->name('qr.submit');
        Route::get('{id}/projector',        [AttendanceController::class, 'showProjector'])
            ->middleware('role:admin_it,admin_hr,ceo')
            ->name('projector');
        Route::get('{id}/refresh-token',    [AttendanceController::class, 'refreshProjectorToken'])
            ->middleware('role:admin_it,admin_hr,ceo')
            ->name('refresh-token');
    });
    Route::get('training', [TrainingController::class, 'index'])->name('training.index');

    Route::get('notifications/list', [NotificationController::class, 'list'])->name('notifications.list');
    Route::get('notifications/count', [NotificationController::class, 'unreadCount'])->name('notifications.count');
    Route::post('notifications/mark-read', [NotificationController::class, 'markRead'])->name('notifications.mark-read');

    Route::get('report', [ReportController::class, 'staffReport'])->name('report');
    Route::get('report/export', [ReportController::class, 'staffExport'])->name('report.export');
    Route::get('report/company-staff/{company}', [ReportController::class, 'companyStaffList'])->name('report.company-staff');
    Route::get('training-report', [ReportController::class, 'trainingReport'])->name('training-report');
    Route::get('training-report/export', [ReportController::class, 'trainingExport'])->name('report.training.export');

    Route::post('family/import-preview', [FamilyController::class, 'importPreview'])->name('family.import-preview');
    Route::get('family/import-template', [FamilyController::class, 'downloadTemplate'])->name('family.import-template');
    Route::post('family/import', [FamilyController::class, 'import'])->name('family.import');
    Route::post('family/bulk-destroy', [FamilyController::class, 'bulkDestroy'])->name('family.bulk-destroy');
    Route::resource('family', FamilyController::class);
    Route::resource('travel', TravelController::class);
    Route::get('my-requests', [UpdateRequestController::class, 'myRequests'])->name('my-requests');
    Route::post('requests/{update_request}/resolve', [UpdateRequestController::class, 'resolve'])->name('requests.resolve');
    Route::post('requests/{update_request}/dismiss', [UpdateRequestController::class, 'dismiss'])->name('requests.dismiss');
    Route::resource('requests', UpdateRequestController::class);
    Route::resource('master-data', MasterDataController::class);
    Route::get('master-data/staff-list/{deptId}', [MasterDataController::class, 'staffList'])->name('master-data.staff-list');
    Route::get('account/security',      [UserController::class, 'accountSecurity'])->name('account.security');
    Route::get('account/totp/setup',    [UserController::class, 'totpSetup'])  ->name('totp.setup');
    Route::post('account/totp/confirm', [UserController::class, 'totpConfirm'])->name('totp.confirm');
    Route::post('account/totp/remove',  [UserController::class, 'totpRemove']) ->name('totp.remove');
    Route::get('users/search-staff', [UserController::class, 'searchStaff'])->name('users.search_staff');
    Route::post('system/email-toggle', [UserController::class, 'toggleEmailSending'])->name('system.email.toggle')->middleware('role:admin_it');
    Route::patch('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggle_active');
    Route::patch('users/{user}/toggle-staff-status', [UserController::class, 'toggleStaffStatus'])->name('users.toggle_staff_status');
    Route::resource('users', UserController::class);
    Route::resource('ir', IRController::class)->middleware('role:admin_it,admin_hr,ceo');

    // Admin routes
    Route::middleware('role:admin_it,admin_hr')->group(function () {
        // Additional admin specific routes if needed
    });

    Route::middleware('role:admin_it,ceo')->group(function () {
        Route::get('audit-log', [AuditLogController::class, 'index'])->name('audit-log.index');
    });

    Route::post('chatbot/message', [ChatbotController::class, 'chat'])->name('chatbot.message');
    Route::post('chatbot/clear', [ChatbotController::class, 'clearHistory'])->name('chatbot.clear');
});

// QR attendance — public (no auth session required; credentials submitted in the form)
Route::get('attendance/scan',         [AttendanceController::class, 'scan'])->name('attendance.scan');
Route::get('attendance/verify/{id}',  [AttendanceController::class, 'verify'])->name('attendance.verify');
Route::post('attendance/verify/{id}', [AttendanceController::class, 'verifySubmit'])->name('attendance.verify.submit');
Route::get('attendance/success/{id}', [AttendanceController::class, 'success'])->name('attendance.success');
Route::post('attendance/feedback/{id}', [AttendanceController::class, 'feedbackStore'])->name('attendance.feedback.submit');
