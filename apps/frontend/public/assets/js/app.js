(function () {
    'use strict';
    angular
        .module('app', ['rock'])
        .config(config);

    config.$inject = ['$locationProvider', '$httpProvider', 'notificationProvider'];

    /**
         *  Configure application.
         * @param $locationProvider
         * @param $httpProvider
         * @param {notificationProvider} notificationProvider
         */
    function config($locationProvider, $httpProvider, notificationProvider)
    {
        // configure location
        $locationProvider.html5Mode({
            enabled: true,
            rewriteLinks: false
        });

        // configure notification
        notificationProvider.debugEnabled(true);

        // configure http
        $httpProvider.defaults.headers.common['Content-Type'] = 'application/json';
    }

})();