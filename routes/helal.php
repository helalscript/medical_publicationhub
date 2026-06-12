<?php

/**
 * Backend Admin Routes for Web Application
 *
 * This file contains all the routes for managing the admin panel, including routes for
 * dashboard, system Settings, profile Settings, daily tips, blogs, and natural care.
 * Routes are grouped under the 'admin' prefix and require authentication with the 'admin' middleware.
 */

use App\Http\Controllers\Web\Backend\AccessControl\PermissionController;
use App\Http\Controllers\Web\Backend\AccessControl\RoleController;
use App\Http\Controllers\Web\Backend\AccessControl\RolePermissionController;
use App\Http\Controllers\Web\Backend\BlogController;
use App\Http\Controllers\Web\Backend\CMS\FaqCategoryController;
use App\Http\Controllers\Web\Backend\CMS\FaqController;
use App\Http\Controllers\Web\Backend\CMS\HomePageSocialLinkContainerController;
use App\Http\Controllers\Web\Backend\ContactMessageController;
use App\Http\Controllers\Web\Backend\CourseAccessController;
use App\Http\Controllers\Web\Backend\CourseController;
use App\Http\Controllers\Web\Backend\DashboardController;
use App\Http\Controllers\Web\Backend\LocaleController;
use App\Http\Controllers\Web\Backend\DynamicPageController;
use App\Http\Controllers\Web\Backend\LessonController;
use App\Http\Controllers\Web\Backend\ModuleController;
use App\Http\Controllers\Web\Backend\NewsletterController;
use App\Http\Controllers\Web\Backend\NotificationController;
use App\Http\Controllers\Web\Backend\OfferController;
use App\Http\Controllers\Web\Backend\OpportunityApplicationAdminController;
use App\Http\Controllers\Web\Backend\OpportunityCategoryController;
use App\Http\Controllers\Web\Backend\OpportunityChatController;
use App\Http\Controllers\Web\Backend\OpportunityController;
use App\Http\Controllers\Web\Backend\PartnershipRequestController;
use App\Http\Controllers\Web\Backend\PaymentController;
use App\Http\Controllers\Web\Backend\ProfileController;
use App\Http\Controllers\Web\Backend\PublicationController;
use App\Http\Controllers\Web\Backend\ResearchIdeaAdminController;
use App\Http\Controllers\Web\Backend\ServiceController;
use App\Http\Controllers\Web\Backend\SystemSettingController;
use App\Http\Controllers\Web\Backend\UserController;
use App\Http\Controllers\Web\Backend\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:web', 'role_check', 'sync_spatie_role'])->prefix('admin')->group(function () {

    Route::post('locale/{locale}', [LocaleController::class, 'update'])->name('admin.locale.update');

    // Dashboard (overview cards + chat charts)
    Route::get('/', DashboardController::class)->name('admin.dashboard')->middleware('permission:admin.dashboard');

    Route::get('settings-profile', [ProfileController::class, 'index'])->name('profile_settings.index')->middleware('permission:profile_settings.index');
    Route::post('settings-profile', [ProfileController::class, 'update'])->name('profile_settings.update')->middleware('permission:profile_settings.update');
    Route::get('settings-profile-password', [ProfileController::class, 'passwordChange'])->name('profile_settings.password_change')->middleware('permission:profile_settings.password_change');
    Route::post('settings-profile-password', [ProfileController::class, 'UpdatePassword'])->name('profile_settings.password')->middleware('permission:profile_settings.password');

    // Route for system settings index
    Route::get('system-settings', [SystemSettingController::class, 'index'])->name('system_settings.index')->middleware('permission:system_settings.index');

    // Route for updating system settings
    Route::post('system-settings-update', [SystemSettingController::class, 'update'])->name('system_settings.update')->middleware('permission:system_settings.update');

    // Calendly settings
    Route::get('system-settings-calendly', [SystemSettingController::class, 'calendlySettingGet'])->name('system_settings.calendly_get')->middleware('permission:system_settings.calendly_get');
    Route::post('system-settings-calendly', [SystemSettingController::class, 'calendlySettingUpdate'])->name('system_settings.calendly_update')->middleware('permission:system_settings.calendly_update');

    // Mail Settings index
    Route::get('system-settings-mail', [SystemSettingController::class, 'mailSettingGet'])->name('system_settings.mail_get')->middleware('permission:system_settings.mail_get');
    // Mail Settings routes
    Route::post('system-settings-mail', [SystemSettingController::class, 'mailSettingUpdate'])->name('system_settings.mail')->middleware('permission:system_settings.mail');

    // Routes for DynamicPageController
    Route::get('/dynamic-page', [DynamicPageController::class, 'index'])->name('dynamic_page.index')->middleware('permission:dynamic_page.index');
    Route::get('/dynamic-page/create', [DynamicPageController::class, 'create'])->name('dynamic_page.create')->middleware('permission:dynamic_page.create');
    Route::post('/dynamic-page', [DynamicPageController::class, 'store'])->name('dynamic_page.store')->middleware('permission:dynamic_page.store');
    Route::get('/dynamic-page/{dynamic_page}', [DynamicPageController::class, 'show'])->name('dynamic_page.show')->middleware('permission:dynamic_page.show');
    Route::get('/dynamic-page/{dynamic_page}/edit', [DynamicPageController::class, 'edit'])->name('dynamic_page.edit')->middleware('permission:dynamic_page.edit');
    Route::put('/dynamic-page/{dynamic_page}', [DynamicPageController::class, 'update'])->name('dynamic_page.update')->middleware('permission:dynamic_page.update');
    Route::delete('/dynamic-page/{dynamic_page}', [DynamicPageController::class, 'destroy'])->name('dynamic_page.destroy')->middleware('permission:dynamic_page.destroy');
    Route::post('/dynamic-page/status/{id}', [DynamicPageController::class, 'status'])->name('dynamic_page.status')->middleware('permission:dynamic_page.status');

    // Route for UserController manage client
    Route::get('/users', [UserController::class, 'index'])->name('users.index')->middleware('permission:users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create')->middleware('permission:users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store')->middleware('permission:users.store');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show')->middleware('permission:users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('permission:users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('permission:users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:users.destroy');
    Route::post('/users/status/{id}', [UserController::class, 'status'])->name('users.status')->middleware('permission:users.status');

    // Routes for managing app users (Clergy & Seeker)
    Route::prefix('user-management')->name('user_management.')->group(function () {

        // Mentor
        Route::get('/mentor', [UserManagementController::class, 'mentorIndex'])->name('mentor.index')->middleware('permission:user_management.mentor.index');
        Route::get('/mentor/create', [UserManagementController::class, 'mentorCreate'])->name('mentor.create')->middleware('permission:user_management.mentor.create');
        Route::post('/mentor', [UserManagementController::class, 'mentorStore'])->name('mentor.store')->middleware('permission:user_management.mentor.store');
        Route::get('/mentor/{id}', [UserManagementController::class, 'mentorShow'])->name('mentor.show')->middleware('permission:user_management.mentor.show');
        Route::get('/mentor/{id}/edit', [UserManagementController::class, 'mentorEdit'])->name('mentor.edit')->middleware('permission:user_management.mentor.edit');
        Route::put('/mentor/{id}', [UserManagementController::class, 'mentorUpdate'])->name('mentor.update')->middleware('permission:user_management.mentor.update');
        Route::delete('/mentor/{id}', [UserManagementController::class, 'mentorDestroy'])->name('mentor.destroy')->middleware('permission:user_management.mentor.destroy');
        Route::post('/mentor/status/{id}', [UserManagementController::class, 'mentorStatus'])->name('mentor.status')->middleware('permission:user_management.mentor.status');

        // Researcher
        Route::get('/researcher', [UserManagementController::class, 'researcherIndex'])->name('researcher.index')->middleware('permission:user_management.researcher.index');
        Route::get('/researcher/create', [UserManagementController::class, 'researcherCreate'])->name('researcher.create')->middleware('permission:user_management.researcher.create');
        Route::post('/researcher', [UserManagementController::class, 'researcherStore'])->name('researcher.store')->middleware('permission:user_management.researcher.store');
        Route::get('/researcher/{id}', [UserManagementController::class, 'researcherShow'])->name('researcher.show')->middleware('permission:user_management.researcher.show');
        Route::get('/researcher/{id}/edit', [UserManagementController::class, 'researcherEdit'])->name('researcher.edit')->middleware('permission:user_management.researcher.edit');
        Route::put('/researcher/{id}', [UserManagementController::class, 'researcherUpdate'])->name('researcher.update')->middleware('permission:user_management.researcher.update');
        Route::delete('/researcher/{id}', [UserManagementController::class, 'researcherDestroy'])->name('researcher.destroy')->middleware('permission:user_management.researcher.destroy');
        Route::post('/researcher/status/{id}', [UserManagementController::class, 'researcherStatus'])->name('researcher.status')->middleware('permission:user_management.researcher.status');

        // Coordinator
        Route::get('/coordinator', [UserManagementController::class, 'coordinatorIndex'])->name('coordinator.index')->middleware('permission:user_management.coordinator.index');
        Route::get('/coordinator/create', [UserManagementController::class, 'coordinatorCreate'])->name('coordinator.create')->middleware('permission:user_management.coordinator.create');
        Route::post('/coordinator', [UserManagementController::class, 'coordinatorStore'])->name('coordinator.store')->middleware('permission:user_management.coordinator.store');
        Route::get('/coordinator/{id}', [UserManagementController::class, 'coordinatorShow'])->name('coordinator.show')->middleware('permission:user_management.coordinator.show');
        Route::get('/coordinator/{id}/edit', [UserManagementController::class, 'coordinatorEdit'])->name('coordinator.edit')->middleware('permission:user_management.coordinator.edit');
        Route::put('/coordinator/{id}', [UserManagementController::class, 'coordinatorUpdate'])->name('coordinator.update')->middleware('permission:user_management.coordinator.update');
        Route::delete('/coordinator/{id}', [UserManagementController::class, 'coordinatorDestroy'])->name('coordinator.destroy')->middleware('permission:user_management.coordinator.destroy');
        Route::post('/coordinator/status/{id}', [UserManagementController::class, 'coordinatorStatus'])->name('coordinator.status')->middleware('permission:user_management.coordinator.status');
    });

    // Access Control (Roles & Permissions)
    Route::prefix('access-control')->name('admin.access_control.')->group(function () {
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index')->middleware('permission:access_control.roles.view');
        Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create')->middleware('permission:access_control.roles.create');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store')->middleware('permission:access_control.roles.create');
        Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit')->middleware('permission:access_control.roles.update');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update')->middleware('permission:access_control.roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('permission:access_control.roles.delete');

        Route::get('/permissions', [PermissionController::class, 'index'])->name('permissions.index')->middleware('permission:access_control.permissions.view');
        Route::get('/permissions/create', [PermissionController::class, 'create'])->name('permissions.create')->middleware('permission:access_control.permissions.create');
        Route::post('/permissions', [PermissionController::class, 'store'])->name('permissions.store')->middleware('permission:access_control.permissions.create');
        Route::get('/permissions/{permission}/edit', [PermissionController::class, 'edit'])->name('permissions.edit')->middleware('permission:access_control.permissions.update');
        Route::put('/permissions/{permission}', [PermissionController::class, 'update'])->name('permissions.update')->middleware('permission:access_control.permissions.update');
        Route::delete('/permissions/{permission}', [PermissionController::class, 'destroy'])->name('permissions.destroy')->middleware('permission:access_control.permissions.delete');

        Route::get('/roles/{role}/permissions', [RolePermissionController::class, 'edit'])->name('roles.permissions.edit')->middleware('permission:access_control.roles.assign_permissions');
        Route::put('/roles/{role}/permissions', [RolePermissionController::class, 'update'])->name('roles.permissions.update')->middleware('permission:access_control.roles.assign_permissions');
    });

    // Research ideas (admin review & dates)
    Route::get('/research-ideas', [ResearchIdeaAdminController::class, 'index'])->name('admin.research_ideas.index')->middleware('permission:admin.research_ideas.index');
    Route::get('/research-ideas/{id}', [ResearchIdeaAdminController::class, 'show'])->name('admin.research_ideas.show')->middleware('permission:admin.research_ideas.show');
    Route::get('/research-ideas/{id}/edit', [ResearchIdeaAdminController::class, 'edit'])->name('admin.research_ideas.edit')->middleware('permission:admin.research_ideas.edit');
    Route::put('/research-ideas/{id}', [ResearchIdeaAdminController::class, 'update'])->name('admin.research_ideas.update')->middleware('permission:admin.research_ideas.update');

    // Opportunities
    Route::get('/opportunities', [OpportunityController::class, 'index'])->name('admin.opportunities.index')->middleware('permission:admin.opportunities.index');
    Route::get('/opportunities/create', [OpportunityController::class, 'create'])->name('admin.opportunities.create')->middleware('permission:admin.opportunities.create');
    Route::post('/opportunities', [OpportunityController::class, 'store'])->name('admin.opportunities.store')->middleware('permission:admin.opportunities.store');
    Route::get('/opportunities/{opportunity}', [OpportunityController::class, 'show'])->name('admin.opportunities.show')->middleware('permission:admin.opportunities.show');
    Route::get('/opportunities/{opportunity}/edit', [OpportunityController::class, 'edit'])->name('admin.opportunities.edit')->middleware('permission:admin.opportunities.edit');
    Route::put('/opportunities/{opportunity}', [OpportunityController::class, 'update'])->name('admin.opportunities.update')->middleware('permission:admin.opportunities.update');
    Route::delete('/opportunities/{opportunity}', [OpportunityController::class, 'destroy'])->name('admin.opportunities.destroy')->middleware('permission:admin.opportunities.destroy');
    Route::post('/opportunities/status/{id}', [OpportunityController::class, 'status'])->name('admin.opportunities.status')->middleware('permission:admin.opportunities.status');

    // Opportunity applications
    Route::get('/opportunity-applications', [OpportunityApplicationAdminController::class, 'index'])->name('admin.opportunity_applications.index')->middleware('permission:admin.opportunity_applications.index');
    Route::get('/opportunity-applications/{id}', [OpportunityApplicationAdminController::class, 'show'])->name('admin.opportunity_applications.show')->middleware('permission:admin.opportunity_applications.show');
    Route::get('/opportunity-applications/{id}/edit', [OpportunityApplicationAdminController::class, 'edit'])->name('admin.opportunity_applications.edit')->middleware('permission:admin.opportunity_applications.edit');
    Route::put('/opportunity-applications/{id}', [OpportunityApplicationAdminController::class, 'update'])->name('admin.opportunity_applications.update')->middleware('permission:admin.opportunity_applications.update');
    Route::delete('/opportunity-applications/{id}', [OpportunityApplicationAdminController::class, 'destroy'])->name('admin.opportunity_applications.destroy')->middleware('permission:admin.opportunity_applications.destroy');

    // Opportunity chats (admin)
    Route::get('/opportunity-chats', [OpportunityChatController::class, 'index'])->name('admin.opportunity_chats.index')->middleware('permission:admin.opportunity_chats.index');
    Route::get('/opportunity-chats/{id}', [OpportunityChatController::class, 'show'])->name('admin.opportunity_chats.show')->middleware('permission:admin.opportunity_chats.show');
    Route::post('/opportunity-chats/{id}/messages', [OpportunityChatController::class, 'sendMessage'])->name('admin.opportunity_chats.messages.store')->middleware('permission:admin.opportunity_chats.messages.store');

    // Opportunity categories (Filter lists)
    Route::get('/opportunity-categories', [OpportunityCategoryController::class, 'index'])->name('admin.opportunity_categories.index')->middleware('permission:admin.opportunity_categories.index');
    Route::get('/opportunity-categories/create', [OpportunityCategoryController::class, 'create'])->name('admin.opportunity_categories.create')->middleware('permission:admin.opportunity_categories.create');
    Route::post('/opportunity-categories', [OpportunityCategoryController::class, 'store'])->name('admin.opportunity_categories.store')->middleware('permission:admin.opportunity_categories.store');
    Route::get('/opportunity-categories/{id}/edit', [OpportunityCategoryController::class, 'edit'])->name('admin.opportunity_categories.edit')->middleware('permission:admin.opportunity_categories.edit');
    Route::put('/opportunity-categories/{id}', [OpportunityCategoryController::class, 'update'])->name('admin.opportunity_categories.update')->middleware('permission:admin.opportunity_categories.update');
    Route::delete('/opportunity-categories/{id}', [OpportunityCategoryController::class, 'destroy'])->name('admin.opportunity_categories.destroy')->middleware('permission:admin.opportunity_categories.destroy');
    Route::post('/opportunity-categories/status/{id}', [OpportunityCategoryController::class, 'status'])->name('admin.opportunity_categories.status')->middleware('permission:admin.opportunity_categories.status');

    // Courses
    Route::get('/courses', [CourseController::class, 'index'])->name('admin.courses.index')->middleware('permission:admin.courses.index');
    Route::get('/courses/create', [CourseController::class, 'create'])->name('admin.courses.create')->middleware('permission:admin.courses.create');
    Route::post('/courses', [CourseController::class, 'store'])->name('admin.courses.store')->middleware('permission:admin.courses.store');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('admin.courses.show')->middleware('permission:admin.courses.show');
    Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('admin.courses.edit')->middleware('permission:admin.courses.edit');
    Route::put('/courses/{course}', [CourseController::class, 'update'])->name('admin.courses.update')->middleware('permission:admin.courses.update');
    Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('admin.courses.destroy')->middleware('permission:admin.courses.destroy');
    Route::post('/courses/status/{id}', [CourseController::class, 'status'])->name('admin.courses.status')->middleware('permission:admin.courses.status');

    // Modules (filter by ?course_id=)
    Route::get('/modules', [ModuleController::class, 'index'])->name('admin.modules.index')->middleware('permission:admin.modules.index');
    Route::get('/modules/create', [ModuleController::class, 'create'])->name('admin.modules.create')->middleware('permission:admin.modules.create');
    Route::post('/modules', [ModuleController::class, 'store'])->name('admin.modules.store')->middleware('permission:admin.modules.store');
    Route::get('/modules/{module}', [ModuleController::class, 'show'])->name('admin.modules.show')->middleware('permission:admin.modules.show');
    Route::get('/modules/{module}/edit', [ModuleController::class, 'edit'])->name('admin.modules.edit')->middleware('permission:admin.modules.edit');
    Route::put('/modules/{module}', [ModuleController::class, 'update'])->name('admin.modules.update')->middleware('permission:admin.modules.update');
    Route::delete('/modules/{module}', [ModuleController::class, 'destroy'])->name('admin.modules.destroy')->middleware('permission:admin.modules.destroy');
    Route::post('/modules/status/{id}', [ModuleController::class, 'status'])->name('admin.modules.status')->middleware('permission:admin.modules.status');

    // Lessons (filter by ?module_id=)
    Route::get('/lessons', [LessonController::class, 'index'])->name('admin.lessons.index')->middleware('permission:admin.lessons.index');
    Route::get('/lessons/create', [LessonController::class, 'create'])->name('admin.lessons.create')->middleware('permission:admin.lessons.create');
    Route::post('/lessons', [LessonController::class, 'store'])->name('admin.lessons.store')->middleware('permission:admin.lessons.store');
    Route::get('/lessons/{lesson}', [LessonController::class, 'show'])->name('admin.lessons.show')->middleware('permission:admin.lessons.show');
    Route::get('/lessons/{lesson}/edit', [LessonController::class, 'edit'])->name('admin.lessons.edit')->middleware('permission:admin.lessons.edit');
    Route::put('/lessons/{lesson}', [LessonController::class, 'update'])->name('admin.lessons.update')->middleware('permission:admin.lessons.update');
    Route::delete('/lessons/{lesson}', [LessonController::class, 'destroy'])->name('admin.lessons.destroy')->middleware('permission:admin.lessons.destroy');
    Route::post('/lessons/status/{id}', [LessonController::class, 'status'])->name('admin.lessons.status')->middleware('permission:admin.lessons.status');

    // Course access (admin grant/revoke)
    Route::get('/course-access', [CourseAccessController::class, 'index'])->name('admin.course_access.index')->middleware('permission:admin.course_access.index');
    Route::post('/course-access/grant', [CourseAccessController::class, 'grant'])->name('admin.course_access.grant')->middleware('permission:admin.course_access.grant');
    Route::post('/course-access/revoke', [CourseAccessController::class, 'revoke'])->name('admin.course_access.revoke')->middleware('permission:admin.course_access.revoke');

    // Payments
    Route::get('/payments', [PaymentController::class, 'index'])->name('admin.payments.index')->middleware('permission:admin.payments.index');
    Route::get('/payments/{id}', [PaymentController::class, 'show'])->name('admin.payments.show')->middleware('permission:admin.payments.show');
    Route::post('/payments/{id}/status', [PaymentController::class, 'status'])->name('admin.payments.status')->middleware('permission:admin.payments.status');

    // Publications
    Route::get('/apublications', [PublicationController::class, 'index'])->name('admin.publications.index')->middleware('permission:admin.publications.index');
    Route::get('/apublications/create', [PublicationController::class, 'create'])->name('admin.publications.create')->middleware('permission:admin.publications.create');
    Route::post('/apublications', [PublicationController::class, 'store'])->name('admin.publications.store')->middleware('permission:admin.publications.store');
    Route::get('/apublications/{publication}', [PublicationController::class, 'show'])->name('admin.publications.show')->middleware('permission:admin.publications.show');
    Route::get('/apublications/{publication}/edit', [PublicationController::class, 'edit'])->name('admin.publications.edit')->middleware('permission:admin.publications.edit');
    Route::put('/apublications/{publication}', [PublicationController::class, 'update'])->name('admin.publications.update')->middleware('permission:admin.publications.update');
    Route::delete('/apublications/{publication}', [PublicationController::class, 'destroy'])->name('admin.publications.destroy')->middleware('permission:admin.publications.destroy');
    Route::post('/apublications/status/{id}', [PublicationController::class, 'status'])->name('admin.publications.status')->middleware('permission:admin.publications.status');

    // Partnership requests
    Route::get('/partnership-requests', [PartnershipRequestController::class, 'index'])->name('admin.partnership_requests.index')->middleware('permission:admin.partnership_requests.index');
    Route::get('/partnership-requests/{id}', [PartnershipRequestController::class, 'show'])->name('admin.partnership_requests.show')->middleware('permission:admin.partnership_requests.show');
    Route::delete('/partnership-requests/{id}', [PartnershipRequestController::class, 'destroy'])->name('admin.partnership_requests.destroy')->middleware('permission:admin.partnership_requests.destroy');
    Route::post('/partnership-requests/status/{id}', [PartnershipRequestController::class, 'status'])->name('admin.partnership_requests.status')->middleware('permission:admin.partnership_requests.status');

    // Services
    Route::get('/services', [ServiceController::class, 'index'])->name('admin.services.index')->middleware('permission:admin.services.index');
    Route::get('/services/create', [ServiceController::class, 'create'])->name('admin.services.create')->middleware('permission:admin.services.create');
    Route::post('/services', [ServiceController::class, 'store'])->name('admin.services.store')->middleware('permission:admin.services.store');
    Route::get('/services/{service}', [ServiceController::class, 'show'])->name('admin.services.show')->middleware('permission:admin.services.show');
    Route::get('/services/{service}/edit', [ServiceController::class, 'edit'])->name('admin.services.edit')->middleware('permission:admin.services.edit');
    Route::put('/services/{service}', [ServiceController::class, 'update'])->name('admin.services.update')->middleware('permission:admin.services.update');
    Route::delete('/services/{service}', [ServiceController::class, 'destroy'])->name('admin.services.destroy')->middleware('permission:admin.services.destroy');
    Route::post('/services/status/{id}', [ServiceController::class, 'status'])->name('admin.services.status')->middleware('permission:admin.services.status');
    Route::post('/services/featured/{id}', [ServiceController::class, 'featured'])->name('admin.services.featured')->middleware('permission:admin.services.featured');

    // Offers
    Route::get('/offers', [OfferController::class, 'index'])->name('admin.offers.index')->middleware('permission:admin.offers.index');
    Route::get('/offers/create', [OfferController::class, 'create'])->name('admin.offers.create')->middleware('permission:admin.offers.create');
    Route::post('/offers', [OfferController::class, 'store'])->name('admin.offers.store')->middleware('permission:admin.offers.store');
    Route::get('/offers/{offer}', [OfferController::class, 'show'])->name('admin.offers.show')->middleware('permission:admin.offers.show');
    Route::get('/offers/{offer}/edit', [OfferController::class, 'edit'])->name('admin.offers.edit')->middleware('permission:admin.offers.edit');
    Route::put('/offers/{offer}', [OfferController::class, 'update'])->name('admin.offers.update')->middleware('permission:admin.offers.update');
    Route::delete('/offers/{offer}', [OfferController::class, 'destroy'])->name('admin.offers.destroy')->middleware('permission:admin.offers.destroy');
    Route::post('/offers/status/{id}', [OfferController::class, 'status'])->name('admin.offers.status')->middleware('permission:admin.offers.status');

    // Route for FaqController
    Route::get('/faqs', [FaqController::class, 'index'])->name('faqs.index')->middleware('permission:faqs.index');
    Route::get('/faqs/create', [FaqController::class, 'create'])->name('faqs.create')->middleware('permission:faqs.create');
    Route::post('/faqs', [FaqController::class, 'store'])->name('faqs.store')->middleware('permission:faqs.store');
    Route::get('/faqs/{faq}', [FaqController::class, 'show'])->name('faqs.show')->middleware('permission:faqs.show');
    Route::get('/faqs/{faq}/edit', [FaqController::class, 'edit'])->name('faqs.edit')->middleware('permission:faqs.edit');
    Route::put('/faqs/{faq}', [FaqController::class, 'update'])->name('faqs.update')->middleware('permission:faqs.update');
    Route::delete('/faqs/{faq}', [FaqController::class, 'destroy'])->name('faqs.destroy')->middleware('permission:faqs.destroy');
    Route::post('/faqs/status/{id}', [FaqController::class, 'status'])->name('faqs.status')->middleware('permission:faqs.status');

    // Route for FaqCategoryController
    Route::get('/faq-categories', [FaqCategoryController::class, 'index'])->name('faq_categories.index')->middleware('permission:faq_categories.index');
    Route::get('/faq-categories/create', [FaqCategoryController::class, 'create'])->name('faq_categories.create')->middleware('permission:faq_categories.create');
    Route::post('/faq-categories', [FaqCategoryController::class, 'store'])->name('faq_categories.store')->middleware('permission:faq_categories.store');
    Route::get('/faq-categories/{faq_category}', [FaqCategoryController::class, 'show'])->name('faq_categories.show')->middleware('permission:faq_categories.show');
    Route::get('/faq-categories/{faq_category}/edit', [FaqCategoryController::class, 'edit'])->name('faq_categories.edit')->middleware('permission:faq_categories.edit');
    Route::put('/faq-categories/{faq_category}', [FaqCategoryController::class, 'update'])->name('faq_categories.update')->middleware('permission:faq_categories.update');
    Route::delete('/faq-categories/{faq_category}', [FaqCategoryController::class, 'destroy'])->name('faq_categories.destroy')->middleware('permission:faq_categories.destroy');
    Route::post('/faq-categories/status/{id}', [FaqCategoryController::class, 'status'])->name('faq_categories.status')->middleware('permission:faq_categories.status');

    // Route Social link
    Route::get('/social-link/index', [HomePageSocialLinkContainerController::class, 'index'])->name('social_link.index')->middleware('permission:social_link.index');
    Route::get('/social-link/index/create', [HomePageSocialLinkContainerController::class, 'create'])->name('social_link.create')->middleware('permission:social_link.create');
    Route::post('/social-link/index', [HomePageSocialLinkContainerController::class, 'store'])->name('social_link.store')->middleware('permission:social_link.store');
    Route::get('/social-link/index/{social_link}/edit', [HomePageSocialLinkContainerController::class, 'edit'])->name('social_link.edit')->middleware('permission:social_link.edit');
    Route::put('/social-link/index/{social_link}', [HomePageSocialLinkContainerController::class, 'update'])->name('social_link.update')->middleware('permission:social_link.update');
    Route::delete('/social-link/index/{social_link}', [HomePageSocialLinkContainerController::class, 'destroy'])->name('social_link.destroy')->middleware('permission:social_link.destroy');
    Route::post('/social-link/status/{id}', [HomePageSocialLinkContainerController::class, 'status'])->name('social_link.status')->middleware('permission:social_link.status');

    Route::get('/blogs', [BlogController::class, 'index'])->name('blogs.index')->middleware('permission:blogs.index');
    Route::get('/blogs/create', [BlogController::class, 'create'])->name('blogs.create')->middleware('permission:blogs.create');
    Route::post('/blogs', [BlogController::class, 'store'])->name('blogs.store')->middleware('permission:blogs.store');
    Route::get('/blogs/{blog}', [BlogController::class, 'show'])->name('blogs.show')->middleware('permission:blogs.show');
    Route::get('/blogs/{blog}/edit', [BlogController::class, 'edit'])->name('blogs.edit')->middleware('permission:blogs.edit');
    Route::put('/blogs/{blog}', [BlogController::class, 'update'])->name('blogs.update')->middleware('permission:blogs.update');
    Route::delete('/blogs/{blog}', [BlogController::class, 'destroy'])->name('blogs.destroy')->middleware('permission:blogs.destroy');
    Route::post('/blogs/status/{id}', [BlogController::class, 'status'])->name('blogs.status')->middleware('permission:blogs.status');
    Route::post('/blogs/update-container', [BlogController::class, 'updateContainer'])->name('blogs.update_container')->middleware('permission:blogs.update_container');

    // Routes for NotificationController
    Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notification.read')->middleware('permission:notification.read');
    Route::post('/notifications/mark-as-read/single/{id}', [NotificationController::class, 'markAsSingleRead'])->name('notification.read_single')->middleware('permission:notification.read_single');
    Route::delete('/notifications/{id}', [NotificationController::class, 'delete'])->name('notification.delete')->middleware('permission:notification.delete');
    Route::delete('/notifications', [NotificationController::class, 'deleteAll'])->name('notification.deleteall')->middleware('permission:notification.deleteall');

    Route::get('/contact-us-message', [ContactMessageController::class, 'index'])->name('admin_contact_us.index')->middleware('permission:admin_contact_us.index');
    Route::get('/contact-us-message/{id}', [ContactMessageController::class, 'show'])->name('admin_contact_us.show')->middleware('permission:admin_contact_us.show');
    Route::delete('/contact-us-message/{id}', [ContactMessageController::class, 'destroy'])->name('admin_contact_us.destroy')->middleware('permission:admin_contact_us.destroy');

    // Newsletter subscriptions
    Route::get('/newsletters', [NewsletterController::class, 'index'])->name('admin.newsletters.index')->middleware('permission:admin.newsletters.index');
});

// Public route for dynamic pages accessible to all users
Route::get('/pages/{slug}', [DynamicPageController::class, 'showDaynamicPage'])->name('pages');

Route::get('/send-test-mail', function () {
    try {
        $to = 'husoaib422@gmail.com';

        Mail::raw('This is a test email from Laravel + Brevo API. Sent at: '.now(), function ($message) use ($to) {
            $message->to($to)
                ->subject('Laravel Brevo Test Mail - '.now());
        });
        

        return response()->json(['message' => 'Email sent successfully to '.$to.' at '.now()], 200);
    } catch (Exception $e) {
        Log::error('SendTestMailController::sendTestMail' . $e->getMessage());
        return response()->json(['error' => 'Email sending failed: ' . $e->getMessage()], 500);
    }
});

require __DIR__.'/cms.php';
