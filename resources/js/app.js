import './bootstrap';
import { createApp } from 'vue/dist/vue.esm-bundler.js';

import SearchAll from "./components/service-connections/search-all.vue"
import SearchMembership from "./components/member-consumers/search-membership.vue"

const app = createApp({});
app.component('search-all', SearchAll);
app.component('search-membership', SearchMembership);

app.mount("#app");
