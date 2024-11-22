<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\SchoolClassController;
use App\Http\Controllers\Api\SubjectController;
use App\Http\Controllers\Api\SemesterController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\ReactionController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\KeywordController;
use App\Http\Controllers\Api\ImageUploadController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\GradeOneController;
use App\Http\Controllers\Api\FrontendNewsController;
use App\Http\Controllers\Api\FilterController;
use App\Http\Controllers\Api\FileController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CalendarController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ForgotPasswordController;

// Open Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Forgot Password
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('api.password.email');

// General Upload Routes
Route::post('/upload-image', [ImageUploadController::class, 'upload'])->name('api.upload.image');
Route::post('/upload-file', [ImageUploadController::class, 'uploadFile'])->name('api.upload.file');

// Home Controller
Route::get('/', [HomeController::class, 'index']);
Route::get('/about', [HomeController::class, 'about']);
Route::get('/contact', [HomeController::class, 'contact']);

// Profile & Logout
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/profile', [AuthController::class, 'profile']);
    Route::get('/logout', [AuthController::class, 'logout']);
});

// Filter Routes
Route::get('/filter-files', [FilterController::class, 'index']);
Route::get('/subjects/{classId}', [FilterController::class, 'getSubjectsByClass']);
Route::get('/semesters/{subjectId}', [FilterController::class, 'getSemestersBySubject']);
Route::get('/files/{semesterId}', [FilterController::class, 'getFileTypesBySemester']);

// Database-Specific Routes (by prefix)
Route::prefix('{database}')->group(function () {
    Route::get('/news', [FrontendNewsController::class, 'index']);
    Route::get('/news/{id}', [FrontendNewsController::class, 'show']);
    Route::get('/news/category/{category}', [FrontendNewsController::class, 'category']);

    Route::prefix('lesson')->group(function () {
        Route::get('/', [GradeOneController::class, 'index'])->name('api.lesson.index');
        Route::get('/{id}', [GradeOneController::class, 'show']);
        Route::get('subjects/{subject}', [GradeOneController::class, 'showSubject']);
        Route::get('subjects/{subject}/articles/{semester}/{category}', [GradeOneController::class, 'subjectArticles']);
        Route::get('/articles/{article}', [GradeOneController::class, 'showArticle']);
        Route::get('files/download/{id}', [GradeOneController::class, 'downloadFile']);
    });
});

// Authenticated and Dashboard Routes
Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::prefix('dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])->name('api.dashboard.index');

        // Users Management


        Route::apiResource('users', UserController::class)->names('api.users');
        Route::post('users/{id}/update-profile-photo', [UserController::class, 'updateProfilePhoto'])->name('users.updateProfilePhoto');

        Route::get('users/{user}/permissions-roles', [UserController::class, 'permissions_roles'])->name('api.users.permissions_roles');
        Route::put('users/{user}/permissions-roles', [UserController::class, 'updatePermissionsRoles'])->name('api.users.updatePermissionsRoles');

        // School Classes API
        Route::apiResource('classes', SchoolClassController::class)->names('api.classes')->middleware('can:manage classes');

        // Subjects API
        Route::apiResource('subjects', SubjectController::class)->names('api.subjects')->middleware('can:manage subjects');

        // Semesters API
        Route::apiResource('semesters', SemesterController::class)->names('api.semesters');

        // Articles API
        Route::apiResource('articles', ArticleController::class)->names('api.articles');
        Route::get('/class/{grade_level}', [ArticleController::class, 'indexByClass'])->name('api.articles.forClass');

        // Files API
        Route::apiResource('files', FileController::class)->names('api.files');
        Route::get('/files/{id}/download', [FileController::class, 'downloadFile'])->name('api.files.download');

        // Categories API
        Route::apiResource('categories', CategoryController::class)->names('api.categories');

        // News API
        Route::apiResource('news', NewsController::class)->names('api.news');

        // Messages API
        Route::apiResource('messages', MessageController::class)->names('api.messages');

        // Notifications API
        Route::apiResource('notifications', NotificationController::class)->names('api.notifications');

        // Calendar API
        Route::apiResource('calendar', CalendarController::class)->names('api.calendar');

        // Roles & Permissions
        Route::apiResource('roles', RoleController::class)->names('api.roles');
        Route::apiResource('permissions', PermissionController::class)->names('api.permissions');

        // Reactions API
        Route::apiResource('reactions', ReactionController::class)->names('api.reactions');

        // Comments
        Route::post('/comments', [CommentController::class, 'store'])->name('api.comments.store');
    });
});

// Keywords Routes
Route::get('/keywords', [KeywordController::class, 'index']);
Route::get('/keywords/{keyword}', [KeywordController::class, 'indexByKeyword']);
