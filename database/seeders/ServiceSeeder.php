<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Service::factory()->count(20)->create();
        $services = [
            [
                'title' => 'Statistical Analysis & Biostatistics',
                'icon' => null,
                'overview' => 'End-to-end biostatistical analysis with a publication-ready Results section and a fully drafted Statistical Analysis subsection for your Methods.',
                'description' => '<p>Comprehensive biostatistical analysis tailored to the unique demands of your research. We deliver a complete Results section with formatted tables and high-resolution figures, plus a fully drafted Statistical Analysis subsection. We support observational, experimental, clinical, and survey-based studies, applying techniques from descriptive and inferential statistics to multivariable regression, survival analysis, mixed-effects models, and propensity score methods.</p>',
                'starting_price' => null,
                'whats_app_num' => null,
                'is_features' => true,
                'status' => 'active',
            ],
            [
                'title' => 'Comprehensive Meta-Analysis Package',
                'icon' => null,
                'overview' => 'A turnkey meta-analysis pipeline — idea validation, screening, extraction, and full quantitative synthesis in one package.',
                'description' => '<p>End-to-end meta-analysis for researchers who want a publication-grade study without managing the workflow. Begins with a validity check of your research question, followed by systematic database searches, dual-reviewer screening, structured data extraction, and full quantitative synthesis — including heterogeneity, subgroup, sensitivity analyses, and publication bias evaluation. Delivered as a PRISMA-compliant manuscript ready for high-impact publication.</p>',
                'starting_price' => null,
                'whats_app_num' => null,
                'is_features' => true,
                'status' => 'active',
            ],
            [
                'title' => 'Network Meta-Analysis with Results Writing',
                'icon' => null,
                'overview' => 'Pairwise and network meta-analysis of your extracted data, delivered with a fully written, publication-ready Results section.',
                'description' => '<p>For teams who have completed screening and data extraction. We perform pairwise and network meta-analyses using fixed- or random-effects, frequentist or Bayesian frameworks. Deliverables include forest plots, funnel plots, league tables, network geometry diagrams, SUCRA rankings, inconsistency and heterogeneity assessments, and sensitivity analyses — integrated into a fully drafted Results section. Formatting and submission not included.</p>',
                'starting_price' => null,
                'whats_app_num' => null,
                'is_features' => true,
                'status' => 'active',
            ],
            [
                'title' => 'Formatting & Submission (One Round)',
                'icon' => null,
                'overview' => 'Full journal-specific formatting plus end-to-end submission to your target journal — handled in one complete round.',
                'description' => '<p>Complete formatting and submission service. We format the manuscript to your target journal\'s guidelines (layout, structure, figures, tables, reference styling), prepare any required supplementary documents (cover letter, title page, conflict-of-interest, author contributions), and handle the full submission process through the journal\'s portal. Delivered as one complete round, ending with successful submission confirmation.</p>',
                'starting_price' => 860.00,
                'whats_app_num' => null,
                'is_features' => true,
                'status' => 'active',
            ],
            [
                'title' => 'English Editing & Proofreading',
                'icon' => null,
                'overview' => 'Native-quality academic editing that polishes grammar, clarity, and flow without altering your scientific voice.',
                'description' => '<p>Precision language editing crafted for scientific manuscripts. Our editors refine your work for grammar, syntax, clarity, logical flow, and tone, while preserving your authorial voice and technical accuracy. Suitable for journal articles, theses, grant applications, and conference abstracts — your manuscript leaves us meeting the linguistic standards expected by international peer-reviewed journals.</p>',
                'starting_price' => null,
                'whats_app_num' => null,
                'is_features' => true,
                'status' => 'active',
            ],
            [
                'title' => 'Research Consultation & Pre-Submission Peer Review',
                'icon' => null,
                'overview' => 'Expert advisory and rigorous pre-submission review to strengthen your manuscript before it reaches an editor\'s desk.',
                'description' => '<p>A senior-level consultation pairing you with experienced reviewers from your field. We provide structured guidance on study design, methodology, statistical strategy, and manuscript architecture, plus a detailed pre-submission peer review modelled on high-impact journal standards. Reviewers identify methodological weaknesses, gaps in argumentation, missing analyses, and presentation issues — with prioritized recommendations.</p>',
                'starting_price' => 350.00,
                'whats_app_num' => null,
                'is_features' => true,
                'status' => 'active',
            ],
            [
                'title' => 'Reviewer Comments Support',
                'icon' => null,
                'overview' => 'We help authors respond effectively to reviewer feedback — clear, persuasive, point-by-point replies that move your manuscript toward acceptance.',
                'description' => '<p>Dedicated support for one of the most decisive stages of the publication process. We help craft clear, comprehensive, and diplomatically worded point-by-point responses, advising on which revisions to accept, which to defend, and how to articulate each decision with evidence and tact. Includes refinement of the rebuttal letter and final coherence check across the revised manuscript.</p>',
                'starting_price' => null,
                'whats_app_num' => null,
                'is_features' => true,
                'status' => 'active',
            ],
            [
                'title' => 'AI & Machine Learning Model Analysis',
                'icon' => null,
                'overview' => 'End-to-end development and validation of machine learning models for research — from data preparation to interpretable, publication-ready results.',
                'description' => '<p>Specialized service for researchers integrating AI/ML. Full pipeline: preprocessing, feature engineering, model selection (logistic regression, random forests, gradient boosting, SVMs, deep learning), tuning, cross-validation, and rigorous evaluation (AUC-ROC, calibration, decision curves). Includes interpretability outputs (SHAP, feature importance), drafted Methods, publication-ready figures, and reproducible code. TRIPOD-AI / CONSORT-AI compliant.</p>',
                'starting_price' => null,
                'whats_app_num' => null,
                'is_features' => true,
                'status' => 'active',
            ],
            [
                'title' => 'Plagiarism Check (iThenticate)',
                'icon' => null,
                'overview' => 'Official iThenticate similarity report so you can submit with confidence that your manuscript meets journal originality standards.',
                'description' => '<p>Professional plagiarism screening powered by iThenticate, the same tool used by leading academic publishers. We run your manuscript through iThenticate\'s comprehensive database — covering published literature, online content, and student papers — and deliver a full similarity report with section-by-section breakdown, source highlights, and overall similarity score. Helps resolve any inadvertent overlaps before submission.</p>',
                'starting_price' => 150.00,
                'whats_app_num' => null,
                'is_features' => true,
                'status' => 'active',
            ],
            [
                'title' => 'Meta-Analysis Validity Check',
                'icon' => null,
                'overview' => 'Pre-study feasibility assessment that confirms your meta-analysis idea is methodologically valid and not already published.',
                'description' => '<p>A focused pre-study assessment for researchers planning a meta-analysis. Reviewers evaluate whether your research question is methodologically suitable — feasibility, expected literature volume, study design compatibility, and likelihood of meaningful synthesis — and conduct a comprehensive prior-publication search. Result: a clear go / no-go recommendation with rationale.</p>',
                'starting_price' => 300.00,
                'whats_app_num' => null,
                'is_features' => true,
                'status' => 'active',
            ],
            [
                'title' => 'Formatting & Referencing (One Round)',
                'icon' => null,
                'overview' => 'One round of full manuscript formatting and reference styling according to your target journal\'s guidelines.',
                'description' => '<p>For authors who need their manuscript shaped to a specific journal\'s specifications. We format the document end-to-end — layout, headings, section structure, figure and table styling, in-text citation style, and full reference list formatting. Delivered as one complete round, leaving your manuscript polished and consistent with the journal\'s expectations. Submission not included.</p>',
                'starting_price' => 400.00,
                'whats_app_num' => null,
                'is_features' => true,
                'status' => 'active',
            ],
            [
                'title' => 'Publication Support Package',
                'icon' => null,
                'overview' => 'A complete submission-ready bundle: English editing & proofreading, journal selection consultation, and three rounds of formatting & submission.',
                'description' => '<p>Comprehensive package for authors preparing a manuscript for publication. Includes full English editing and proofreading; a personalized journal selection consultation matched to your study\'s scope and impact goals; and three rounds of formatting and submission to your chosen journals — covering layout, references, figures, cover letter drafting, and submission portal handling. We manage the editorial workflow end-to-end across all three rounds.</p>',
                'starting_price' => 2000.00,
                'whats_app_num' => null,
                'is_features' => true,
                'status' => 'active',
            ],
        ];

        foreach ($services as $service) {
            Service::create($service);
        }
    }
}
