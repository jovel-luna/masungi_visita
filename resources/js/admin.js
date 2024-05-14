import Vue from 'vue';

Vue.component('dashboard-analytics', require('./views/dashboards/DashboardAnalytics.vue').default);

Vue.component('admin-user-table', require('./views/admin/admin-users/AdminTable.vue').default);
Vue.component('admin-user-view', require('./views/admin/admin-users/AdminView.vue').default);

Vue.component('user-table', require('./views/admin/users/UserTable.vue').default);
Vue.component('user-view', require('./views/admin/users/UserView.vue').default);

Vue.component('role-table', require('./views/admin/roles/RoleTable.vue').default);
Vue.component('role-view', require('./views/admin/roles/RoleView.vue').default);

Vue.component('page-table', require('./views/admin/pages/PageTable.vue').default);
Vue.component('page-view', require('./views/admin/pages/PageView.vue').default);

Vue.component('page-item-table', require('./views/admin/pages/PageItemTable.vue').default);
Vue.component('page-item-view', require('./views/admin/pages/PageItemView.vue').default);

Vue.component('permission-list', require('./views/admin/permissions/PermissionList.vue').default);

Vue.component('article-table', require('./views/admin/articles/ArticleTable.vue').default);
Vue.component('article-view', require('./views/admin/articles/ArticleView.vue').default);

Vue.component('home-banners-table', require('./views/admin/home-banners/HomeBannersTable.vue').default);
Vue.component('home-banners-view', require('./views/admin/home-banners/HomeBannersView.vue').default);

Vue.component('about-infos-table', require('./views/admin/about-infos/AboutInfosTable.vue').default);
Vue.component('about-infos-view', require('./views/admin/about-infos/AboutInfosView.vue').default);

Vue.component('destinations-table', require('./views/admin/destinations/DestinationsTable.vue').default);
Vue.component('destinations-view', require('./views/admin/destinations/DestinationsView.vue').default);

Vue.component('inquiries-table', require('./views/admin/inquiries/InquiriesTable.vue').default);
Vue.component('inquiries-view', require('./views/admin/inquiries/InquiriesView.vue').default);

Vue.component('experiences-table', require('./views/admin/experiences/ExperiencesTable.vue').default);
Vue.component('experiences-view', require('./views/admin/experiences/ExperiencesView.vue').default);

Vue.component('annual-incomes-table', require('./views/admin/annual-incomes/AnnualIncomesTable.vue').default);
Vue.component('annual-incomes-view', require('./views/admin/annual-incomes/AnnualIncomesView.vue').default);

Vue.component('survey-experiences-table', require('./views/admin/survey-experiences/SurveyExperiencesTable.vue').default);
Vue.component('survey-experiences-view', require('./views/admin/survey-experiences/SurveyExperiencesView.vue').default);

Vue.component('allocations-table', require('./views/admin/allocations/AllocationsTable.vue').default);
Vue.component('allocations-view', require('./views/admin/allocations/AllocationsView.vue').default);

Vue.component('add-ons-table', require('./views/admin/add-ons/AddOnsTable.vue').default);
Vue.component('add-ons-view', require('./views/admin/add-ons/AddOnsView.vue').default);

Vue.component('visitor-types-table', require('./views/admin/visitor-types/VisitorTypesTable.vue').default);
Vue.component('visitor-types-view', require('./views/admin/visitor-types/VisitorTypesView.vue').default);

Vue.component('calendar-view', require('./views/admin/calendars/CalendarView.vue').default);

Vue.component('bookings-table', require('./views/admin/bookings/BookingsTable.vue').default);
Vue.component('bookings-view', require('./views/admin/bookings/BookingsView.vue').default);

Vue.component('fees-table', require('./views/admin/fees/FeesTable.vue').default);
Vue.component('fees-view', require('./views/admin/fees/FeesView.vue').default);

Vue.component('feedbacks-table', require('./views/admin/feedbacks/FeedbacksTable.vue').default);
Vue.component('feedbacks-view', require('./views/admin/feedbacks/FeedbacksView.vue').default);

Vue.component('blocked-dates-table', require('./views/admin/blocked-dates/BlockedDatesTable.vue').default);
Vue.component('blocked-dates-view', require('./views/admin/blocked-dates/BlockedDatesView.vue').default);

Vue.component('managements-table', require('./views/admin/managements/ManagementsTable.vue').default);
Vue.component('managements-view', require('./views/admin/managements/ManagementView.vue').default);

Vue.component('training-modules-table', require('./views/admin/training-modules/TrainingModulesTable.vue').default);
Vue.component('training-modules-view', require('./views/admin/training-modules/TrainingModulesView.vue').default);

Vue.component('faqs-table', require('./views/admin/faqs/FaqsTable.vue').default);
Vue.component('faqs-view', require('./views/admin/faqs/FaqsView.vue').default);

