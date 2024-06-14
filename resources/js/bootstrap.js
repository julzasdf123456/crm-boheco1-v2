import 'bootstrap';

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

window.axios.defaults.baseURL = '/crm-boheco1/public/index.php';
window.axios.defaults.filePath = '/crm-boheco1/public/scfiles/';
window.axios.defaults.imgURL = '/crm-boheco1/public/imgs/';