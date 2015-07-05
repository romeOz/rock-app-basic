(function () {
    'use strict';

    angular
        .module('rock.core.notification.services', [])
        .provider('notification', notification);

    /**
         * @ngdoc provider
         * @name notificationProvider
         * @returns {*}
         */
    function notification(){
        var messages = [],
            debug = true;

        /**
                 * @ngdoc method
                 * @name notificationProvider#debugEnabled
                 * @description
                 * @param {boolean} debugEnabled enable or disable debug level messages
                 */
        this.debugEnabled = function(debugEnabled){
            debug = debugEnabled;
        };

        this.$get = ['$translate', function($translate) {

            return {
                /**
                                 * @ngdoc method
                                 * @name notification#log
                                 *
                                 * @description
                                 * Write a log message
                                 * @param {string} msg
                                 * @param {Object} placeholders
                                 * @param {string} _default
                                 */
                log: function(msg, placeholders, _default){
                    translate('log', msg, placeholders, _default);
                },

                /**
                                 * @ngdoc method
                                 * @name notification#info
                                 *
                                 * @description
                                 * Write an information message
                                 * @param {string} msg
                                 * @param {Object=} placeholders
                                 * @param {string=} _default
                                 */
                info: function(msg, placeholders, _default){
                    translate('info', msg, placeholders, _default);
                },

                /**
                                 * @ngdoc method
                                 * @name notification#success
                                 *
                                 * @description
                                 * Write an information message
                                 * @param {string} msg
                                 * @param {Object=} placeholders
                                 * @param {string=} _default
                                 */
                success: function(msg, placeholders, _default){
                    translate('success', msg, placeholders, _default);
                },

                /**
                                 * @ngdoc method
                                 * @name notification#warn
                                 *
                                 * @description
                                 * Write a warning message
                                 * @param {string} msg
                                 * @param {Object=} placeholders
                                 * @param {string=} _default
                                 */
                warn: function(msg, placeholders, _default){
                    translate('warn', msg, placeholders, _default);
                },

                /**
                                 * @ngdoc method
                                 * @name notification#error
                                 *
                                 * @description
                                 * Write an error message
                                 * @param {string} msg
                                 * @param {Object=} placeholders
                                 * @param {string=} _default
                                 */
                error: function(msg, placeholders, _default){
                    translate('error', msg, placeholders, _default);
                },

                /**
                                 * @ngdoc method
                                 * @name notification#debug
                                 *
                                 * @description
                                 * Write a debug message
                                 */
                debug: function(msg){
                    if (angular.isString(msg)) {
                        msg = new Error(msg);
                    }
                    console.debug(msg);
                },

                /**
                                 * @ngdoc method
                                 * @name notification#merge
                                 *
                                 * @description adds list messages
                                 * @param {Object[]|string[]} data
                                 */
                merge: function(data){
                    if (!data) {
                        return;
                    }
                    if (angular.isString(data[0])) {
                        data = data.map(function(value){
                            return {msg: value};
                        });
                    }
                    angular.extend(messages, data);
                },

                /**
                                 * @ngdoc method
                                 * @name notification#getAll
                                 *
                                 * @description returns list messages
                                 * @return {Object[]}
                                 */
                getAll: function(){
                    return messages;
                },

                /**
                                 * @ngdoc method
                                 * @name notification#exists
                                 *
                                 * @description exists messages
                                 * @return {boolean}
                                 */
                exists: function(){
                    return !!messages;
                },

                /**
                                 * @ngdoc method
                                 * @name notification#remove
                                 *
                                 * @description remove message
                                 * @param {number} index
                                 */
                remove: function(index){
                    if (!!messages) {
                        messages.splice(index, 1);
                    }
                },

                /**
                                 * @ngdoc method
                                 * @name notification#removeAll
                                 *
                                 * @description remove all messages
                                 */
                removeAll: function(){
                    messages = [];
                }
            };

            function translate (type, msg, placeholders, _default){
                var push = function(msg){
                    switch (type) {
                        case 'warn':
                            type = 'warning';
                            break;
                        case 'error':
                            type = 'danger';
                            break;
                        case 'success':
                            type = 'success';
                            break;
                        default:
                            type = 'info';
                    }
                    messages.push({msg : msg, type : type});
                };

                $translate(msg, placeholders).then(push)['catch'](function(msg){push(_default || msg)});
            }
        }];
    }
})();