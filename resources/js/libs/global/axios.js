import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';


window.axios.interceptors.response.use(
    response => response,
    error => {

        if (!error.response) {
            console.error('Network error', error);
            return Promise.reject(error);
        }

        const status = error.response.status;

        if (status === 403) {
            window.location.replace('/company-blocked');
        }

        if (status === 401) {
            window.location.replace('/login');
        }

        // Otros errores: 500, 404, etc.
        return Promise.reject(error);
    }
);
