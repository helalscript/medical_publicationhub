<?php

use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\LogoutController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\Auth\ResetPasswordController;
use App\Http\Controllers\API\Auth\SocialLoginController;
use App\Http\Controllers\API\Auth\UserController;
use App\Http\Controllers\API\V1\CalendlyController as CalendlyApiController;
use App\Http\Controllers\API\V1\CMS\AboutusPageController as APIAboutusPageController;
use App\Http\Controllers\API\V1\CMS\FaqPageController as APIFaqPageController;
use App\Http\Controllers\API\V1\CMS\HomePageController;
use App\Http\Controllers\API\V1\CMS\MetaAcademyPageController as APIMetaAcademyPageController;
use App\Http\Controllers\API\V1\CMS\PartnershipPageController as APIPartnershipPageController;
use App\Http\Controllers\API\V1\CMS\ResearchPageController as APIResearchPageController;
use App\Http\Controllers\API\V1\CMS\ServicePageController as APIServicePageController;
use App\Http\Controllers\API\V1\CourseController as CourseApiController;
use App\Http\Controllers\API\V1\CoursePurchaseController as CoursePurchaseApiController;
use App\Http\Controllers\API\V1\PaymentController as PaymentApiController;
use App\Http\Controllers\API\V1\DashboardController;
use App\Http\Controllers\API\V1\FavouriteController as FavouriteApiController;
use App\Http\Controllers\API\V1\Mentor\DashboardController as MentorDashboardApiController;
use App\Http\Controllers\API\V1\Mentor\ChatController as MentorChatApiController;
use App\Http\Controllers\API\V1\Mentor\ResearchIdeaController as MentorResearchIdeaApiController;
use App\Http\Controllers\API\V1\MyCourseController as MyCourseApiController;
use App\Http\Controllers\API\V1\OpportunityApplicationController as OpportunityApplicationApiController;
use App\Http\Controllers\API\V1\OpportunityController as OpportunityApiController;
use App\Http\Controllers\API\V1\PartnershipRequestController as PartnershipRequestApiController;
use App\Http\Controllers\API\V1\PublicationController as PublicationApiController;
use App\Http\Controllers\API\V1\Researcher\ChatController as ResearcherChatApiController;
use App\Http\Controllers\API\V1\Researcher\DashboardController as ResearcherDashboardApiController;
use App\Http\Controllers\API\V1\OfferController as OfferApiController;
use App\Http\Controllers\API\V1\ServiceController as ServiceApiController;
use App\Http\Controllers\API\V1\User\Faqs\UserFaqController;
use App\Http\Controllers\API\V1\User\NewsletterController;
use App\Http\Controllers\API\V1\User\NotificationController;
use App\Http\Controllers\API\V1\User\UserContactSupportController;
use App\Http\Controllers\API\V1\Coordinator\OpportunityController as CoordinatorOpportunityController;
use App\Http\Controllers\API\V1\Coordinator\OpportunityApplicationController as CoordinatorOpportunityApplicationController;
use App\Http\Controllers\API\V1\Coordinator\ChatController as CoordinatorChatController;
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
    Route::delete('/delete-profile', [UserController::class, 'deleteProfile']);
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

    
    // My courses
    Route::get('/my-courses', [MyCourseApiController::class, 'index']);
    Route::post('/courses/{courseId}/buy', [CoursePurchaseApiController::class, 'buy']);

    // Payments (Moyasar)
    Route::post('/payments', [PaymentApiController::class, 'create']);
    Route::post('/payments/pay', [PaymentApiController::class, 'pay']);
    Route::get('/payments/{paymentId}', [PaymentApiController::class, 'fetch']);
    Route::get('/payments', [PaymentApiController::class, 'list']);
    Route::post('/payments/{paymentId}/verify', [PaymentApiController::class, 'verify']);
});

// Mentor — research ideas (JWT + mentor role)
Route::group(['prefix' => 'mentor', 'middleware' => ['auth:api', 'check_is_mentor']], function ($router) {
    // Dashboard overview
    Route::get('/dashboard/overview', [MentorDashboardApiController::class, 'overview']);

    Route::get('/research-ideas', [MentorResearchIdeaApiController::class, 'index']);
    Route::post('/research-ideas', [MentorResearchIdeaApiController::class, 'store']);
    Route::get('/research-ideas/{id}', [MentorResearchIdeaApiController::class, 'show']);
    Route::put('/research-ideas/{id}', [MentorResearchIdeaApiController::class, 'update']);
    Route::post('/research-ideas/{id}/submission-proof', [MentorResearchIdeaApiController::class, 'submitSubmissionProof']);

    // Chats
    Route::get('/chats', [MentorChatApiController::class, 'index']);
    Route::get('/chats/{id}', [MentorChatApiController::class, 'show']);
    Route::post('/chats/{id}/messages', [MentorChatApiController::class, 'sendMessage']);
});

