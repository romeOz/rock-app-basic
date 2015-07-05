(function () {
    'use strict';

    angular
        .module('rock.core.services', [])
        .factory('userUtils', userUtils)
        .provider('formUtils', formUtils)
        .provider('httpUtils', httpUtils)
        .factory('csrfUtils', csrfUtils)
        .factory('modalUtils', modalUtils)
        .provider('htmlUtils', htmlUtils);


    userUtils.$inject = ['$rootScope','$http', 'csrfUtils', 'httpUtils', 'notification'];
    /**
         * @ngdoc service
         * @name userUtils
         */
    function userUtils($rootScope, $http, csrfUtils, httpUtils, notification) {
        var userUtils = {};
        $rootScope._user = undefined;

        /**
                 * Set list data fo user.
                 * @param {Object} data
                 */
        userUtils.set = function(data){
            $rootScope._user = httpUtils.removeExtend(data);
        };

        /**
                 * Adds data by key.
                 * @param {string} key
                 * @param {*} value
                 */
        userUtils.add = function(key, value){
            if (!$rootScope._user) {
                $rootScope._user = {};
            }
            $rootScope._user[key] = httpUtils.removeExtend(value);
        };

        /**
                 * Returns data by key.
                 * @param {string} key
                 * @return {*}
                 */
        userUtils.get = function(key){
            if (!$rootScope._user) {
                return null;
            }
            return $rootScope._user[key] !== undefined ? $rootScope._user[key] : null;
        };

        /**
                 * Returns list data.
                 * @return {undefined|*}
                 */
        userUtils.getAll = function(){
            return $rootScope._user;
        };

        /**
                 * Is logged.
                 * @return {boolean|undefined}
                 */
        userUtils.isLogged = function(){
            if ($rootScope._user === undefined) {
                return undefined;
            }
            return !!$rootScope._user;
        };

        /**
                 * Logout user.
                 * @param {string} url
                 */
        userUtils.logout = function(url){
            $http.get(URI(url).setSearch(csrfUtils.get()))
                .success(function(){
                    $rootScope._user = null;
                    notification.success('lang.successLogout');
                    $rootScope.$broadcast('onLogout');
                });
        };

        return userUtils;
    }


    /**
         * @ngdoc provider
         * @name formUtilsProvider
         * @returns {*}
        */
    function formUtils(){
        var defaultMsg = 'Success.';

        /**
                 * @ngdoc method
                 * @name formUtilsProvider#defaultMsg
                 * @description
                 * @param {string} msg
                 */
        this.defaultMsg = function(msg){
            defaultMsg = msg;
        };


        this.$get = ['$http', function($http){
            var formUtils = {};
            /**
                         * Reload captcha.
                         * @ngdoc method
                         * @name reloadCaptcha
                         * @param {string} url
                         * @return {Object}
                         */
            formUtils.reloadCaptcha = function(url)
            {
               return $http.get(url);
            };

            return formUtils;
        }];
    }

    /**
         * @ngdoc provider
         * @name httpUtilsProvider
         * @returns {*}
         */
    function httpUtils()
    {
        var extendAttribute = '_extend',
            defaultMsg = 'lang.failHTTPRequest';

        /**
                 * @ngdoc method
                 * @name httpUtilsProvider#extendAttribute
                 * @description
                 * @param {string} attribute
                 */
        this.extendAttribute = function(attribute){
            extendAttribute = attribute;
        };


        /**
                 * @ngdoc method
                 * @name httpUtilsProvider#defaultMsg
                 * @description
                 * @param {string} msg
                 */
        this.defaultMsg = function(msg){
            defaultMsg = msg;
        };

        this.$get = ['collectionHelper','stringHelper', 'csrfUtils', 'notification', function(collectionHelper, stringHelper, csrfUtils, notification){
            var httpUtils = {};
            /**
                         * Calculate CSRF-data.
                         * @ngdoc method
                         * @name csrf
                         * @param {Object} data
                         * @param {Function=} headers
                         */
            httpUtils.csrf = function(data, headers)
            {
                if (angular.isObject(data)) {
                    if (data[extendAttribute] && data[extendAttribute].csrf) {
                        csrfUtils.addToken(data[extendAttribute].csrf.token);
                        csrfUtils.addParam(data[extendAttribute].csrf.param);
                        return;
                    }
                }

                if (angular.isFunction(headers)) {
                    csrfUtils.addToken(headers('x-csrf-token'));
                }

            };

            /**
                         * Prepare messages.
                         * @ngdoc method
                         * @name prepareMessages
                         * @param {Array|object} messages
                         * @param {boolean=true} uniq
                         * @param {string=} defaultMessage
                         * @return {Array}
                         */
            httpUtils.normalizeAlerts = function(messages, uniq, defaultMessage){
                if (!messages) {
                    messages = [defaultMessage || defaultMsg];
                }
                if (uniq === undefined) {
                    uniq = true
                }
                messages = flatten(httpUtils.removeExtend(messages));
                if (uniq === true && angular.isArray(messages)) {
                    messages = _.uniq(messages);
                }
                return messages;
            };

            /**
                         * Returns extend attribute.
                         * @param {Object} data
                         * @param {string=} attribute
                         * @return {*}
                         */
            httpUtils.getExtend = function(data, attribute){
                if (!angular.isObject(data) || !data[extendAttribute]) {
                    return null;
                }
                if (attribute) {
                    return data[extendAttribute][attribute] || null;
                }
                return data[extendAttribute];
            };

            /**
                         * Removes extend attribute.
                         * @return {*}
                         */
            httpUtils.removeExtend = function(data){
                delete(data[extendAttribute]);
                return data;
            };

            /**
                         * @ngdoc method
                         * @name error
                         * @param {*} data
                         * @param {number} status
                         * @param {string=} statusText
                         */
            httpUtils.error = function(data, status, statusText) {
                if (data && data.error && data.error.message) {
                    notification.debug(data.error.message);
                }
                switch (status) {
                    case 400:
                    case 422:
                        break;
                    case 403:
                        notification.error('lang.failAccess', {}, prepareMessage(statusText));
                        break;
                    case 404:
                        notification.error('lang.notPage', {}, prepareMessage(statusText));
                        break;
                    case 500:
                        notification.error('lang.failServer', {}, prepareMessage(statusText));
                }
            };

            /**
                         *
                         * @param {Array} value
                         * @return {Array}
                         */
            function flatten(value){
                return collectionHelper.flatten(value, function(value){
                    return prepareMessage(value);
                });
            }

            /**
                         *
                         * @param {string} message
                         * @return {string}
                         */
            function prepareMessage(message)
            {
                message = stringHelper.upperFirst(message);
                if (message.slice(-1) !== '.') {
                    return message + '.';
                }
                return message
            }
            return httpUtils;
        }];
    }

    /**
         * @ngdoc service
         * @name csrfUtils
         */
    function csrfUtils(){
        var csrfUtils = {},
            csrf = {token : undefined, param: undefined};

        /**
                 * Adds CSRF-token.
                 * @param {string} token
                 */
        csrfUtils.addToken = function(token){
            if (angular.isString(token)) {
                csrf.token = token;
            }
        };
        /**
                 * Adds CSRF-param.
                 * @param {string} param
                 */
        csrfUtils.addParam = function(param){
            if (param) {
                csrf.param = param;
            }
        };
        /**
                 *  Returns `<param>:<token>`.
                 * @return {Object|null}
                 */
        csrfUtils.get = function(){
            if (csrf.token && csrf.param) {
                var result = {};
                result[csrf.param] = csrf.token;
                return result;
            }
            return null;
        };
        /**
                 * Return CSRF-token.
                 * @return {string}
                 */
        csrfUtils.getToken = function(){
            return csrf.token;
        };
        /**
                 *  Return CSRF-param
                 * @return {string}
                 */
        csrfUtils.getParam = function(){
            return csrf.param;
        };
        /**
                 * Exists CSRF-token.
                 * @return {boolean}
                 */
        csrfUtils.has = function(){
            return csrf && csrf.token;
        };
        return csrfUtils;
    }


    /**
         * @ngdoc service
         * @name modalUtils
         */
    modalUtils.$inject = ['$templateCache', '$modal'];
    function modalUtils($templateCache, $modal){
        var modalUtils = {};
        modalUtils.show = function($scope, url, ctrl){
            $modal.open({
                templateUrl: url,
                controller: ctrl
            });
        };
        return modalUtils;
    }

    /**
         * @ngdoc service
         * @name htmlUtils
         */
    function htmlUtils(){
        var tpl = '<iframe width="{{width}}" height="{{height}}" frameborder="0" allowfullscreen="allowfullscreen" src="{{src}}"></iframe>',
            width = 480,
            height = 360;

        /**
                 *
                 * @type {{width: Function(width:number), height: Function(height:number)}}
                 */
        this.video = {
            width : function(_width){
                width = _width;
            },
            height : function(_height){
                height = _height;
            }
        };

        this.$get = ['$modal', '$interpolate', function($modal, $interpolate){

            var htmlUtils = {};


            /**
                         *
                         * @param {string} src
                         * @param {number} width
                         * @param {number} height
                         * @param {string} title
                         * @param {Event} $event
                         */
            htmlUtils.playVideo = function(src, width, height, title, $event){
                if (!src) {
                    return;
                }
                $event.preventDefault();
                angular.element($event.target).replaceWith(interpolate(tpl, src, width, height))
            };
            htmlUtils.playVideoModal = function(src, width, height, title, $event){
                if (!src) {
                    return;
                }
                $event.preventDefault();

                Controller.$inject = ['$scope', '$modalInstance'];
                function Controller($scope, $modalInstance){

                    $scope.cancel = function () {
                        $modalInstance.dismiss('cancel');
                    };

                }
                if (title) {
                    title = '<div class="modal-header">' +
                    '<button data-ng-click="cancel()" class="close" type="button">×</button>' +
                    '<h4 class="modal-title"><span class="glyphicon glyphicon-star"></span> '+title+'</h4>' +
                    '</div>'
                }

                $modal.open({
                    template: title +
                    '<div class="modal-body">' + interpolate(tpl, src, width, height) + '</div>',
                    controller: Controller
                });
            };
            function interpolate(tpl, src, width, height){
                return $interpolate(tpl)({
                    width : width,
                    height : height,
                    src: src
                });
            }

            return htmlUtils;
        }];
    }
})();