import axios from 'axios';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Set the base URL for Axios requests.
// The VITE_APP_URL is set in the .env file and passed by Vite.
window.axios.defaults.baseURL = import.meta.env.VITE_APP_URL || 'http://localhost';
