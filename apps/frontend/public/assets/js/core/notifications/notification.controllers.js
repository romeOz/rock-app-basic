(function () {
    'use strict';

    angular
        .module('rock.core.notification.controllers', ['ui.bootstrap', 'ngAnimate'])
        .controller('NotificationController', NotificationController);

    NotificationController.$inject = ['$scope', 'notification'];
    function NotificationController($scope, notification)
    {
        $scope.notifications = notification.getAll();
        $scope.merge = function(messages){
            notification.merge(messages)
        };

        $scope.closeable = true;
        $scope.closeAlert = function(index) {
            notification.remove(index);
        };
    }
})();
