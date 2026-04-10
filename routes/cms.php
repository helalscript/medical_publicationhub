<?php

use App\Http\Controllers\Web\Backend\CMS\HomePageController;
use App\Http\Controllers\Web\Backend\CMS\HomePageCounterController;
use App\Http\Controllers\Web\Backend\CMS\HomePageReviewController;
use App\Http\Controllers\Web\Backend\CMS\HomePageWhyResearchersChooseController;
use App\Http\Controllers\Web\Backend\CMS\FaqPageController;
use App\Http\Controllers\Web\Backend\CMS\ResearchPageController;
use App\Http\Controllers\Web\Backend\CMS\ServicePageController;
use App\Http\Controllers\Web\Backend\CMS\MetaAcademyPageController;
use App\Http\Controllers\Web\Backend\CMS\PartnershipPageController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:web', 'role_check', 'sync_spatie_role'])->group(function () {

// ===================== CMS HOME PAGE =====================
Route::prefix('cms/home-page')->name('cms.home_page.')->group(function () {

    Route::get('/hero-section', [HomePageController::class, 'getHeroSection'])->name('hero_section.create')->middleware('permission:cms.home_page.hero_section.create');
    Route::post('/hero-section', [HomePageController::class, 'updateHeroSection'])->name('hero_section.update')->middleware('permission:cms.home_page.hero_section.update');

    Route::resource('counter-section', HomePageCounterController::class)->parameters(['counter-section' => 'id'])->names('counter_section')->middleware('permission:cms.home_page.counter_section.index');
    Route::post('/counter-section/status/{id}', [HomePageCounterController::class, 'status'])->name('counter_section.status')->middleware('permission:cms.home_page.counter_section.status');

    Route::resource('why-researchers-choose-section', HomePageWhyResearchersChooseController::class)
        ->parameters(['why-researchers-choose-section' => 'id'])
        ->names('why_researchers_choose_section')
        ->middleware('permission:cms.home_page.why_researchers_choose_section.index');
    Route::post('/why-researchers-choose-section/status/{id}', [HomePageWhyResearchersChooseController::class, 'status'])->name('why_researchers_choose_section.status')->middleware('permission:cms.home_page.why_researchers_choose_section.status');
    Route::post('/why-researchers-choose-section/update-container', [HomePageWhyResearchersChooseController::class, 'updateContainer'])->name('why_researchers_choose_section.update_container')->middleware('permission:cms.home_page.why_researchers_choose_section.update_container');

    Route::get('/featured-research-opportunities-section', [HomePageController::class, 'getFeaturedResearchOpportunities'])->name('featured_research_opportunities_section.create')->middleware('permission:cms.home_page.featured_research_opportunities_section.create');
    Route::post('/featured-research-opportunities-section', [HomePageController::class, 'updateFeaturedResearchOpportunities'])->name('featured_research_opportunities_section.update')->middleware('permission:cms.home_page.featured_research_opportunities_section.update');

    Route::resource('review-section', HomePageReviewController::class)->parameters(['review-section' => 'id'])->names('review_section')->middleware('permission:cms.home_page.review_section.index');
    Route::post('/review-section/status/{id}', [HomePageReviewController::class, 'status'])->name('review_section.status')->middleware('permission:cms.home_page.review_section.status');
    Route::post('/review-section/update-container', [HomePageReviewController::class, 'updateContainer'])->name('review_section.update_container')->middleware('permission:cms.home_page.review_section.update_container');

    Route::get('/meta-academy-section', [HomePageController::class, 'getMetaAcademy'])->name('meta_academy_section.create')->middleware('permission:cms.home_page.meta_academy_section.create');
    Route::post('/meta-academy-section', [HomePageController::class, 'updateMetaAcademy'])->name('meta_academy_section.update')->middleware('permission:cms.home_page.meta_academy_section.update');

    Route::get('/research-journey-section', [HomePageController::class, 'getResearchJourney'])->name('research_journey_section.create')->middleware('permission:cms.home_page.research_journey_section.create');
    Route::post('/research-journey-section', [HomePageController::class, 'updateResearchJourney'])->name('research_journey_section.update')->middleware('permission:cms.home_page.research_journey_section.update');

    Route::get('/faq-section', [HomePageController::class, 'getFaqSection'])->name('faq_section.create')->middleware('permission:cms.home_page.faq_section.create');
    Route::post('/faq-section', [HomePageController::class, 'updateFaqSection'])->name('faq_section.update')->middleware('permission:cms.home_page.faq_section.update');
});


