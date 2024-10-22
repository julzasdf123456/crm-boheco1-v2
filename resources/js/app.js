import './bootstrap';
import { createApp } from 'vue/dist/vue.esm-bundler.js';

import SearchAll from "./components/service-connections/search-all.vue"
import SelectMCO from "./components/service-connections/select-mco.vue"
import SearchMembership from "./components/member-consumers/search-membership.vue"
import SearchTickets from "./components/tickets/search-tickets.vue"
import BillingDashboard from "./components/billing/billing-dashboard.vue"

const app = createApp({});
app.component('search-all', SearchAll);
app.component('search-membership', SearchMembership);
app.component('search-tickets', SearchTickets);
app.component('select-mco', SelectMCO);
app.component('billing-dashboard', BillingDashboard);

app.mount("#app");
