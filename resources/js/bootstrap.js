import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true; // Ensure cookies are sent in cross-site requests
window.axios.defaults.baseURL = import.meta.env.VITE_APP_URL; // Set the base URL for axios requests
