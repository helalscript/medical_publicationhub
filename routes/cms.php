<?php

use App\Http\Controllers\Web\Backend\CMS\HomePageController;
use App\Http\Controllers\Web\Backend\CMS\HomePageCounterController;
use App\Http\Controllers\Web\Backend\CMS\HomePageReviewController;
use App\Http\Controllers\Web\Backend\CMS\HomePageWhyResearchersChooseController;
use App\Http\Controllers\Web\Backend\CMS\OnboardingScreenController;
use App\Http\Controllers\Web\Backend\CMS\QuestionPageController;
use App\Http\Controllers\Web\Backend\CMS\HowItWorkPageController;
use App\Http\Controllers\Web\Backend\CMS\HowItWorkWorkProgressController;
use App\Http\Controllers\Web\Backend\CMS\HowItWorkOurValuesController;
use App\Http\Controllers\Web\Backend\CMS\FindClergyPageController;
use App\Http\Controllers\Web\Backend\CMS\FaqPageController;
use Illuminate\Support\Facades\Route;

// ===================== CMS HOME PAGE =====================
Route::prefix('cms/home-page')->name('cms.home_page.')->group(function () {

    Route::get('/hero-section', [HomePageController::class, 'getHeroSection'])->name('hero_section.create');
    Route::post('/hero-section', [HomePageController::class, 'updateHeroSection'])->name('hero_section.update');

    Route::resource('counter-section', HomePageCounterController::class)->parameters(['counter-section' => 'id'])->names('counter_section');
    Route::post('/counter-section/status/{id}', [HomePageCounterController::class, 'status'])->name('counter_section.status');
    Route::post('/counter-section/update-container', [HomePageCounterController::class, 'updateContainer'])->name('counter_section.update_container');

    Route::resource('why-researchers-choose-section', HomePageWhyResearchersChooseController::class)
        ->parameters(['why-researchers-choose-section' => 'id'])
        ->names('why_researchers_choose_section');
    Route::post('/why-researchers-choose-section/status/{id}', [HomePageWhyResearchersChooseController::class, 'status'])->name('why_researchers_choose_section.status');
    Route::post('/why-researchers-choose-section/update-container', [HomePageWhyResearchersChooseController::class, 'updateContainer'])->name('why_researchers_choose_section.update_container');

    Route::get('/featured-research-opportunities-section', [HomePageController::class, 'getFeaturedResearchOpportunities'])->name('featured_research_opportunities_section.create');
    Route::post('/featured-research-opportunities-section', [HomePageController::class, 'updateFeaturedResearchOpportunities'])->name('featured_research_opportunities_section.update');

    Route::resource('review-section', HomePageReviewController::class)->parameters(['review-section' => 'id'])->names('review_section');
    Route::post('/review-section/status/{id}', [HomePageReviewController::class, 'status'])->name('review_section.status');
    Route::post('/review-section/update-container', [HomePageReviewController::class, 'updateContainer'])->name('review_section.update_container');

    Route::get('/meta-academy-section', [HomePageController::class, 'getMetaAcademy'])->name('meta_academy_section.create');
    Route::post('/meta-academy-section', [HomePageController::class, 'updateMetaAcademy'])->name('meta_academy_section.update');

    Route::get('/research-journey-section', [HomePageController::class, 'getResearchJourney'])->name('research_journey_section.create');
    Route::post('/research-journey-section', [HomePageController::class, 'updateResearchJourney'])->name('research_journey_section.update');

    Route::get('/faq-section', [HomePageController::class, 'getFaqSection'])->name('faq_section.create');
    Route::post('/faq-section', [HomePageController::class, 'updateFaqSection'])->name('faq_section.update');
});




// ===================== CMS How It Work Page =====================
Route::prefix('cms/how-it-work-page')->name('cms.how_it_work_page.')->group(function () {
    Route::get('/hero-section', [HowItWorkPageController::class, 'getHeroSection'])->name('hero_section.create');
    Route::post('/hero-section', [HowItWorkPageController::class, 'updateHeroSection'])->name('hero_section.update');

    Route::resource('work-progress-section', HowItWorkWorkProgressController::class)->parameters(['work-progress-section' => 'id'])->names('work_progress_section');
    Route::post('/work-progress-section/status/{id}', [HowItWorkWorkProgressController::class, 'status'])->name('work_progress_section.status');
    Route::post('/work-progress-section/update-container', [HowItWorkWorkProgressController::class, 'updateContainer'])->name('work_progress_section.update_container');

    Route::resource('our-values-section', HowItWorkOurValuesController::class)->parameters(['our-values-section' => 'id'])->names('our_values_section');
    Route::post('/our-values-section/status/{id}', [HowItWorkOurValuesController::class, 'status'])->name('our_values_section.status');
    Route::post('/our-values-section/update-container', [HowItWorkOurValuesController::class, 'updateContainer'])->name('our_values_section.update_container');

    Route::get('/spiritual-journey', [HowItWorkPageController::class, 'getSpiritualJourney'])->name('spiritual_journey.create');
    Route::post('/spiritual-journey', [HowItWorkPageController::class, 'updateSpiritualJourney'])->name('spiritual_journey.update');
});

