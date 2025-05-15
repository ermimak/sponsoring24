const Ziggy = {"url":"http:\/\/localhost","port":null,"defaults":{},"routes":{"sanctum.csrf-cookie":{"uri":"sanctum\/csrf-cookie","methods":["GET","HEAD"]},"home":{"uri":"\/","methods":["GET","HEAD"]},"projects.index":{"uri":"projects","methods":["GET","HEAD"]},"login":{"uri":"login","methods":["GET","HEAD"]},"register":{"uri":"register","methods":["GET","HEAD"]},"language.switch":{"uri":"language\/{locale}","methods":["GET","HEAD"],"wheres":{"locale":"de|fr"},"parameters":["locale"]},"welcome.locale":{"uri":"{locale}","methods":["GET","HEAD"],"wheres":{"locale":"de|fr"},"parameters":["locale"]},"logout":{"uri":"logout","methods":["POST"]},"dashboard":{"uri":"dashboard","methods":["GET","HEAD"]},"dashboard.projects.index":{"uri":"dashboard\/projects","methods":["GET","HEAD"]},"dashboard.projects.create":{"uri":"dashboard\/projects\/create","methods":["GET","HEAD"]},"dashboard.projects.edit":{"uri":"dashboard\/projects\/{project}\/edit","methods":["GET","HEAD"],"wheres":{"project":"[0-9a-fA-F-]{36}"},"parameters":["project"]},"dashboard.projects.uploadImage":{"uri":"dashboard\/projects\/{project}\/upload-image","methods":["POST"],"wheres":{"project":"[0-9a-fA-F-]{36}"},"parameters":["project"],"bindings":{"project":"id"}},"dashboard.projects.duplicate":{"uri":"dashboard\/projects\/{project}\/duplicate","methods":["POST"],"wheres":{"project":"[0-9a-fA-F-]{36}"},"parameters":["project"],"bindings":{"project":"id"}},"projects.store":{"uri":"dashboard\/projects","methods":["POST"],"wheres":{"project":"[0-9a-fA-F-]{36}"}},"dashboard.projects.update":{"uri":"dashboard\/projects\/{project}","methods":["PUT","PATCH"],"wheres":{"project":"[0-9a-fA-F-]{36}"},"parameters":["project"],"bindings":{"project":"id"}},"projects.destroy":{"uri":"dashboard\/projects\/{project}","methods":["DELETE"],"wheres":{"project":"[0-9a-fA-F-]{36}"},"parameters":["project"],"bindings":{"project":"id"}},"dashboard.members.index":{"uri":"dashboard\/members","methods":["GET","HEAD"]},"dashboard.members.store":{"uri":"dashboard\/members","methods":["POST"]},"dashboard.members.create":{"uri":"dashboard\/members\/create","methods":["GET","HEAD"]},"dashboard.members.show":{"uri":"dashboard\/members\/{participant}","methods":["GET","HEAD"],"wheres":{"participant":"[0-9]+"},"parameters":["participant"],"bindings":{"participant":"id"}},"dashboard.members.update":{"uri":"dashboard\/members\/{participant}","methods":["PUT"],"wheres":{"participant":"[0-9]+"},"parameters":["participant"],"bindings":{"participant":"id"}},"dashboard.members.destroy":{"uri":"dashboard\/members\/{participant}","methods":["DELETE"],"wheres":{"participant":"[0-9]+"},"parameters":["participant"],"bindings":{"participant":"id"}},"dashboard.members.edit":{"uri":"dashboard\/members\/{participant}\/edit","methods":["GET","HEAD"],"wheres":{"participant":"[0-9]+"},"parameters":["participant"],"bindings":{"participant":"id"}},"dashboard.members.import":{"uri":"dashboard\/members\/import","methods":["POST"]},"dashboard.members.export":{"uri":"dashboard\/members\/export","methods":["GET","HEAD"]},"dashboard.members.groups":{"uri":"dashboard\/members\/groups","methods":["GET","HEAD"]},"dashboard.members.groups.data":{"uri":"dashboard\/members\/groups\/data","methods":["GET","HEAD"]},"dashboard.members.groups.store":{"uri":"dashboard\/members\/groups","methods":["POST"]},"dashboard.members.groups.destroy":{"uri":"dashboard\/members\/groups\/{memberGroup}","methods":["DELETE"],"wheres":{"memberGroup":"[0-9]+"},"parameters":["memberGroup"],"bindings":{"memberGroup":"id"}},"dashboard.users":{"uri":"dashboard\/users","methods":["GET","HEAD"]},"dashboard.settings":{"uri":"dashboard\/settings","methods":["GET","HEAD"]},"dashboard.bonus":{"uri":"dashboard\/bonus","methods":["GET","HEAD"]},"donations.index":{"uri":"dashboard\/donations","methods":["GET","HEAD"],"wheres":{"donation":"[0-9]+"}},"dashboard.reports":{"uri":"dashboard\/reports","methods":["GET","HEAD"]},"dashboard.admin.roles":{"uri":"dashboard\/admin\/roles","methods":["GET","HEAD"]},"dashboard.admin.permissions":{"uri":"dashboard\/admin\/permissions","methods":["GET","HEAD"]},"donations.store":{"uri":"dashboard\/donations","methods":["POST"],"wheres":{"donation":"[0-9]+"}},"donations.show":{"uri":"dashboard\/donations\/{donation}","methods":["GET","HEAD"],"wheres":{"donation":"[0-9]+"},"parameters":["donation"],"bindings":{"donation":"id"}},"donations.update":{"uri":"dashboard\/donations\/{donation}","methods":["PUT","PATCH"],"wheres":{"donation":"[0-9]+"},"parameters":["donation"],"bindings":{"donation":"id"}},"donations.destroy":{"uri":"dashboard\/donations\/{donation}","methods":["DELETE"],"wheres":{"donation":"[0-9]+"},"parameters":["donation"],"bindings":{"donation":"id"}},"email-templates.index":{"uri":"dashboard\/email-templates","methods":["GET","HEAD"],"wheres":{"email_template":"[0-9]+"}},"email-templates.store":{"uri":"dashboard\/email-templates","methods":["POST"],"wheres":{"email_template":"[0-9]+"}},"email-templates.show":{"uri":"dashboard\/email-templates\/{email_template}","methods":["GET","HEAD"],"wheres":{"email_template":"[0-9]+"},"parameters":["email_template"]},"email-templates.update":{"uri":"dashboard\/email-templates\/{email_template}","methods":["PUT","PATCH"],"wheres":{"email_template":"[0-9]+"},"parameters":["email_template"]},"email-templates.destroy":{"uri":"dashboard\/email-templates\/{email_template}","methods":["DELETE"],"wheres":{"email_template":"[0-9]+"},"parameters":["email_template"]},"upload":{"uri":"upload","methods":["POST"]},"api.projects":{"uri":"api\/projects","methods":["GET","HEAD"]},"debug.login":{"uri":"debug-login","methods":["GET","HEAD"]},"storage.local":{"uri":"storage\/{path}","methods":["GET","HEAD"],"wheres":{"path":".*"},"parameters":["path"]}}};
if (typeof window !== 'undefined' && typeof window.Ziggy !== 'undefined') {
  Object.assign(Ziggy.routes, window.Ziggy.routes);
}

// Define the route function manually
function route(name, params, absolute = true) {
    const routeConfig = Ziggy.routes[name];
    if (!routeConfig) {
        throw new Error(`Route ${name} not found in Ziggy configuration.`);
    }

    let url = routeConfig.uri;
    if (params) {
        Object.keys(params).forEach(key => {
            const value = params[key];
            url = url.replace(`{${key}}`, encodeURIComponent(value));
        });
    }

    // Handle query parameters if params contain extra keys not in the route
    const queryParams = new URLSearchParams();
    if (params) {
        Object.keys(params).forEach(key => {
            if (!routeConfig.parameters?.includes(key)) {
                queryParams.append(key, params[key]);
            }
        });
    }

    const queryString = queryParams.toString();
    if (queryString) {
        url += `?${queryString}`;
    }

    return absolute ? `${Ziggy.url}/${url}` : `/${url}`;
}

export { Ziggy, route };