import axios from 'axios';

// Detect file uploads automatically
const apiClient = axios.create({
    baseURL: '/api',
});

// Request interceptor
apiClient.interceptors.request.use(config => {
    config.headers['Accept'] = 'application/json';
    config.headers['Content-Type'] = 'application/json';
    config.withCredentials = true;
    return config;
});

export default apiClient;
