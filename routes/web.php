<?php

use App\Http\Controllers\AccessRequestController;
use App\Http\Controllers\AdminAccessRequestController;
use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\AdminResearchController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\ContactRequestController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\DashboardAccessRequestController;
use App\Http\Controllers\DashboardContactRequestController;
use App\Http\Controllers\GuestConversationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DashboardResearchController;
use App\Http\Controllers\HealthCheckController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ResearchCategoryController;
use App\Http\Controllers\ResearchController;
use App\Http\Controllers\ResearchDownloadController;
use App\Http\Controllers\ResearcherAnalyticsController;
use App\Http\Controllers\ResearcherProfileController;
use App\Http\Controllers\SavedResearchController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\ThumbnailDownloadController;
use Illuminate\Support\Facades\Route;


/*
|--------------------------------------------------------------------------
| Public & SEO Routes
|--------------------------------------------------------------------------
*/
Route::get('/health', HealthCheckController::class)->name('health');

Route::get('/', HomeController::class)->name('home');
Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::post('/contact', [PageController::class, 'submitContact'])
    ->middleware('throttle:5,1')
    ->name('contact.submit');

Route::get('/sitemap.xml', [SitemapController::class, 'sitemap'])->name('seo.sitemap');
Route::get('/robots.txt', [SitemapController::class, 'robots'])->name('seo.robots');

// Research Catalog & Secure Downloads
Route::get('/research', [ResearchController::class, 'index'])->name('research.index');
Route::get('/research/{research}/download', ResearchDownloadController::class)->name('research.download');
Route::get('/research/{research}/thumbnail', ThumbnailDownloadController::class)->name('research.thumbnail');
Route::get('/research/{slug}', [ResearchController::class, 'show'])->name('research.show');

