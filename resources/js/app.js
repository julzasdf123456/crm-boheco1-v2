import './bootstrap';
import { createApp } from 'vue/dist/vue.esm-bundler.js';

const SearchAll = defineAsyncComponent(() => import('./components/service-connections/search-all.vue'));
const SelectMCO = defineAsyncComponent(() => import('./components/service-connections/select-mco.vue'));
const SearchMembership = defineAsyncComponent(() => import('./components/member-consumers/search-membership.vue'));
const SearchTickets = defineAsyncComponent(() => import('./components/tickets/search-tickets.vue'));
const BillingDashboard = defineAsyncComponent(() => import('./components/billing/billing-dashboard.vue'));
const SearchAccounts = defineAsyncComponent(() => import('./components/service-accounts/search-accounts.vue'));
const ViewAccount = defineAsyncComponent(() => import('./components/service-accounts/view-account.vue'));
const Ledger = defineAsyncComponent(() => import('./components/service-accounts/ledger.vue'));
const ReadingHistory = defineAsyncComponent(() => import('./components/service-accounts/reading-history.vue'));
const ReadingMonitor = defineAsyncComponent(() => import('./components/readings/reading-monitor.vue'));

const app = createApp({});
app.component('search-all', SearchAll);
app.component('search-membership', SearchMembership);
app.component('search-tickets', SearchTickets);
app.component('select-mco', SelectMCO);
app.component('billing-dashboard', BillingDashboard);
app.component('search-accounts', SearchAccounts);
app.component('view-account', ViewAccount);
app.component('ledger', Ledger);
app.component('reading-history', ReadingHistory);
app.component('reading-monitor', ReadingMonitor);

app.mount("#app");
