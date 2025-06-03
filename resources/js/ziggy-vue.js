// Ziggy Vue integration
// This file properly exports Ziggy routes for Vue components
import { Ziggy } from './ziggy';
import { route as ziggyRoute } from 'ziggy-js';

// Create a wrapper for the route function that uses our Ziggy config
const route = (name, params, absolute, config = Ziggy) => {
  return ziggyRoute(name, params, absolute, config);
};

export { Ziggy, route };
