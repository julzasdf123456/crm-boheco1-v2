import './bootstrap';
import { createApp } from 'vue/dist/vue.esm-bundler.js';

import SearchAll from "./components/service-connections/search-all.vue"
import SelectMCO from "./components/service-connections/select-mco.vue"
import SearchMembership from "./components/member-consumers/search-membership.vue"
import SearchTickets from "./components/tickets/search-tickets.vue"
import BillingDashboard from "./components/billing/billing-dashboard.vue"
import SearchAccounts from "./components/service-accounts/search-accounts.vue"
import ViewAccount from "./components/service-accounts/view-account.vue"
import Ledger from "./components/service-accounts/ledger.vue"
import ReadingHistory from "./components/service-accounts/reading-history.vue"
import ReadingMonitor from "./components/readings/reading-monitor.vue"

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