// ===================== CMS FAQ Page =====================
Route::prefix('cms/faq-page')->name('cms.faq_page.')->group(function () {
    Route::get('/hero-section', [FaqPageController::class, 'getHeroSection'])->name('hero_section.create')->middleware('permission:cms.faq_page.hero_section.create');
    Route::post('/hero-section', [FaqPageController::class, 'updateHeroSection'])->name('hero_section.update')->middleware('permission:cms.faq_page.hero_section.update');
});

// ===================== CMS Research Page =====================
Route::prefix('cms/research-page')->name('cms.research_page.')->group(function () {
    Route::get('/research-section', [ResearchPageController::class, 'getResearchSection'])->name('research_section.create')->middleware('permission:cms.research_page.research_section.create');
    Route::post('/research-section', [ResearchPageController::class, 'updateResearchSection'])->name('research_section.update')->middleware('permission:cms.research_page.research_section.update');
});

// ===================== CMS Service Page =====================
Route::prefix('cms/service-page')->name('cms.service_page.')->group(function () {
    Route::get('/service-section', [ServicePageController::class, 'getServiceSection'])->name('service_section.create')->middleware('permission:cms.service_page.service_section.create');
    Route::post('/service-section', [ServicePageController::class, 'updateServiceSection'])->name('service_section.update')->middleware('permission:cms.service_page.service_section.update');
});

// ===================== CMS Meta Academy Page =====================
Route::prefix('cms/meta-academy-page')->name('cms.meta_academy_page.')->group(function () {
    Route::get('/hero-section', [MetaAcademyPageController::class, 'getHeroSection'])->name('hero_section.create')->middleware('permission:cms.meta_academy_page.hero_section.create');
    Route::post('/hero-section', [MetaAcademyPageController::class, 'updateHeroSection'])->name('hero_section.update')->middleware('permission:cms.meta_academy_page.hero_section.update');

    Route::get('/counter-section', [MetaAcademyPageController::class, 'counterIndex'])->name('counter_section.index')->middleware('permission:cms.meta_academy_page.counter_section.index');
    Route::get('/counter-section/create', [MetaAcademyPageController::class, 'counterCreate'])->name('counter_section.create')->middleware('permission:cms.meta_academy_page.counter_section.create');
    Route::post('/counter-section', [MetaAcademyPageController::class, 'counterStore'])->name('counter_section.store')->middleware('permission:cms.meta_academy_page.counter_section.store');
    Route::get('/counter-section/{id}/edit', [MetaAcademyPageController::class, 'counterEdit'])->name('counter_section.edit')->middleware('permission:cms.meta_academy_page.counter_section.edit');
    Route::put('/counter-section/{id}', [MetaAcademyPageController::class, 'counterUpdate'])->name('counter_section.update')->middleware('permission:cms.meta_academy_page.counter_section.update');
    Route::delete('/counter-section/{id}', [MetaAcademyPageController::class, 'counterDestroy'])->name('counter_section.destroy')->middleware('permission:cms.meta_academy_page.counter_section.destroy');
    Route::post('/counter-section/status/{id}', [MetaAcademyPageController::class, 'counterStatus'])->name('counter_section.status')->middleware('permission:cms.meta_academy_page.counter_section.status');
});

