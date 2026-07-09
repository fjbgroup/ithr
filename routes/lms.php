<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LMS\LmsDashboardController;
use App\Http\Controllers\LMS\LmsCourseController;
use App\Http\Controllers\LMS\LmsMaterialController;
use App\Http\Controllers\LMS\LmsLearnController;

Route::middleware(['auth'])->prefix('lms')->name('lms.')->group(function () {
    Route::get('/', [LmsDashboardController::class, 'index'])->name('dashboard');

    // Admin / PIC Course Management
    Route::resource('courses', LmsCourseController::class);
    Route::resource('courses.materials', LmsMaterialController::class)->except(['index', 'show']);

    // Learning Interface for Staff
    Route::get('learn/{course}', [LmsLearnController::class, 'show'])->name('learn.show');
    Route::post('learn/{course}/complete/{material}', [LmsLearnController::class, 'completeMaterial'])->name('learn.complete');
    Route::post('learn/{course}/quiz/{material}', [LmsLearnController::class, 'submitQuiz'])->name('learn.quiz.submit');
});
