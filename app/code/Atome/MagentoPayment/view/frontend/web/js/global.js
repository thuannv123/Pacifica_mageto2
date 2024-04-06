define(["jquery"], function ($) {
    var mod = {};
    mod.init = function (pluginUrl) {
        $(document).ready(function () {
            const head = document.head || document.getElementsByTagName('head')[0];
            const script = document.createElement('script');
            script.type = 'text/javascript';
            script.src = pluginUrl;
            head.appendChild(script);
        });
    }
    return mod;
});