Vue.component('payments-table', require('./views/admin/payments/PaymentsTable.vue').default);
Vue.component('payments-view', require('./views/admin/payments/PaymentsView.vue').default);

Vue.component('capacities-table', require('./views/admin/capacities/CapacitiesTable.vue').default);
Vue.component('capacities-view', require('./views/admin/capacities/CapacitiesView.vue').default);

Vue.component('agencies-table', require('./views/admin/agencies/AgenciesTable.vue').default);
Vue.component('agencies-view', require('./views/admin/agencies/AgenciesView.vue').default);

Vue.component('religions-table', require('./views/admin/religions/ReligionsTable.vue').default);
Vue.component('religions-view', require('./views/admin/religions/ReligionsView.vue').default);

Vue.component('announcements-table', require('./views/admin/announcements/AnnouncementsTable.vue').default);
Vue.component('announcements-view', require('./views/admin/announcements/AnnouncementsView.vue').default);

Vue.component('remarks-table', require('./views/admin/remarks/RemarksTable.vue').default);
Vue.component('remarks-view', require('./views/admin/remarks/RemarksView.vue').default);

Vue.component('violations-table', require('./views/admin/violations/ViolationsTable.vue').default);
Vue.component('violations-view', require('./views/admin/violations/ViolationsView.vue').default);

Vue.component('group-remarks-table', require('./views/admin/group-remarks/GroupRemarksTable.vue').default);

Vue.component('group-violation-table', require('./views/admin/group-violations/GroupViolationTable.vue').default);

Vue.component('guest-feedback-table', require('./views/admin/guest-feedbacks/GuestFeedbackTable.vue').default);

Vue.component('surveys-table', require('./views/admin/surveys/SurveysTable.vue').default);
Vue.component('surveys-view', require('./views/admin/surveys/SurveysView.vue').default);

Vue.component('surveys-answer-table', require('./views/admin/surveys/SurveyAnswerTable.vue').default);

Vue.component('genders-table', require('./views/admin/genders/GendersTable.vue').default);
Vue.component('genders-view', require('./views/admin/genders/GendersView.vue').default);

Vue.component('civil_statuses-table', require('./views/admin/civil_statuses/CivilStatusesTable.vue').default);
Vue.component('civil_statuses-view', require('./views/admin/civil_statuses/CivilStatusesView.vue').default);

Vue.component('time-slots-table', require('./views/admin/time-slots/TimeSlotTable.vue').default);
Vue.component('time-slots-view', require('./views/admin/time-slots/TimeSlotView.vue').default);

Vue.component('invoices-view', require('./views/admin/bookings/InvoiceView.vue').default);
Vue.component('export-view', require('./views/admin/exports/ExportView.vue').default);

Vue.component('about-us-table', require('./views/admin/about-us/AboutUsTable.vue').default);
Vue.component('about-us-view', require('./views/admin/about-us/AboutUsView.vue').default);

Vue.component('teams-table', require('./views/admin/teams/TeamTable.vue').default);
Vue.component('teams-view', require('./views/admin/teams/TeamView.vue').default);

Vue.component('frame-three-table', require('./views/admin/frame-three/FrameThreeTable.vue').default);
Vue.component('frame-three-view', require('./views/admin/frame-three/FrameThreeView.vue').default);

Vue.component('generated-emails-table', require('./views/admin/generated-emails/GeneratedEmailsTable.vue').default);
Vue.component('generated-emails-view', require('./views/admin/generated-emails/GeneratedEmailsView.vue').default);

Vue.component('bookings-v2-table', require('./views/admin/bookings/version2/BookingsTable.vue').default);
Vue.component('bookings-v2-view', require('./views/admin/bookings/version2/BookingsView.vue').default);

Vue.component('copywritings-table', require('./views/admin/copywritings/CopywritingsTable.vue').default);
Vue.component('copywriting-view', require('./views/admin/copywritings/CopywritingView.vue').default);

Vue.component('conservation-fees-table', require('./views/admin/conservation-fees/ConservationFeesTable.vue').default);
Vue.component('conservation-fee-view', require('./views/admin/conservation-fees/ConservationFeeView.vue').default);

Vue.component('age-ranges-table', require('./views/admin/age-ranges/AgeRangesTable.vue').default);
Vue.component('age-range-view', require('./views/admin/age-ranges/AgeRangeView.vue').default);

Vue.component('nationalities-table', require('./views/admin/nationalities/NationalitiesTable.vue').default);
Vue.component('nationality-view', require('./views/admin/nationalities/NationalityView.vue').default);

Vue.component('sources-table', require('./views/admin/sources/SourcesTable.vue').default);
Vue.component('source-view', require('./views/admin/sources/SourceView.vue').default);