// Categories System
Route::get('/categories', [ResearchCategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{slug}', [ResearchCategoryController::class, 'show'])->name('categories.show');

// Public Researcher Profiles
Route::get('/researchers/{user}', [ResearcherProfileController::class, 'show'])->name('researchers.show');

// Contact Author Request (Public for users & guests)
Route::post('/research/{research}/contact-request', [ContactRequestController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('research.contact-request.store');

// Signed Guest Conversation Access Routes
Route::get('/guest/conversations/{conversation}', [GuestConversationController::class, 'show'])
    ->middleware('signed')
    ->name('guest.conversations.show');
Route::post('/guest/conversations/{conversation}/messages', [GuestConversationController::class, 'storeMessage'])
    ->middleware(['signed', 'throttle:20,1'])
    ->name('guest.conversations.messages.store');

/*
|--------------------------------------------------------------------------
| Authenticated Researcher Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/user/profile-settings', [ProfileController::class, 'show'])->name('profile.show');

    // Dashboard Research Management with Upload Rate Limits
    Route::get('/dashboard/research', [DashboardResearchController::class, 'index'])->name('dashboard.research.index');
    Route::get('/dashboard/research/create', [DashboardResearchController::class, 'create'])->name('dashboard.research.create');
    Route::post('/dashboard/research', [DashboardResearchController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('dashboard.research.store');
    Route::get('/dashboard/research/{research}/edit', [DashboardResearchController::class, 'edit'])->name('dashboard.research.edit');
    Route::put('/dashboard/research/{research}', [DashboardResearchController::class, 'update'])
        ->middleware('throttle:10,1')
        ->name('dashboard.research.update');
    Route::delete('/dashboard/research/{research}', [DashboardResearchController::class, 'destroy'])->name('dashboard.research.destroy');

    // Access Requests with Throttling
    Route::post('/research/{research}/access-request', [AccessRequestController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('research.access-request.store');

    // Dashboard Conversations System
    Route::get('/dashboard/conversations', [ConversationController::class, 'index'])->name('dashboard.conversations.index');
    Route::get('/dashboard/conversations/{conversation}', [ConversationController::class, 'show'])->name('dashboard.conversations.show');
    Route::post('/dashboard/conversations/{conversation}/messages', [ConversationController::class, 'storeMessage'])
        ->middleware('throttle:20,1')
        ->name('dashboard.conversations.messages.store');
    Route::post('/dashboard/conversations/{conversation}/grant-access', [ConversationController::class, 'grantAccess'])
        ->name('dashboard.conversations.grant-access');
    Route::post('/dashboard/conversations/{conversation}/close', [ConversationController::class, 'close'])
        ->name('dashboard.conversations.close');

    // Dashboard Access & Contact Inquiries Review (Legacy)
    Route::get('/dashboard/requests', [DashboardAccessRequestController::class, 'index'])->name('dashboard.requests.index');
    Route::post('/dashboard/requests/{accessRequest}/approve', [DashboardAccessRequestController::class, 'approve'])->name('dashboard.requests.approve');
    Route::post('/dashboard/requests/{accessRequest}/reject', [DashboardAccessRequestController::class, 'reject'])->name('dashboard.requests.reject');

    Route::get('/dashboard/inquiries', [DashboardContactRequestController::class, 'index'])->name('dashboard.inquiries.index');
    Route::get('/dashboard/inquiries/{contactRequest}', [DashboardContactRequestController::class, 'show'])->name('dashboard.inquiries.show');
    Route::post('/dashboard/inquiries/{contactRequest}/reply', [DashboardContactRequestController::class, 'reply'])->name('dashboard.inquiries.reply');
    Route::post('/dashboard/inquiries/{contactRequest}/close', [DashboardContactRequestController::class, 'close'])->name('dashboard.inquiries.close');

    // Bookmarks & Saved Research
    Route::get('/dashboard/bookmarks', [SavedResearchController::class, 'index'])->name('dashboard.bookmarks.index');
    Route::post('/research/{research}/bookmark', [SavedResearchController::class, 'toggle'])->name('research.bookmark.toggle');

    // Notification Center
    Route::get('/dashboard/notifications', [NotificationController::class, 'index'])->name('dashboard.notifications.index');
    Route::post('/dashboard/notifications/{id}/read', [NotificationController::class, 'markRead'])->name('dashboard.notifications.read');
    Route::post('/dashboard/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('dashboard.notifications.mark-all-read');
    Route::delete('/dashboard/notifications/{id}', [NotificationController::class, 'destroy'])->name('dashboard.notifications.destroy');

    // Researcher Analytics Dashboard
    Route::get('/dashboard/analytics', ResearcherAnalyticsController::class)->name('dashboard.analytics');
});

/*
|--------------------------------------------------------------------------
| Protected Admin Portal Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])
        ->name('admin.dashboard');

    // Research Management
    Route::get('/admin/research', [AdminResearchController::class, 'index'])->name('admin.research.index');
    Route::get('/admin/research/{research}', [AdminResearchController::class, 'show'])->name('admin.research.show');
    Route::get('/admin/research/{research}/edit', [AdminResearchController::class, 'edit'])->name('admin.research.edit');
    Route::put('/admin/research/{research}', [AdminResearchController::class, 'update'])->name('admin.research.update');
    Route::post('/admin/research/{research}/approve', [AdminResearchController::class, 'approve'])->name('admin.research.approve');
    Route::post('/admin/research/{research}/reject', [AdminResearchController::class, 'reject'])->name('admin.research.reject');
    Route::post('/admin/research/{research}/archive', [AdminResearchController::class, 'archive'])->name('admin.research.archive');
    Route::post('/admin/research/{research}/request-changes', [AdminResearchController::class, 'requestChanges'])->name('admin.research.request-changes');
    Route::delete('/admin/research/{research}', [AdminResearchController::class, 'destroy'])->name('admin.research.destroy');

    // User Management
    Route::get('/admin/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/admin/users/{user}', [AdminUserController::class, 'show'])->name('admin.users.show');
    Route::post('/admin/users/{user}/role', [AdminUserController::class, 'updateRole'])->name('admin.users.update-role');

    // Category Management
    Route::get('/admin/categories', [AdminCategoryController::class, 'index'])->name('admin.categories.index');
    Route::get('/admin/categories/create', [AdminCategoryController::class, 'create'])->name('admin.categories.create');
    Route::post('/admin/categories', [AdminCategoryController::class, 'store'])->name('admin.categories.store');
    Route::get('/admin/categories/{category}/edit', [AdminCategoryController::class, 'edit'])->name('admin.categories.edit');
    Route::put('/admin/categories/{category}', [AdminCategoryController::class, 'update'])->name('admin.categories.update');
    Route::delete('/admin/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('admin.categories.destroy');

    // Access Request Overview
    Route::get('/admin/access-requests', [AdminAccessRequestController::class, 'index'])->name('admin.access-requests.index');
});

