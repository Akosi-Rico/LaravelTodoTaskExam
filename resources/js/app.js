import './bootstrap';
import 'datatables.net-dt';
import $ from 'jquery';
window.$ = $;
window.jQuery = $;
import './datatable.pipiline.js';
import { createApp } from 'vue';
import index from './components/index.vue';
import login from './components/auth/login.vue';
import register from './components/auth/register.vue';
createApp({})
  .component('parent', index)
  .component('login', login)
  .component('register', register)
  .mount('#app');
