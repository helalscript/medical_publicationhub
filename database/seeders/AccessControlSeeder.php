<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class AccessControlSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $roles = ['admin', 'coordinator', 'mentor', 'researcher', 'user'];
        foreach ($roles as $roleName) {
            Role::findOrCreate($roleName, 'web');
        }

        $permissions = [];

        $add = function (string $group, string $name) use (&$permissions): void {
            $permissions[] = ['group' => $group, 'name' => $name];
        };

        $crud = function (string $group, string $prefix, bool $hasShow = true) use ($add): void {
            $add($group, "{$prefix}.index");
            $add($group, "{$prefix}.create");
            $add($group, "{$prefix}.store");
            if ($hasShow) {
                $add($group, "{$prefix}.show");
            }
            $add($group, "{$prefix}.edit");
            $add($group, "{$prefix}.update");
            $add($group, "{$prefix}.destroy");
        };

        // ===================== Access Control =====================
        $add('Access Control', 'access_control.roles.view');
        $add('Access Control', 'access_control.roles.create');
        $add('Access Control', 'access_control.roles.update');
        $add('Access Control', 'access_control.roles.delete');
        $add('Access Control', 'access_control.roles.assign_permissions');
        $add('Access Control', 'access_control.permissions.view');
        $add('Access Control', 'access_control.permissions.create');
        $add('Access Control', 'access_control.permissions.update');
        $add('Access Control', 'access_control.permissions.delete');

        // ===================== Dashboard / Profile / Settings =====================
        $add('Dashboard', 'admin.dashboard');
        $add('Profile Settings', 'profile_settings.index');
        $add('Profile Settings', 'profile_settings.update');
        $add('Profile Settings', 'profile_settings.password_change');
        $add('Profile Settings', 'profile_settings.password');

        $add('System Settings', 'system_settings.index');
        $add('System Settings', 'system_settings.update');
        $add('System Settings', 'system_settings.calendly_get');
        $add('System Settings', 'system_settings.calendly_update');
        $add('System Settings', 'system_settings.mail_get');
        $add('System Settings', 'system_settings.mail');

        // ===================== Dynamic Pages =====================
        $crud('Dynamic Pages', 'dynamic_page', true);
        $add('Dynamic Pages', 'dynamic_page.status');

        // ===================== Users (App Users) =====================
        $crud('Users', 'users', true);
        $add('Users', 'users.status');

        // ===================== User Management (Mentor / Researcher / Coordinator) =====================
        $crud('User Management - Mentor', 'user_management.mentor', true);
        $add('User Management - Mentor', 'user_management.mentor.status');

        $crud('User Management - Researcher', 'user_management.researcher', true);
        $add('User Management - Researcher', 'user_management.researcher.status');

        $crud('User Management - Coordinator', 'user_management.coordinator', true);
        $add('User Management - Coordinator', 'user_management.coordinator.status');

        // ===================== Research Ideas (Admin Review) =====================
        $add('Research Ideas', 'admin.research_ideas.index');
        $add('Research Ideas', 'admin.research_ideas.show');
        $add('Research Ideas', 'admin.research_ideas.edit');
        $add('Research Ideas', 'admin.research_ideas.update');

        // ===================== Opportunities =====================
        $crud('Opportunities', 'admin.opportunities', true);
        $add('Opportunities', 'admin.opportunities.status');

        // ===================== Opportunity Applications =====================
        $add('Opportunity Applications', 'admin.opportunity_applications.index');
        $add('Opportunity Applications', 'admin.opportunity_applications.show');
        $add('Opportunity Applications', 'admin.opportunity_applications.edit');
        $add('Opportunity Applications', 'admin.opportunity_applications.update');
        $add('Opportunity Applications', 'admin.opportunity_applications.destroy');

        // ===================== Opportunity Chats =====================
        $add('Opportunity Chats', 'admin.opportunity_chats.index');
        $add('Opportunity Chats', 'admin.opportunity_chats.show');
        $add('Opportunity Chats', 'admin.opportunity_chats.messages.store');

        // ===================== Courses =====================
        $crud('Courses', 'admin.courses', true);
        $add('Courses', 'admin.courses.status');

        // Modules (show = detail + media preview)
        $crud('Modules', 'admin.modules', false);
        $add('Modules', 'admin.modules.show');
        $add('Modules', 'admin.modules.status');

        // Lessons (show separate: list/detail + video preview)
        $crud('Lessons', 'admin.lessons', false);
        $add('Lessons', 'admin.lessons.show');
        $add('Lessons', 'admin.lessons.status');

        // Course access
        $add('Course Access', 'admin.course_access.index');
        $add('Course Access', 'admin.course_access.grant');
        $add('Course Access', 'admin.course_access.revoke');

        // Payments
        $add('Payments', 'admin.payments.index');
        $add('Payments', 'admin.payments.show');
        $add('Payments', 'admin.payments.status');

        // ===================== Publications =====================
        $crud('Publications', 'admin.publications', true);
        $add('Publications', 'admin.publications.status');

        // ===================== Partnership Requests =====================
        $add('Partnership Requests', 'admin.partnership_requests.index');
        $add('Partnership Requests', 'admin.partnership_requests.show');
        $add('Partnership Requests', 'admin.partnership_requests.destroy');
        $add('Partnership Requests', 'admin.partnership_requests.status');

        // ===================== Services =====================
        $crud('Services', 'admin.services', true);
        $add('Services', 'admin.services.status');
        $add('Services', 'admin.services.featured');

        // ===================== FAQs =====================
        $crud("FAQ's", 'faqs', true);
        $add("FAQ's", 'faqs.status');

        // ===================== FAQ Categories =====================
        $crud('FAQ Categories', 'faq_categories', true);
        $add('FAQ Categories', 'faq_categories.status');

        // ===================== Social Links =====================
        $crud('Social Links', 'social_link', false);
        $add('Social Links', 'social_link.status');

        // ===================== Blogs =====================
        $crud('Blogs', 'blogs', true);
        $add('Blogs', 'blogs.status');
        $add('Blogs', 'blogs.update_container');

        // ===================== Notifications =====================
        $add('Notifications', 'notification.read');
        $add('Notifications', 'notification.read_single');
        $add('Notifications', 'notification.delete');
        $add('Notifications', 'notification.deleteall');

        // ===================== Contact Messages =====================
        $add('Contact Messages', 'admin_contact_us.index');
        $add('Contact Messages', 'admin_contact_us.show');
        $add('Contact Messages', 'admin_contact_us.destroy');

        // ===================== Newsletters =====================
        $add('Newsletters', 'admin.newsletters.index');

        // ===================== CMS (cms.php) =====================
        // CMS Home Page
        $add('CMS - Home Page', 'cms.home_page.hero_section.create');
        $add('CMS - Home Page', 'cms.home_page.hero_section.update');
        $crud('CMS - Home Page (Counter Section)', 'cms.home_page.counter_section', true);
        $add('CMS - Home Page (Counter Section)', 'cms.home_page.counter_section.status');

        $crud('CMS - Home Page (Why Researchers Choose)', 'cms.home_page.why_researchers_choose_section', true);
        $add('CMS - Home Page (Why Researchers Choose)', 'cms.home_page.why_researchers_choose_section.status');
        $add('CMS - Home Page (Why Researchers Choose)', 'cms.home_page.why_researchers_choose_section.update_container');

        $add('CMS - Home Page', 'cms.home_page.featured_research_opportunities_section.create');
        $add('CMS - Home Page', 'cms.home_page.featured_research_opportunities_section.update');

        $crud('CMS - Home Page (Review Section)', 'cms.home_page.review_section', true);
        $add('CMS - Home Page (Review Section)', 'cms.home_page.review_section.status');
        $add('CMS - Home Page (Review Section)', 'cms.home_page.review_section.update_container');

        $add('CMS - Home Page', 'cms.home_page.meta_academy_section.create');
        $add('CMS - Home Page', 'cms.home_page.meta_academy_section.update');
        $add('CMS - Home Page', 'cms.home_page.research_journey_section.create');
        $add('CMS - Home Page', 'cms.home_page.research_journey_section.update');
        $add('CMS - Home Page', 'cms.home_page.faq_section.create');
        $add('CMS - Home Page', 'cms.home_page.faq_section.update');

        // CMS FAQ Page
        $add('CMS - FAQ Page', 'cms.faq_page.hero_section.create');
        $add('CMS - FAQ Page', 'cms.faq_page.hero_section.update');

        // CMS Research Page
        $add('CMS - Research Page', 'cms.research_page.research_section.create');
        $add('CMS - Research Page', 'cms.research_page.research_section.update');

        // CMS Service Page
        $add('CMS - Service Page', 'cms.service_page.service_section.create');
        $add('CMS - Service Page', 'cms.service_page.service_section.update');

        // CMS Aboutus Page
        $add('CMS - Aboutus Page', 'cms.aboutus_page.hero_section.create');
        $add('CMS - Aboutus Page', 'cms.aboutus_page.hero_section.update');
        // $add('CMS - Aboutus Page', 'cms.aboutus_page.our_mission_section.create');
        $add('CMS - Aboutus Page', 'cms.aboutus_page.our_mission_section.update');
        $add('CMS - Aboutus Page', 'cms.aboutus_page.our_mission_items.index');
        $add('CMS - Aboutus Page', 'cms.aboutus_page.our_mission_items.create');
        $add('CMS - Aboutus Page', 'cms.aboutus_page.our_mission_items.store');
        $add('CMS - Aboutus Page', 'cms.aboutus_page.our_mission_items.edit');
        $add('CMS - Aboutus Page', 'cms.aboutus_page.our_mission_items.update');
        $add('CMS - Aboutus Page', 'cms.aboutus_page.our_mission_items.destroy');
        $add('CMS - Aboutus Page', 'cms.aboutus_page.our_mission_items.status');

        // CMS Meta Academy Page
        $add('CMS - Meta Academy Page', 'cms.meta_academy_page.hero_section.create');
        $add('CMS - Meta Academy Page', 'cms.meta_academy_page.hero_section.update');
        $add('CMS - Meta Academy Page (Counter Section)', 'cms.meta_academy_page.counter_section.index');
        $add('CMS - Meta Academy Page (Counter Section)', 'cms.meta_academy_page.counter_section.create');
        $add('CMS - Meta Academy Page (Counter Section)', 'cms.meta_academy_page.counter_section.store');
        $add('CMS - Meta Academy Page (Counter Section)', 'cms.meta_academy_page.counter_section.edit');
        $add('CMS - Meta Academy Page (Counter Section)', 'cms.meta_academy_page.counter_section.update');
        $add('CMS - Meta Academy Page (Counter Section)', 'cms.meta_academy_page.counter_section.destroy');
        $add('CMS - Meta Academy Page (Counter Section)', 'cms.meta_academy_page.counter_section.status');

        // CMS Partnership Page (many sections)
        $add('CMS - Partnership Page', 'cms.partnership_page.hero_section.create');
        $add('CMS - Partnership Page', 'cms.partnership_page.hero_section.update');
        $add('CMS - Partnership Page', 'cms.partnership_page.our_mission_section.create');
        $add('CMS - Partnership Page', 'cms.partnership_page.our_mission_section.update');
        $add('CMS - Partnership Page', 'cms.partnership_page.about_us_section.create');
        $add('CMS - Partnership Page', 'cms.partnership_page.about_us_section.update');

        $add('CMS - Partnership Page (Value Section)', 'cms.partnership_page.partnership_value_section.index');
        $add('CMS - Partnership Page (Value Section)', 'cms.partnership_page.partnership_value_section.create');
        $add('CMS - Partnership Page (Value Section)', 'cms.partnership_page.partnership_value_section.store');
        $add('CMS - Partnership Page (Value Section)', 'cms.partnership_page.partnership_value_section.edit');
        $add('CMS - Partnership Page (Value Section)', 'cms.partnership_page.partnership_value_section.update');
        $add('CMS - Partnership Page (Value Section)', 'cms.partnership_page.partnership_value_section.destroy');
        $add('CMS - Partnership Page (Value Section)', 'cms.partnership_page.partnership_value_section.status');
        $add('CMS - Partnership Page (Value Section)', 'cms.partnership_page.partnership_value_section.update_container');

        $add('CMS - Partnership Page (Core Research)', 'cms.partnership_page.core_research_section.index');
        $add('CMS - Partnership Page (Core Research)', 'cms.partnership_page.core_research_section.create');
        $add('CMS - Partnership Page (Core Research)', 'cms.partnership_page.core_research_section.store');
        $add('CMS - Partnership Page (Core Research)', 'cms.partnership_page.core_research_section.edit');
        $add('CMS - Partnership Page (Core Research)', 'cms.partnership_page.core_research_section.update');
        $add('CMS - Partnership Page (Core Research)', 'cms.partnership_page.core_research_section.destroy');
        $add('CMS - Partnership Page (Core Research)', 'cms.partnership_page.core_research_section.status');
        $add('CMS - Partnership Page (Core Research)', 'cms.partnership_page.core_research_section.update_container');

        $add('CMS - Partnership Page (Systematic Review)', 'cms.partnership_page.systematic_review_section.index');
        $add('CMS - Partnership Page (Systematic Review)', 'cms.partnership_page.systematic_review_section.create');
        $add('CMS - Partnership Page (Systematic Review)', 'cms.partnership_page.systematic_review_section.store');
        $add('CMS - Partnership Page (Systematic Review)', 'cms.partnership_page.systematic_review_section.edit');
        $add('CMS - Partnership Page (Systematic Review)', 'cms.partnership_page.systematic_review_section.update');
        $add('CMS - Partnership Page (Systematic Review)', 'cms.partnership_page.systematic_review_section.destroy');
        $add('CMS - Partnership Page (Systematic Review)', 'cms.partnership_page.systematic_review_section.status');
        $add('CMS - Partnership Page (Systematic Review)', 'cms.partnership_page.systematic_review_section.update_container');

        $add('CMS - Partnership Page', 'cms.partnership_page.manuscript_section.create');
        $add('CMS - Partnership Page', 'cms.partnership_page.manuscript_section.update');

        $add('CMS - Partnership Page (Partnership Model)', 'cms.partnership_page.partnership_model_section.index');
        $add('CMS - Partnership Page (Partnership Model)', 'cms.partnership_page.partnership_model_section.create');
        $add('CMS - Partnership Page (Partnership Model)', 'cms.partnership_page.partnership_model_section.store');
        $add('CMS - Partnership Page (Partnership Model)', 'cms.partnership_page.partnership_model_section.edit');
        $add('CMS - Partnership Page (Partnership Model)', 'cms.partnership_page.partnership_model_section.update');
        $add('CMS - Partnership Page (Partnership Model)', 'cms.partnership_page.partnership_model_section.destroy');
        $add('CMS - Partnership Page (Partnership Model)', 'cms.partnership_page.partnership_model_section.status');
        $add('CMS - Partnership Page (Partnership Model)', 'cms.partnership_page.partnership_model_section.update_container');

        $add('CMS - Partnership Page', 'cms.partnership_page.reviewer_comment_section.create');
        $add('CMS - Partnership Page', 'cms.partnership_page.reviewer_comment_section.update');
        $add('CMS - Partnership Page', 'cms.partnership_page.journal_section.create');
        $add('CMS - Partnership Page', 'cms.partnership_page.journal_section.update');

        $add('CMS - Partnership Page (Research Opportunity)', 'cms.partnership_page.research_opportunitie_section.index');
        $add('CMS - Partnership Page (Research Opportunity)', 'cms.partnership_page.research_opportunitie_section.create');
        $add('CMS - Partnership Page (Research Opportunity)', 'cms.partnership_page.research_opportunitie_section.store');
        $add('CMS - Partnership Page (Research Opportunity)', 'cms.partnership_page.research_opportunitie_section.edit');
        $add('CMS - Partnership Page (Research Opportunity)', 'cms.partnership_page.research_opportunitie_section.update');
        $add('CMS - Partnership Page (Research Opportunity)', 'cms.partnership_page.research_opportunitie_section.destroy');
        $add('CMS - Partnership Page (Research Opportunity)', 'cms.partnership_page.research_opportunitie_section.status');
        $add('CMS - Partnership Page (Research Opportunity)', 'cms.partnership_page.research_opportunitie_section.update_container');

        $add('CMS - Partnership Page', 'cms.partnership_page.why_partner_section.create');
        $add('CMS - Partnership Page', 'cms.partnership_page.why_partner_section.update');

        $add('CMS - Partnership Page (Partner Model)', 'cms.partnership_page.partner_model_section.index');
        $add('CMS - Partnership Page (Partner Model)', 'cms.partnership_page.partner_model_section.create');
        $add('CMS - Partnership Page (Partner Model)', 'cms.partnership_page.partner_model_section.store');
        $add('CMS - Partnership Page (Partner Model)', 'cms.partnership_page.partner_model_section.edit');
        $add('CMS - Partnership Page (Partner Model)', 'cms.partnership_page.partner_model_section.update');
        $add('CMS - Partnership Page (Partner Model)', 'cms.partnership_page.partner_model_section.destroy');
        $add('CMS - Partnership Page (Partner Model)', 'cms.partnership_page.partner_model_section.status');
        $add('CMS - Partnership Page (Partner Model)', 'cms.partnership_page.partner_model_section.update_container');

        foreach ($permissions as $perm) {
            /** @var Permission $permission */
            $permission = Permission::query()->firstOrCreate(
                ['name' => $perm['name'], 'guard_name' => 'web'],
                ['group' => $perm['group']]
            );

            if (($perm['group'] ?? null) && ! $permission->group) {
                $permission->group = $perm['group'];
                $permission->save();
            }
        }

        $adminRole = Role::findByName('admin', 'web');
        $adminRole->syncPermissions(Permission::query()->where('guard_name', 'web')->get());

        // Sync web roles with existing users.role column
        User::query()
            ->select(['id', 'role'])
            ->whereNotNull('role')
            ->chunkById(200, function ($users) {
                foreach ($users as $user) {
                    $role = is_string($user->role) ? strtolower($user->role) : null;
                    if ($role) {
                        $user->syncRoles([$role]);
                    }
                }
            });

        // Give coordinators a sensible default set (can be changed from UI)
        $coordinatorRole = Role::findByName('coordinator', 'web');
        $coordinatorRole->syncPermissions(Permission::query()->whereIn('name', [
            'admin.dashboard',
            'admin.courses.index',
            'admin.courses.show',
            'admin.modules.index',
            'admin.modules.show',
            'admin.lessons.index',
            'admin.lessons.show',
            'admin.payments.index',
            'admin.payments.show',
            'admin.publications.index',
            'admin.publications.show',
            'admin.services.index',
            'admin.services.show',
            'admin.opportunities.index',
            'admin.opportunities.show',
        ])->get());
    }
}

