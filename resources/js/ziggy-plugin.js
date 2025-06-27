// Ziggy Vue Plugin
import { Ziggy } from './ziggy';
import { route as ziggyRoute } from 'ziggy-js';

// Create a wrapper for the route function that uses our Ziggy config
const route = (name, params, absolute, config = Ziggy) => {
  return ziggyRoute(name, params, absolute, config);
};

// Create a Vue plugin
export default {
  install: (app, options) => {
    // Make route function available globally in the Vue app
    app.config.globalProperties.$route = route;
    
    // Make route function available in the window object for non-Vue code
    window.route = route;
  }
};

// Export route function for direct imports
export { route, Ziggy };
