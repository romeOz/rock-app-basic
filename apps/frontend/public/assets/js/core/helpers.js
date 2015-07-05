(function () {
    'use strict';

    angular
        .module('rock.core.helpers', [])
        .factory('stringHelper', stringHelper)
        .factory('collectionHelper', collectionHelper)
        .factory('alias', alias);

    /**
         * @ngdoc service
         * @name stringHelper
         */
    function stringHelper() {
        var StringHelper = {};

        /**
                 * Upper first char.
                 * @ngdoc method
                 * @name stringHelper#upperFirst
                 * @param {string} value
                 * @returns {string}
                 */
        StringHelper.upperFirst = function(value){
            return value.charAt(0).toUpperCase() + value.slice(1);
        };

        /**
                 * Find the position of the first occurrence of a substring in a string.
                 * @ngdoc method
                 * @name stringHelper#strpos
                 * @param haystack
                 * @param needle
                 * @param offset
                 * @returns {*|Number}
                 * @link http://kevin.vanzonneveld.net
                 */
        StringHelper.strpos = function ( haystack, needle, offset){
            if (offset === undefined) {
                offset = 0;
            }
            var i = haystack.indexOf( needle, offset ); // returns -1
            return i >= 0 ? i : false;
        };

        /**
                 * Reverse string
                 * @ngdoc method
                 * @name stringHelper#reverse
                 * @param string
                 * @returns {string}
                 */
        StringHelper.reverse = function(string){
            return string.split("").reverse().join("");
        };

        /**
                 * Binary safe string comparison.
                 *
                 * ```js
                 * strncmp('aaa', 'aab', 2); // 0
                 * strncmp('aaa', 'aab', 3 ); // -1
                 * ```
                 * @ngdoc method
                 * @name stringHelper#strncmp
                 * @param {string} str1
                 * @param {string} str2
                 * @param {number} lgth
                 * @return {number}
                 */
        StringHelper.strncmp = function(str1, str2, lgth) {
            var s1 = (str1 + '')
                .substr(0, lgth),
                s2 = (str2 + '')
                .substr(0, lgth);

            return ((s1 == s2) ? 0 : ((s1 > s2) ? 1 : -1));
        };

        /**
                 * Find the position of the first occurrence of a substring in a string.
                 * @ngdoc method
                 * @name stringHelper#strpos
                 * @param {string} haystack
                 * @param {string} needle
                 * @param {number} offset
                 * @return {number|boolean}
                 */
        StringHelper.strpos = function(haystack, needle, offset){
            var i = haystack.indexOf( needle, offset ); // returns -1
            return i >= 0 ? i : false;
        };

        /**
                 * Strip whitespace (or other characters) from the beginning of a string.
                 * @ngdoc method
                 * @name stringHelper#ltrim
                 * @param {string} str
                 * @param {string=} charlist
                 * @return {string}
                 */
        StringHelper.ltrim = function(str, charlist) {

            charlist = !charlist ? ' \\s\u00A0' : (charlist + '')
                .replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '$1');
            var re = new RegExp('^[' + charlist + ']+', 'g');
            return (str + '')
                .replace(re, '');
        };

        /**
                 * Strip whitespace (or other characters) from the end of a string.
                 * @ngdoc method
                 * @name stringHelper#rtrim
                 * @param {string} str
                 * @param {string=} charlist
                 * @return {string}
                 */
        StringHelper.rtrim = function(str, charlist) {

            charlist = !charlist ? ' \\s\u00A0' : (charlist + '')
                .replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '\\$1');
            var re = new RegExp('[' + charlist + ']+$', 'g');
            return (str + '')
                .replace(re, '');
        };


        return StringHelper;
    }

    /**
         * @ngdoc service
         * @name collectionHelper
         * @return {*}
         */
    function collectionHelper() {
        var CollectionHelper = {};

        /**
                 * Calculate CSRF-data.
                 * @ngdoc method
                 * @name collectionHelper#flatten
                 * @param {Array} value
                 * @param {Function} callback
                 * @return {Array|Object}
                 */
        CollectionHelper.flatten = function(value, callback){
            var isArray = angular.isArray(value),
                result = isArray ? [] : {};
            var recurs  = function(value, isArray) {
                angular.forEach(value, function(value, key){
                    if (angular.isObject(value)) {
                        recurs(value, isArray);
                        return;
                    }
                    if (angular.isFunction(callback)) {
                        value = callback(value);
                    }
                    if (isArray) {
                        result.push(value);
                    } else {
                        result[key] = value;
                    }
                });
            };
            recurs(value, isArray);
            return result;
        };

        return CollectionHelper;
    }

    alias.$inject = ['stringHelper', 'notification'];

    /**
         * @ngdoc service
         * @name alias
         * @returns {*}
         */
    function alias(stringHelper, notification){
        var _alias = {},
            aliases = {};

        /**
                 * @ngdoc method
                 * @name alias#set
                 * @param {string} alias
                 * @param {string} path
                 */
        _alias.set = function(alias, path){

            if (stringHelper.strncmp(alias, '@', 1)) {
                alias = '@' + alias;
            }
            var delimiter = '/',
                pos = stringHelper.strpos(alias, delimiter),
                root = pos === false ? alias : alias.substr(0, pos);
            if (path !== null) {
                path = stringHelper.strncmp(path, '@', 1) ? stringHelper.rtrim(path, '\\/') : _alias.get(path);
                if (aliases[root] === undefined) {
                    if (pos === false) {
                        aliases[root] = path;
                    } else {
                        aliases[root] = {};
                        aliases[root][alias] = path;
                    }
                } else if (angular.isString(aliases[root])) {
                    if (pos === false) {
                        aliases[root] = path;
                    } else {
                        aliases[root] = {};
                        aliases[root][alias] = path;
                        aliases[root][root] = aliases[root];
                    }
                } else {
                    aliases[root][alias] = path;
                    //krsort(aliases[root]);
                }
            } else if (aliases[root] !== undefined) {
                if (angular.isArray(aliases[root])) {
                    aliases[root][alias] = undefined;
                } else if (pos === false) {
                    aliases[root] = undefined;
                }
            }
        };

        /**
                 * @ngdoc method
                 * @name alias#get
                 * @param {string} alias
                 * @return {*}
                 */
        _alias.get = function(alias){

            if (stringHelper.strncmp(alias, '@', 1)) {
                // not an alias
                return alias;
            }

            var  delimiter = '/',
                pos = stringHelper.strpos(alias, delimiter),
                root = pos === false ? alias : alias.substr(0, pos);

            if (aliases[root] !== undefined) {
                if (angular.isString(aliases[root])) {
                    return pos === false ? aliases[root] : aliases[root] + alias.substr(pos);
                } else {
                    var result = _.find(aliases[root], function(path, name){
                        if (stringHelper.strpos(alias + delimiter, name + delimiter) === 0) {
                            return path + alias.substr(name.length);
                        }
                    });

                }
            }

            if (result === undefined) {
                notification.debug('Invalid path alias: ' + alias);
            }
            return result;
        };

        /**
                 * @ngdoc method
                 * @name alias#remove
                 * @param {string} alias
                 */
        _alias.remove = function(alias){
            aliases[alias] = undefined;
        };

        return _alias;
    }
})();