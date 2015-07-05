(function () {
    'use strict';

    angular
        .module('rock.core.forms.controllers', ['pascalprecht.translate'])
        .controller('RockFormController', RockFormController)
        .filter('normalizeAlerts', normalizeAlerts);

    /**
         * @ngdoc filter
         * @name normalizeAlerts
         */
    normalizeAlerts.$inject = ['httpUtils'];
    function normalizeAlerts(httpUtils){
        return function(inputs, unique) {
            if (inputs) {
                if (unique === undefined) {
                    unique = true;
                }
                return httpUtils.normalizeAlerts(inputs, unique);
            }
        };
    }

    RockFormController.$inject = ['$scope', '$http', '$translate', 'csrfUtils', 'formUtils', 'userUtils', 'notification'];

    /**
         *
         * @param $scope
         * @param $http
         * @param {$translate} $translate
         * @param {formUtils} formUtils
         * @param {userUtils} userUtils
         * @param {notification} notification
         * @param {csrfUtils} csrfUtils
         * @constructor
         * @ngInject
         * @export
         */
    function RockFormController($scope, $http, $translate, csrfUtils, formUtils, userUtils, notification)
    {
        $scope.response = {};
        $scope.sending = false;
        $scope.class = 'alert-danger';
        $scope.formName = null;
        $scope.validateOnChanged = false;
        /**
                 * Is send http-request.
                 * @return {boolean}
                 */
        $scope.isSend = function(){
            return $scope.sending;
        };

        /**
                 * Adds alert message by attribute.
                 * @param {string} attributeName
                 * @param {string} msg
                 */
        $scope.addAlert = function(attributeName, msg){
            if (!$scope.response.messages) {
                $scope.response.messages = {};
            }
            $scope.response.messages[attributeName] = msg;
        };

        /**
                 * Returns alert by attribute.
                 * @return {string|undefined}
                 */
        $scope.getAlert = function(attributeName){
            if (!$scope.isAlerts()) {
                return undefined;
            }
            return $scope.response.messages[attributeName];
        };

        /**
                 * Returns list alerts.
                 * @return {Object}
                 */
        $scope.getAlerts = function(){
            return $scope.response.messages;
        };

        /**
                 * Is alerts.
                 * @return {boolean}
                 */
        $scope.isAlerts = function(){
            return !!$scope.response.messages;
        };

        /**
                 * Exists alert by attribute.
                 * @return {boolean}
                 */
        $scope.existsAlert = function(attributeName){
            if (!$scope.isAlerts()) {
                return false;
            }
            return !!$scope.response.messages[attributeName];
        };

        /**
                 * Reset `$scope.response`.
                 */
        $scope.clear = function(){
            $scope.response = {};
        };

        /**
                 * Pristine value.
                 * @param {string} attributeName
                 * @returns {boolean}
                 */
        $scope.pristine = function (attributeName) {
            var formName = $scope.formName;
            if (!$scope[formName] || !$scope[formName][attributeName]) {
                return false;
            }
            return $scope[formName][attributeName].$pristine;
        };

        /**
                 * Invalid value.
                 * @param {string} attributeName
                 * @returns {boolean}
                 */
        $scope.invalid = function (attributeName) {
            var formName = $scope.formName;
            if (!$scope[formName] || !$scope[formName][attributeName]) {
                return true;
            }
            return $scope[formName][attributeName].$invalid;
        };

        /**
                 * Bind error.
                 * @param {string} attributeName
                 * @return {string|undefined}
                 */
        $scope.bindError = function (attributeName) {
            return $scope.getAlert(attributeName);
        };

        /**
                 * Show error.
                 * @param {string} attributeName
                 * @param {string} ruleName
                 * @returns {boolean}
                 */
        $scope.showError = function (attributeName, ruleName) {
            var formName = $scope.formName;

            if (!$scope[formName] || !$scope[formName][attributeName]) {
                return false;
            }
            if (!!$scope.validateOnChanged) {
                return ($scope[formName][attributeName].$dirty || $scope[formName].$submitted) &&
                ($scope[formName][attributeName].$focused || $scope[formName].$submitted) &&
                $scope[formName][attributeName].$error[ruleName];
            }
            return ($scope[formName][attributeName].$dirty || $scope[formName].$submitted) &&
            $scope[formName][attributeName].$error[ruleName];
        };

        /**
                 * Hide error.
                 * @param {string} attributeName
                 * @returns {boolean}
                 */
        $scope.hideError = function (attributeName) {
            var formName = $scope.formName;
            if (!$scope[formName] || !$scope[formName][attributeName]) {
                return false;
            }
            return $scope[formName][attributeName].$valid;
        };

        /**
                 * Highlighting input.
                 * @param {string} attributeName
                 * @return {string}
                 */
        $scope.showHighlightError = function (attributeName) {
            var formName = $scope.formName;
            if (!$scope[formName] || !$scope[formName][attributeName]) {
                return '';
            }
            if (!!$scope.validateOnChanged) {
                return $scope[formName][attributeName].$invalid &&
                ($scope[formName][attributeName].$focused || $scope[formName].$submitted) &&
                (!$scope[formName][attributeName].$pristine || $scope[formName].$submitted) ? 'has-error' : '';
            }
            return $scope[formName][attributeName].$invalid && (!$scope[formName][attributeName].$pristine || $scope[formName].$submitted) ? 'has-error' : '';
        };

        /**
                 * Returns `src` of captcha.
                 * @return {string}
                 */
        $scope.getCaptcha = function(){
            return $scope.response.captcha;
        };

        /**
                 * Reload captcha.
                 * @param {string} url
                 * @param {Event} $event
                 */
        $scope.reloadCaptcha = function(url, $event) {
            if (!url) {
                return;
            }
            $event.preventDefault();
            formUtils.reloadCaptcha(url).success(function (data) {
                if (data) {
                    // changed src
                    $event.target.src = data;
                    return;
                }
                notification.debug('Request data "captcha" is empty.');
            });
        };

        /**
                 * Submit form
                 * @param {string} url
                 * @param {Event} $event
                 */
        $scope.submit = function (url, $event) {
            var formName,
                data = {};

            if (!$scope.formName) {
                notification.debug('Name of form is empty');
                $event.preventDefault();
                return;
            }
            formName = $scope.formName;
            $scope[formName].$setSubmitted();

            if ($scope[formName].$invalid) {
                $event.preventDefault();
                return;
            }

            if (!url) {
                return;
            }
            $event.preventDefault();

            $scope[formName].$submitted = false;
            if (!$scope[formName].values) {
                notification.debug('Values of form is empty');
                return;
            }
            $scope.clear();
            $scope.sending = true;
            data[formName] = $scope[formName].values;
            // add CSRF-token
            data[formName][csrfUtils.getParam()] = csrfUtils.getToken();

            $http.post(url, data).success(httpSuccess).error(httpFail);
        };

        /**
                 * @param {string} url
                 * @param {Event} $e
                 */
        $scope.logout = function(url, $e){
            if (!url) {
                return;
            }
            $e.preventDefault();
            userUtils.logout(url);
        };

        //$scope.normalizeAlerts = httpUtils.normalizeAlerts;

        function httpSuccess (data){
            $scope.sending = false;
            //$scope.$root.$broadcast('onHttpFormSuccess');
            if (!data) {
                return;
            }
            $scope.class = 'alert-success';
            $translate('lang.success')
                .then(function(msg){
                    if (!$scope.response.messages) {
                        $scope.response.messages = [];
                    }
                    $scope.response.messages.push(msg);
                });
        }

        function httpFail(data, status){
            $scope.sending = false;
            $scope.class = 'alert-danger';
            //$scope.$root.$broadcast('onHttpFormFail');
            if (status === 422) {
                $scope.response.messages = data;
            }
        }
    }
})();