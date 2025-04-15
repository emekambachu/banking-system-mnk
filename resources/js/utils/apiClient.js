import axios from 'axios';

// Detect file uploads automatically
const apiClient = axios.create({
    baseURL: '/api',
});

// Request interceptor
apiClient.interceptors.request.use(config => {
    config.headers['Accept'] = 'application/json';
    return config;
});

export default apiClient;
