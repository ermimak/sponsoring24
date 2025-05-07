const Ziggy = {
    url: '/',
    port: null,
    defaults: {},
    routes: {
        'language.switch': {
            uri: 'language/{locale}',
            methods: ['GET', 'HEAD'],
            parameters: ['locale']
        }
    }
};

export { Ziggy };
export function route(name, params, absolute) {
    return window.route(name, params, absolute, Ziggy);
}