// ===================== CMS Partnership Page =====================
Route::prefix('cms/partnership-page')->name('cms.partnership_page.')->group(function () {
    Route::get('/hero-section', [PartnershipPageController::class, 'getHeroSection'])->name('hero_section.create')->middleware('permission:cms.partnership_page.hero_section.create');
    Route::post('/hero-section', [PartnershipPageController::class, 'updateHeroSection'])->name('hero_section.update')->middleware('permission:cms.partnership_page.hero_section.update');

    Route::get('/our-mission-section', [PartnershipPageController::class, 'getOurMissionSection'])->name('our_mission_section.create')->middleware('permission:cms.partnership_page.our_mission_section.create');
    Route::post('/our-mission-section', [PartnershipPageController::class, 'updateOurMissionSection'])->name('our_mission_section.update')->middleware('permission:cms.partnership_page.our_mission_section.update');

    Route::get('/about-us-section', [PartnershipPageController::class, 'getAboutUsSection'])->name('about_us_section.create')->middleware('permission:cms.partnership_page.about_us_section.create');
    Route::post('/about-us-section', [PartnershipPageController::class, 'updateAboutUsSection'])->name('about_us_section.update')->middleware('permission:cms.partnership_page.about_us_section.update');

    Route::get('/partnership-value-section', [PartnershipPageController::class, 'partnershipValueIndex'])->name('partnership_value_section.index')->middleware('permission:cms.partnership_page.partnership_value_section.index');
    Route::get('/partnership-value-section/create', [PartnershipPageController::class, 'partnershipValueCreate'])->name('partnership_value_section.create')->middleware('permission:cms.partnership_page.partnership_value_section.create');
    Route::post('/partnership-value-section', [PartnershipPageController::class, 'partnershipValueStore'])->name('partnership_value_section.store')->middleware('permission:cms.partnership_page.partnership_value_section.store');
    Route::get('/partnership-value-section/{id}/edit', [PartnershipPageController::class, 'partnershipValueEdit'])->name('partnership_value_section.edit')->middleware('permission:cms.partnership_page.partnership_value_section.edit');
    Route::put('/partnership-value-section/{id}', [PartnershipPageController::class, 'partnershipValueUpdate'])->name('partnership_value_section.update')->middleware('permission:cms.partnership_page.partnership_value_section.update');
    Route::delete('/partnership-value-section/{id}', [PartnershipPageController::class, 'partnershipValueDestroy'])->name('partnership_value_section.destroy')->middleware('permission:cms.partnership_page.partnership_value_section.destroy');
    Route::post('/partnership-value-section/status/{id}', [PartnershipPageController::class, 'partnershipValueStatus'])->name('partnership_value_section.status')->middleware('permission:cms.partnership_page.partnership_value_section.status');
    Route::post('/partnership-value-section/update-container', [PartnershipPageController::class, 'updatePartnershipValueContainer'])->name('partnership_value_section.update_container')->middleware('permission:cms.partnership_page.partnership_value_section.update_container');

    Route::get('/core-research-section', [PartnershipPageController::class, 'coreResearchIndex'])->name('core_research_section.index')->middleware('permission:cms.partnership_page.core_research_section.index');
    Route::get('/core-research-section/create', [PartnershipPageController::class, 'coreResearchCreate'])->name('core_research_section.create')->middleware('permission:cms.partnership_page.core_research_section.create');
    Route::post('/core-research-section', [PartnershipPageController::class, 'coreResearchStore'])->name('core_research_section.store')->middleware('permission:cms.partnership_page.core_research_section.store');
    Route::get('/core-research-section/{id}/edit', [PartnershipPageController::class, 'coreResearchEdit'])->name('core_research_section.edit')->middleware('permission:cms.partnership_page.core_research_section.edit');
    Route::put('/core-research-section/{id}', [PartnershipPageController::class, 'coreResearchUpdate'])->name('core_research_section.update')->middleware('permission:cms.partnership_page.core_research_section.update');
    Route::delete('/core-research-section/{id}', [PartnershipPageController::class, 'coreResearchDestroy'])->name('core_research_section.destroy')->middleware('permission:cms.partnership_page.core_research_section.destroy');
    Route::post('/core-research-section/status/{id}', [PartnershipPageController::class, 'coreResearchStatus'])->name('core_research_section.status')->middleware('permission:cms.partnership_page.core_research_section.status');
    Route::post('/core-research-section/update-container', [PartnershipPageController::class, 'updateCoreResearchContainer'])->name('core_research_section.update_container')->middleware('permission:cms.partnership_page.core_research_section.update_container');

    Route::get('/systematic-review-section', [PartnershipPageController::class, 'systematicReviewIndex'])->name('systematic_review_section.index')->middleware('permission:cms.partnership_page.systematic_review_section.index');
    Route::get('/systematic-review-section/create', [PartnershipPageController::class, 'systematicReviewCreate'])->name('systematic_review_section.create')->middleware('permission:cms.partnership_page.systematic_review_section.create');
    Route::post('/systematic-review-section', [PartnershipPageController::class, 'systematicReviewStore'])->name('systematic_review_section.store')->middleware('permission:cms.partnership_page.systematic_review_section.store');
    Route::get('/systematic-review-section/{id}/edit', [PartnershipPageController::class, 'systematicReviewEdit'])->name('systematic_review_section.edit')->middleware('permission:cms.partnership_page.systematic_review_section.edit');
    Route::put('/systematic-review-section/{id}', [PartnershipPageController::class, 'systematicReviewUpdate'])->name('systematic_review_section.update')->middleware('permission:cms.partnership_page.systematic_review_section.update');
    Route::delete('/systematic-review-section/{id}', [PartnershipPageController::class, 'systematicReviewDestroy'])->name('systematic_review_section.destroy')->middleware('permission:cms.partnership_page.systematic_review_section.destroy');
    Route::post('/systematic-review-section/status/{id}', [PartnershipPageController::class, 'systematicReviewStatus'])->name('systematic_review_section.status')->middleware('permission:cms.partnership_page.systematic_review_section.status');
    Route::post('/systematic-review-section/update-container', [PartnershipPageController::class, 'updateSystematicReviewContainer'])->name('systematic_review_section.update_container')->middleware('permission:cms.partnership_page.systematic_review_section.update_container');

    Route::get('/manuscript-section', [PartnershipPageController::class, 'getManuscriptSection'])->name('manuscript_section.create')->middleware('permission:cms.partnership_page.manuscript_section.create');
    Route::post('/manuscript-section', [PartnershipPageController::class, 'updateManuscriptSection'])->name('manuscript_section.update')->middleware('permission:cms.partnership_page.manuscript_section.update');

    Route::get('/partnership-model-section', [PartnershipPageController::class, 'partnershipModelIndex'])->name('partnership_model_section.index')->middleware('permission:cms.partnership_page.partnership_model_section.index');
    Route::get('/partnership-model-section/create', [PartnershipPageController::class, 'partnershipModelCreate'])->name('partnership_model_section.create')->middleware('permission:cms.partnership_page.partnership_model_section.create');
    Route::post('/partnership-model-section', [PartnershipPageController::class, 'partnershipModelStore'])->name('partnership_model_section.store')->middleware('permission:cms.partnership_page.partnership_model_section.store');
    Route::get('/partnership-model-section/{id}/edit', [PartnershipPageController::class, 'partnershipModelEdit'])->name('partnership_model_section.edit')->middleware('permission:cms.partnership_page.partnership_model_section.edit');
    Route::put('/partnership-model-section/{id}', [PartnershipPageController::class, 'partnershipModelUpdate'])->name('partnership_model_section.update')->middleware('permission:cms.partnership_page.partnership_model_section.update');
    Route::delete('/partnership-model-section/{id}', [PartnershipPageController::class, 'partnershipModelDestroy'])->name('partnership_model_section.destroy')->middleware('permission:cms.partnership_page.partnership_model_section.destroy');
    Route::post('/partnership-model-section/status/{id}', [PartnershipPageController::class, 'partnershipModelStatus'])->name('partnership_model_section.status')->middleware('permission:cms.partnership_page.partnership_model_section.status');
    Route::post('/partnership-model-section/update-container', [PartnershipPageController::class, 'updatePartnershipModelContainer'])->name('partnership_model_section.update_container')->middleware('permission:cms.partnership_page.partnership_model_section.update_container');

    Route::get('/reviewer-comment-section', [PartnershipPageController::class, 'getReviewerCommentSection'])->name('reviewer_comment_section.create')->middleware('permission:cms.partnership_page.reviewer_comment_section.create');
    Route::post('/reviewer-comment-section', [PartnershipPageController::class, 'updateReviewerCommentSection'])->name('reviewer_comment_section.update')->middleware('permission:cms.partnership_page.reviewer_comment_section.update');

    Route::get('/journal-section', [PartnershipPageController::class, 'getJournalSection'])->name('journal_section.create')->middleware('permission:cms.partnership_page.journal_section.create');
    Route::post('/journal-section', [PartnershipPageController::class, 'updateJournalSection'])->name('journal_section.update')->middleware('permission:cms.partnership_page.journal_section.update');

    Route::get('/research-opportunitie-section', [PartnershipPageController::class, 'researchOpportunitieIndex'])->name('research_opportunitie_section.index')->middleware('permission:cms.partnership_page.research_opportunitie_section.index');
    Route::get('/research-opportunitie-section/create', [PartnershipPageController::class, 'researchOpportunitieCreate'])->name('research_opportunitie_section.create')->middleware('permission:cms.partnership_page.research_opportunitie_section.create');
    Route::post('/research-opportunitie-section', [PartnershipPageController::class, 'researchOpportunitieStore'])->name('research_opportunitie_section.store')->middleware('permission:cms.partnership_page.research_opportunitie_section.store');
    Route::get('/research-opportunitie-section/{id}/edit', [PartnershipPageController::class, 'researchOpportunitieEdit'])->name('research_opportunitie_section.edit')->middleware('permission:cms.partnership_page.research_opportunitie_section.edit');
    Route::put('/research-opportunitie-section/{id}', [PartnershipPageController::class, 'researchOpportunitieUpdate'])->name('research_opportunitie_section.update')->middleware('permission:cms.partnership_page.research_opportunitie_section.update');
    Route::delete('/research-opportunitie-section/{id}', [PartnershipPageController::class, 'researchOpportunitieDestroy'])->name('research_opportunitie_section.destroy')->middleware('permission:cms.partnership_page.research_opportunitie_section.destroy');
    Route::post('/research-opportunitie-section/status/{id}', [PartnershipPageController::class, 'researchOpportunitieStatus'])->name('research_opportunitie_section.status')->middleware('permission:cms.partnership_page.research_opportunitie_section.status');
    Route::post('/research-opportunitie-section/update-container', [PartnershipPageController::class, 'updateResearchOpportunitieContainer'])->name('research_opportunitie_section.update_container')->middleware('permission:cms.partnership_page.research_opportunitie_section.update_container');

    Route::get('/why-partner-section', [PartnershipPageController::class, 'getWhyPartnerSection'])->name('why_partner_section.create')->middleware('permission:cms.partnership_page.why_partner_section.create');
    Route::post('/why-partner-section', [PartnershipPageController::class, 'updateWhyPartnerSection'])->name('why_partner_section.update')->middleware('permission:cms.partnership_page.why_partner_section.update');

    Route::get('/partner-model-section', [PartnershipPageController::class, 'partnerModelIndex'])->name('partner_model_section.index')->middleware('permission:cms.partnership_page.partner_model_section.index');
    Route::get('/partner-model-section/create', [PartnershipPageController::class, 'partnerModelCreate'])->name('partner_model_section.create')->middleware('permission:cms.partnership_page.partner_model_section.create');
    Route::post('/partner-model-section', [PartnershipPageController::class, 'partnerModelStore'])->name('partner_model_section.store')->middleware('permission:cms.partnership_page.partner_model_section.store');
    Route::get('/partner-model-section/{id}/edit', [PartnershipPageController::class, 'partnerModelEdit'])->name('partner_model_section.edit')->middleware('permission:cms.partnership_page.partner_model_section.edit');
    Route::put('/partner-model-section/{id}', [PartnershipPageController::class, 'partnerModelUpdate'])->name('partner_model_section.update')->middleware('permission:cms.partnership_page.partner_model_section.update');
    Route::delete('/partner-model-section/{id}', [PartnershipPageController::class, 'partnerModelDestroy'])->name('partner_model_section.destroy')->middleware('permission:cms.partnership_page.partner_model_section.destroy');
    Route::post('/partner-model-section/status/{id}', [PartnershipPageController::class, 'partnerModelStatus'])->name('partner_model_section.status')->middleware('permission:cms.partnership_page.partner_model_section.status');
    Route::post('/partner-model-section/update-container', [PartnershipPageController::class, 'updatePartnerModelContainer'])->name('partner_model_section.update_container')->middleware('permission:cms.partnership_page.partner_model_section.update_container');
});


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