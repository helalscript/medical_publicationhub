<?php

use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\LogoutController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\Auth\ResetPasswordController;
use App\Http\Controllers\API\Auth\SocialLoginController;
use App\Http\Controllers\API\Auth\UserController;
use App\Http\Controllers\API\V1\CMS\HomePageController;
use App\Http\Controllers\API\V1\CMS\FaqPageController as APIFaqPageController;
use App\Http\Controllers\API\V1\CMS\MetaAcademyPageController as APIMetaAcademyPageController;
use App\Http\Controllers\API\V1\CMS\ResearchPageController as APIResearchPageController;
use App\Http\Controllers\API\V1\CMS\ServicePageController as APIServicePageController;
use App\Http\Controllers\API\V1\CMS\PartnershipPageController as APIPartnershipPageController;
use App\Http\Controllers\API\V1\DashboardController;
use App\Http\Controllers\API\V1\User\Faqs\UserFaqController;
use App\Http\Controllers\API\V1\User\NewsletterController;
use App\Http\Controllers\API\V1\User\NotificationController;
use App\Http\Controllers\API\V1\User\UserContactSupportController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'guest:api'], function ($router) {
    // register
    Route::post('register', [RegisterController::class, 'register']);
    Route::post('/verify-email', [RegisterController::class, 'VerifyEmail']);
    Route::post('/resend-otp', [RegisterController::class, 'ResendOtp']);
    // login
    Route::post('login', [LoginController::class, 'login']);
    // forgot password
    Route::post('/forget-password', [ResetPasswordController::class, 'forgotPassword']);
    Route::post('/verify-otp', [ResetPasswordController::class, 'VerifyOTP']);
    Route::post('/reset-password', [ResetPasswordController::class, 'ResetPassword']);
    // social login
    Route::post('/social-login', [SocialLoginController::class, 'SocialLogin']);
});

Route::group(['middleware' => 'auth:api'], function ($router) {
    Route::get('/refresh-token', [LoginController::class, 'refreshToken']);
    Route::post('/logout', [LogoutController::class, 'logout']);
    Route::get('/me', [UserController::class, 'me']);
    Route::post('/update-profile', [UserController::class, 'updateProfile']);
    Route::post('/update-password', [UserController::class, 'changePassword']);
    Route::post('/clergy-profile-update', [UserController::class, 'ClergyProfileUpdate']);
});

// only for user
Route::group(['middleware' => ['auth:api', 'check_is_user']], function ($router) {
    // route for dashboard data
    Route::get('/dashboard-data/home', [DashboardController::class, 'dashboardData']);

    // Notifications route
    Route::get('/my-notifications', [NotificationController::class, 'index']);
    Route::post('/my-notifications/mark-as-read', [NotificationController::class, 'markAsRead']);
    Route::delete('/my-notifications-delete/{id}', [NotificationController::class, 'delete']);
    Route::delete('/my-notifications-delete-all', [NotificationController::class, 'deleteAll']);
});



// FAQ for users (public)
Route::get('/faqs', [UserFaqController::class, 'index']);
Route::get('/faqs-categories', [UserFaqController::class, 'categories']);
Route::get('/faqs-categories/{id}', [UserFaqController::class, 'showCategory']);

// Newsletter (public)
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe']);
// social link
Route::get('/social-links', [HomePageController::class, 'getSocialLinks']);

// without auth
Route::post('/contact-support-message/sent', [UserContactSupportController::class, 'store']);


// --------- cms part --------------
// Route::get('/social-link', [HomePageController::class, 'getSocialLinks']);
Route::get('/system-info', [HomePageController::class, 'getSystemInfo']);


// dynamic page
Route::get('dynamic-pages', [HomePageController::class, 'getDynamicPages']);
Route::get('dynamic-pages/single/{slug}', [HomePageController::class, 'showDaynamicPage']);

// route for home page
Route::group(['prefix' => 'cms/home-page'], function () {
    Route::get('/hero-section', [HomePageController::class, 'getHeroSection']);
    Route::get('/counter-section', [HomePageController::class, 'getCounterSection']);
    Route::get('/why-researchers-choose-section', [HomePageController::class, 'getWhyResearchersChooseSection']);
    Route::get('/featured-research-opportunities-section', [HomePageController::class, 'getFeaturedResearchOpportunitiesSection']);
    Route::get('/meta-academy-section', [HomePageController::class, 'getMetaAcademySection']);
    Route::get('/review-section', [HomePageController::class, 'getReviewSection']);
    Route::get('/research-journey-section', [HomePageController::class, 'getResearchJourneySection']);
    Route::get('/faq-section', [HomePageController::class, 'getFaqSection']);
});


// route for faq page
Route::group(['prefix' => 'cms/faq-page'], function () {
    Route::get('/hero-section', [APIFaqPageController::class, 'getHeroSection']);
    Route::post('/hero-section', [APIFaqPageController::class, 'updateHeroSection']);
    Route::get('/question-section', [APIFaqPageController::class, 'getQuestionSection']);
    Route::post('/question-section', [APIFaqPageController::class, 'updateQuestionSection']);
});

// route for meta academy page
Route::group(['prefix' => 'cms/meta-academy-page'], function () {
    Route::get('/hero-section', [APIMetaAcademyPageController::class, 'getHeroSection']);
    Route::get('/counter-section', [APIMetaAcademyPageController::class, 'getCounterSection']);
});

// route for research page
Route::group(['prefix' => 'cms/research-page'], function () {
    Route::get('/research-section', [APIResearchPageController::class, 'getResearchSection']);
});

// route for service page
Route::group(['prefix' => 'cms/service-page'], function () {
    Route::get('/service-section', [APIServicePageController::class, 'getServiceSection']);
});

// route for partnership page
Route::group(['prefix' => 'cms/partnership-page'], function () {
    Route::get('/hero-section', [APIPartnershipPageController::class, 'getHeroSection']);
    Route::get('/our-mission-section', [APIPartnershipPageController::class, 'getOurMissionSection']);
    Route::get('/about-us-section', [APIPartnershipPageController::class, 'getAboutUsSection']);
    Route::get('/partnership-value-section', [APIPartnershipPageController::class, 'getPartnershipValueSection']);
    Route::get('/core-research-section', [APIPartnershipPageController::class, 'getCoreResearchSection']);
    Route::get('/systematic-review-section', [APIPartnershipPageController::class, 'getSystematicReviewSection']);
    Route::get('/manuscript-section', [APIPartnershipPageController::class, 'getManuscriptSection']);
    Route::get('/partnership-model-section', [APIPartnershipPageController::class, 'getPartnershipModelSection']);
    Route::get('/reviewer-comment-section', [APIPartnershipPageController::class, 'getReviewerCommentSection']);
    Route::get('/journal-section', [APIPartnershipPageController::class, 'getJournalSection']);
    Route::get('/research-opportunitie-section', [APIPartnershipPageController::class, 'getResearchOpportunitieSection']);
    Route::get('/why-partner-section', [APIPartnershipPageController::class, 'getWhyPartnerSection']);
    Route::get('/partner-model-section', [APIPartnershipPageController::class, 'getPartnerModelSection']);
});


