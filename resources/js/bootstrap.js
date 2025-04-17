import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;
axios.defaults.withXSRFToken = true;
window.axios.defaults.baseURL = import.meta.env.VITE_APP_URL; // Set the base URL for axios requests