// ===================== CMS Find Clergy Page =====================
Route::prefix('cms/find-clergy-page')->name('cms.find_clergy_page.')->group(function () {
    Route::get('/hero-section', [FindClergyPageController::class, 'getHeroSection'])->name('hero_section.create');
    Route::post('/hero-section', [FindClergyPageController::class, 'updateHeroSection'])->name('hero_section.update');
});

// ===================== CMS Onboarding Screen =====================
Route::prefix('cms/onboarding-screen')->name('cms.onboarding_screen.')->group(function () {
    Route::resource('/screens', OnboardingScreenController::class)->names('screens');
    Route::post('/screens/status/{id}', [OnboardingScreenController::class, 'status'])->name('screens.status');
});

// ===================== CMS Question Page =====================
Route::prefix('cms/question-page')->name('cms.question_page.')->group(function () {
    Route::get('clergy', [QuestionPageController::class, 'index'])->name('clergy.index');
    Route::get('clergy/create', [QuestionPageController::class, 'create'])->name('clergy.create');
    Route::post('clergy', [QuestionPageController::class, 'store'])->name('clergy.store');
    Route::get('clergy/{id}/edit', [QuestionPageController::class, 'edit'])->name('clergy.edit');
    Route::put('clergy/{id}', [QuestionPageController::class, 'update'])->name('clergy.update');
    Route::delete('clergy/{id}', [QuestionPageController::class, 'destroy'])->name('clergy.destroy');
    Route::post('clergy/status/{id}', [QuestionPageController::class, 'status'])->name('clergy.status');

    Route::get('seeker', [QuestionPageController::class, 'index'])->name('seeker.index');
    Route::get('seeker/create', [QuestionPageController::class, 'create'])->name('seeker.create');
    Route::post('seeker', [QuestionPageController::class, 'store'])->name('seeker.store');
    Route::get('seeker/{id}/edit', [QuestionPageController::class, 'edit'])->name('seeker.edit');
    Route::put('seeker/{id}', [QuestionPageController::class, 'update'])->name('seeker.update');
    Route::delete('seeker/{id}', [QuestionPageController::class, 'destroy'])->name('seeker.destroy');
    Route::post('seeker/status/{id}', [QuestionPageController::class, 'status'])->name('seeker.status');
});

// ===================== CMS FAQ Page =====================
Route::prefix('cms/faq-page')->name('cms.faq_page.')->group(function () {
    Route::get('/hero-section', [FaqPageController::class, 'getHeroSection'])->name('hero_section.create');
    Route::post('/hero-section', [FaqPageController::class, 'updateHeroSection'])->name('hero_section.update');
});


// // ===================== CMS CONTACT US PAGE =====================
// Route::prefix('cms/contact-us-page')->name('cms.contact_us_page.')->group(function () {
//     Route::get('/', [ContactUsPageController::class, 'index'])->name('index');
//     Route::post('/', [ContactUsPageController::class, 'update'])->name('update');

//     Route::get('/form', [ContactUsPageController::class, 'getForm'])->name('form.create');
//     Route::post('/form', [ContactUsPageController::class, 'updateForm'])->name('form.update');
// });


// // ===================== CMS AUTH PAGE =====================
// Route::prefix('cms/auth-page')->name('cms.auth_page.')->group(function () {
//     Route::get('/login', [AuthPageController::class, 'getLogin'])->name('login.create');
//     Route::post('/login', [AuthPageController::class, 'updateLogin'])->name('login.update');

//     Route::get('/register', [AuthPageController::class, 'getRegister'])->name('register.create');
//     Route::post('/register', [AuthPageController::class, 'updateRegister'])->name('register.update');

//     Route::get('/forgot-password', [AuthPageController::class, 'getForgotPassword'])->name('forgot_password.create');
//     Route::post('/forgot-password', [AuthPageController::class, 'updateForgotPassword'])->name('forgot_password.update');
// });