// Opportunities & applications (JWT user) and Researcher chats
Route::group(['prefix' => 'researcher', 'middleware' => ['auth:api', 'check_is_researcher']], function ($router) {
    // Dashboard overview
    Route::get('/dashboard/overview', [ResearcherDashboardApiController::class, 'overview']);

    // applications
    Route::get('/opportunity-applications', [OpportunityApplicationApiController::class, 'index']);
    Route::post('/opportunity-applications', [OpportunityApplicationApiController::class, 'store']);
    Route::get('/opportunity-applications/{id}', [OpportunityApplicationApiController::class, 'show']);
    Route::put('/opportunity-applications/{id}', [OpportunityApplicationApiController::class, 'update']);
    Route::delete('/opportunity-applications/{id}', [OpportunityApplicationApiController::class, 'destroy']);

    // favourites (opportunities)
    Route::get('/favourites', [FavouriteApiController::class, 'index']);
    Route::post('/favourites', [FavouriteApiController::class, 'store']);
    Route::get('/favourites/{id}', [FavouriteApiController::class, 'show']);
    Route::delete('/favourites/{id}', [FavouriteApiController::class, 'destroy']);


    Route::get('/chats', [ResearcherChatApiController::class, 'index']);
    Route::get('/chats/{id}', [ResearcherChatApiController::class, 'show']);
    Route::post('/chats/{id}/messages', [ResearcherChatApiController::class, 'sendMessage']);
});

// Coordinator routes
Route::group(['prefix' => 'coordinator', 'middleware' => ['auth:api', 'check_is_coordinator']], function ($router) {
    // Opportunities
    Route::get('/opportunities', [CoordinatorOpportunityController::class, 'index']);
    Route::get('/opportunities/{id}', [CoordinatorOpportunityController::class, 'show']);

    // Applications
    Route::get('/opportunity-applications', [CoordinatorOpportunityApplicationController::class, 'index']);
    Route::get('/opportunity-applications/{id}', [CoordinatorOpportunityApplicationController::class, 'show']);
    Route::put('/opportunity-applications/{id}/status', [CoordinatorOpportunityApplicationController::class, 'updateStatus']);

    // Chats
    Route::get('/chats', [CoordinatorChatController::class, 'index']);
    Route::get('/chats/{id}', [CoordinatorChatController::class, 'show']);
    Route::post('/chats/{id}/messages', [CoordinatorChatController::class, 'sendMessage']);
});

// opportunities (public)
Route::get('/opportunities', [OpportunityApiController::class, 'index']);
Route::get('/opportunities/{id}', [OpportunityApiController::class, 'show']);
Route::get('/opportunities/mentors/{id}', [OpportunityApiController::class, 'mentors']);

//fillter list for opportunity
Route::post('/opportunities/fillter-list', [OpportunityApiController::class, 'fillterList']);

// Courses (public)
Route::get('/courses', [CourseApiController::class, 'index']);
Route::get('/courses/{id}', [CourseApiController::class, 'show']);

// Publications (public)
Route::get('/publications', [PublicationApiController::class, 'index']);
Route::get('/publications/{id}', [PublicationApiController::class, 'show']);

// Partnership requests (public)
Route::post('/partnership-requests', [PartnershipRequestApiController::class, 'store']);

// Calendly schedule (public)
Route::get('/calendly/schedule-link', [CalendlyApiController::class, 'scheduleLink']);

// Services (public)
Route::get('/services', [ServiceApiController::class, 'index']);
Route::get('/services/{id}', [ServiceApiController::class, 'show']);

// Offers (public)
Route::get('/offers', [OfferApiController::class, 'index']);
Route::get('/offers/{id}', [OfferApiController::class, 'show']);

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
Route::post('/payments/webhook/moyasar', [PaymentApiController::class, 'webhook']);

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

// route for aboutus page
Route::group(['prefix' => 'cms/aboutus-page'], function () {
    Route::get('/hero-section', [APIAboutusPageController::class, 'getHeroSection']);
    Route::post('/hero-section', [APIAboutusPageController::class, 'updateHeroSection']);
    Route::get('/our-mission-section', [APIAboutusPageController::class, 'getOurMissionSection']);
    Route::post('/our-mission-section', [APIAboutusPageController::class, 'updateOurMissionSection']);
    Route::get('/mission-items', [APIAboutusPageController::class, 'getMissionItems']);
    Route::post('/mission-items', [APIAboutusPageController::class, 'storeMissionItem']);
    Route::put('/mission-items/{id}', [APIAboutusPageController::class, 'updateMissionItem']);
    Route::delete('/mission-items/{id}', [APIAboutusPageController::class, 'deleteMissionItem']);
    Route::get('/full-page', [APIAboutusPageController::class, 'getAboutUsPage']);
});
