<?php

/**
 * Backend Admin Routes for Web Application
 *
 * This file contains all the routes for managing the admin panel, including routes for
 * dashboard, system Settings, profile Settings, daily tips, blogs, and natural care.
 * Routes are grouped under the 'admin' prefix and require authentication with the 'admin' middleware.
 */

use App\Http\Controllers\Web\Backend\BlogController;
use App\Http\Controllers\Web\Backend\DashboardController;
use App\Http\Controllers\Web\Backend\CMS\FaqController;
use App\Http\Controllers\Web\Backend\CMS\FaqCategoryController;
use App\Http\Controllers\Web\Backend\CMS\HomePageSocialLinkContainerController;
use App\Http\Controllers\Web\Backend\ContactMessageController;
use App\Http\Controllers\Web\Backend\DynamicPageController;
use App\Http\Controllers\Web\Backend\LiveSessionRequestController;
use App\Http\Controllers\Web\Backend\PrayerRequestController;
use App\Http\Controllers\Web\Backend\ChatController;
use App\Http\Controllers\Web\Backend\NewsletterController;
use App\Http\Controllers\Web\Backend\NotificationController;
use App\Http\Controllers\Web\Backend\ProfileController;
use App\Http\Controllers\Web\Backend\SystemSettingController;
use App\Http\Controllers\Web\Backend\UserController;
use App\Http\Controllers\Web\Backend\UserManagementController;
use App\Http\Controllers\Web\Backend\ResearchIdeaAdminController;
use App\Http\Controllers\Web\Backend\OpportunityController;
use App\Http\Controllers\Web\Backend\OpportunityApplicationAdminController;
use App\Http\Controllers\Web\Backend\OpportunityChatController;
use App\Http\Controllers\Web\Backend\CourseController;
use App\Http\Controllers\Web\Backend\ModuleController;
use App\Http\Controllers\Web\Backend\LessonController;
use App\Http\Controllers\Web\Backend\PaymentController;
use App\Http\Controllers\Web\Backend\CourseAccessController;
use App\Http\Controllers\Web\Backend\PublicationController;
use App\Http\Controllers\Web\Backend\PartnershipRequestController;
use App\Http\Controllers\Web\Backend\ServiceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:web', 'role_check'])->prefix('admin')->group(function () {

    // Dashboard (overview cards + chat charts)
    Route::get('/', DashboardController::class)->name('admin.dashboard');

    Route::get('settings-profile', [ProfileController::class, 'index'])->name('profile_settings.index');
    Route::post('settings-profile', [ProfileController::class, 'update'])->name('profile_settings.update');
    Route::get('settings-profile-password', [ProfileController::class, 'passwordChange'])->name('profile_settings.password_change');
    Route::post('settings-profile-password', [ProfileController::class, 'UpdatePassword'])->name('profile_settings.password');

    // Route for system settings index
    Route::get('system-settings', [SystemSettingController::class, 'index'])->name('system_settings.index');

    // Route for updating system settings
    Route::post('system-settings-update', [SystemSettingController::class, 'update'])->name('system_settings.update');

    // Calendly settings
    Route::get('system-settings-calendly', [SystemSettingController::class, 'calendlySettingGet'])->name('system_settings.calendly_get');
    Route::post('system-settings-calendly', [SystemSettingController::class, 'calendlySettingUpdate'])->name('system_settings.calendly_update');

    // Mail Settings index
    Route::get('system-settings-mail', [SystemSettingController::class, 'mailSettingGet'])->name('system_settings.mail_get');
    // Mail Settings routes
    Route::post('system-settings-mail', [SystemSettingController::class, 'mailSettingUpdate'])->name('system_settings.mail');

    // Routes for DynamicPageController
    Route::resource('/dynamic-page', DynamicPageController::class)->names('dynamic_page');
    Route::post('/dynamic-page/status/{id}', [DynamicPageController::class, 'status'])->name('dynamic_page.status');

    // Route for UserController manage client
    Route::resource('/users', UserController::class)->names('users');
    Route::post('/users/status/{id}', [UserController::class, 'status'])->name('users.status');

    // Routes for managing app users (Clergy & Seeker)
    Route::prefix('user-management')->name('user_management.')->group(function () {

        // Mentor
        Route::get('/mentor', [UserManagementController::class, 'mentorIndex'])->name('mentor.index');
        Route::get('/mentor/create', [UserManagementController::class, 'mentorCreate'])->name('mentor.create');
        Route::post('/mentor', [UserManagementController::class, 'mentorStore'])->name('mentor.store');
        Route::get('/mentor/{id}', [UserManagementController::class, 'mentorShow'])->name('mentor.show');
        Route::get('/mentor/{id}/edit', [UserManagementController::class, 'mentorEdit'])->name('mentor.edit');
        Route::put('/mentor/{id}', [UserManagementController::class, 'mentorUpdate'])->name('mentor.update');
        Route::delete('/mentor/{id}', [UserManagementController::class, 'mentorDestroy'])->name('mentor.destroy');
        Route::post('/mentor/status/{id}', [UserManagementController::class, 'mentorStatus'])->name('mentor.status');

        // Researcher
        Route::get('/researcher', [UserManagementController::class, 'researcherIndex'])->name('researcher.index');
        Route::get('/researcher/create', [UserManagementController::class, 'researcherCreate'])->name('researcher.create');
        Route::post('/researcher', [UserManagementController::class, 'researcherStore'])->name('researcher.store');
        Route::get('/researcher/{id}', [UserManagementController::class, 'researcherShow'])->name('researcher.show');
        Route::get('/researcher/{id}/edit', [UserManagementController::class, 'researcherEdit'])->name('researcher.edit');
        Route::put('/researcher/{id}', [UserManagementController::class, 'researcherUpdate'])->name('researcher.update');
        Route::delete('/researcher/{id}', [UserManagementController::class, 'researcherDestroy'])->name('researcher.destroy');
        Route::post('/researcher/status/{id}', [UserManagementController::class, 'researcherStatus'])->name('researcher.status');

        // Coordinator
        Route::get('/coordinator', [UserManagementController::class, 'coordinatorIndex'])->name('coordinator.index');
        Route::get('/coordinator/create', [UserManagementController::class, 'coordinatorCreate'])->name('coordinator.create');
        Route::post('/coordinator', [UserManagementController::class, 'coordinatorStore'])->name('coordinator.store');
        Route::get('/coordinator/{id}', [UserManagementController::class, 'coordinatorShow'])->name('coordinator.show');
        Route::get('/coordinator/{id}/edit', [UserManagementController::class, 'coordinatorEdit'])->name('coordinator.edit');
        Route::put('/coordinator/{id}', [UserManagementController::class, 'coordinatorUpdate'])->name('coordinator.update');
        Route::delete('/coordinator/{id}', [UserManagementController::class, 'coordinatorDestroy'])->name('coordinator.destroy');
        Route::post('/coordinator/status/{id}', [UserManagementController::class, 'coordinatorStatus'])->name('coordinator.status');
    });

    // Research ideas (admin review & dates)
    Route::get('/research-ideas', [ResearchIdeaAdminController::class, 'index'])->name('admin.research_ideas.index');
    Route::get('/research-ideas/{id}', [ResearchIdeaAdminController::class, 'show'])->name('admin.research_ideas.show');
    Route::get('/research-ideas/{id}/edit', [ResearchIdeaAdminController::class, 'edit'])->name('admin.research_ideas.edit');
    Route::put('/research-ideas/{id}', [ResearchIdeaAdminController::class, 'update'])->name('admin.research_ideas.update');

    // Opportunities
    Route::resource('/opportunities', OpportunityController::class)->names('admin.opportunities');
    Route::post('/opportunities/status/{id}', [OpportunityController::class, 'status'])->name('admin.opportunities.status');

    // Opportunity applications
    Route::get('/opportunity-applications', [OpportunityApplicationAdminController::class, 'index'])->name('admin.opportunity_applications.index');
    Route::get('/opportunity-applications/{id}', [OpportunityApplicationAdminController::class, 'show'])->name('admin.opportunity_applications.show');
    Route::get('/opportunity-applications/{id}/edit', [OpportunityApplicationAdminController::class, 'edit'])->name('admin.opportunity_applications.edit');
    Route::put('/opportunity-applications/{id}', [OpportunityApplicationAdminController::class, 'update'])->name('admin.opportunity_applications.update');
    Route::delete('/opportunity-applications/{id}', [OpportunityApplicationAdminController::class, 'destroy'])->name('admin.opportunity_applications.destroy');

    // Opportunity chats (admin)
    Route::get('/opportunity-chats', [OpportunityChatController::class, 'index'])->name('admin.opportunity_chats.index');
    Route::get('/opportunity-chats/{id}', [OpportunityChatController::class, 'show'])->name('admin.opportunity_chats.show');
    Route::post('/opportunity-chats/{id}/messages', [OpportunityChatController::class, 'sendMessage'])->name('admin.opportunity_chats.messages.store');

    // Courses
    Route::resource('/courses', CourseController::class)->names('admin.courses');
    Route::post('/courses/status/{id}', [CourseController::class, 'status'])->name('admin.courses.status');

    // Modules (filter by ?course_id=)
    Route::resource('/modules', ModuleController::class)->names('admin.modules')->except(['show']);
    Route::post('/modules/status/{id}', [ModuleController::class, 'status'])->name('admin.modules.status');

    // Lessons (filter by ?module_id=)
    Route::resource('/lessons', LessonController::class)->names('admin.lessons')->except(['show']);
    Route::post('/lessons/status/{id}', [LessonController::class, 'status'])->name('admin.lessons.status');

    // Course access (admin grant/revoke)
    Route::get('/course-access', [CourseAccessController::class, 'index'])->name('admin.course_access.index');
    Route::post('/course-access/grant', [CourseAccessController::class, 'grant'])->name('admin.course_access.grant');
    Route::post('/course-access/revoke', [CourseAccessController::class, 'revoke'])->name('admin.course_access.revoke');

    // Payments
    Route::get('/payments', [PaymentController::class, 'index'])->name('admin.payments.index');
    Route::get('/payments/{id}', [PaymentController::class, 'show'])->name('admin.payments.show');
    Route::post('/payments/{id}/status', [PaymentController::class, 'status'])->name('admin.payments.status');

    // Publications
    Route::resource('/publications', PublicationController::class)->names('admin.publications');
    Route::post('/publications/status/{id}', [PublicationController::class, 'status'])->name('admin.publications.status');

    // Partnership requests
    Route::get('/partnership-requests', [PartnershipRequestController::class, 'index'])->name('admin.partnership_requests.index');
    Route::get('/partnership-requests/{id}', [PartnershipRequestController::class, 'show'])->name('admin.partnership_requests.show');
    Route::delete('/partnership-requests/{id}', [PartnershipRequestController::class, 'destroy'])->name('admin.partnership_requests.destroy');
    Route::post('/partnership-requests/status/{id}', [PartnershipRequestController::class, 'status'])->name('admin.partnership_requests.status');

    // Services
    Route::resource('/services', ServiceController::class)->names('admin.services');
    Route::post('/services/status/{id}', [ServiceController::class, 'status'])->name('admin.services.status');
    Route::post('/services/featured/{id}', [ServiceController::class, 'featured'])->name('admin.services.featured');

    // Route for FaqController
    Route::resource('/faqs', FaqController::class)->names('faqs');
    Route::post('/faqs/status/{id}', [FaqController::class, 'status'])->name('faqs.status');

    // Route for FaqCategoryController
    Route::resource('/faq-categories', FaqCategoryController::class)->names('faq_categories');
    Route::post('/faq-categories/status/{id}', [FaqCategoryController::class, 'status'])->name('faq_categories.status');

    // Route Social link
    Route::resource('/social-link/index', HomePageSocialLinkContainerController::class)->names('social_link')->except('show');
    Route::post('/social-link/status/{id}', [HomePageSocialLinkContainerController::class, 'status'])->name('social_link.status');

    Route::resource('/blogs', BlogController::class)->names('blogs');
    Route::post('/blogs/status/{id}', [BlogController::class, 'status'])->name('blogs.status');
    Route::post('/blogs/update-container', [BlogController::class, 'updateContainer'])->name('blogs.update_container');


    // Routes for NotificationController
    Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notification.read');
    Route::post('/notifications/mark-as-read/single/{id}', [NotificationController::class, 'markAsSingleRead'])->name('notification.read_single');
    Route::delete('/notifications/{id}', [NotificationController::class, 'delete'])->name('notification.delete');
    Route::delete('/notifications', [NotificationController::class, 'deleteAll'])->name('notification.deleteall');

    Route::get('/contact-us-message', [ContactMessageController::class, 'index'])->name('admin_contact_us.index');
    Route::get('/contact-us-message/{id}', [ContactMessageController::class, 'show'])->name('admin_contact_us.show');
    Route::delete('/contact-us-message/{id}', [ContactMessageController::class, 'destroy'])->name('admin_contact_us.destroy');




    // Newsletter subscriptions
    Route::get('/newsletters', [NewsletterController::class, 'index'])->name('admin.newsletters.index');
});

// Public route for dynamic pages accessible to all users
Route::get('/pages/{slug}', [DynamicPageController::class, 'showDaynamicPage'])->name('pages');

require __DIR__.'/cms.php';
