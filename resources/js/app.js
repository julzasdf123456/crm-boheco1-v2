import './bootstrap';
import { createApp } from 'vue/dist/vue.esm-bundler.js';

import SearchAll from "./components/service-connections/search-all.vue"

const app = createApp({});
app.component('search-all', SearchAll);

app.mount("#app");
