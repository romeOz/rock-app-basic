(function () {
    'use strict';
    angular
        .module('rock.core.filters', [])
        .filter('unsafe', unsafe)
        .filter('byKeys', byKeys);

    /**
         * @ngdoc filter
         * @name unsafe
         */
    unsafe.$inject = ['$sce'];
    function unsafe($sce){
        return function(value){
            if (typeof value === 'undefined' || value === null) {
                return '';
            }
            return $sce.trustAsHtml(value);
        }
    }

    /**
         * @ngdoc filter
         * @name byKeys
         */
    function byKeys(){
        return function(inputs, attrubutes) {
            if (inputs && angular.isObject(inputs)) {
                inputs = _.filter(inputs, function(value, attribute){
                    return _.contains(attrubutes,  attribute);
                });
                if (_.isEmpty(inputs)) {
                    return null;
                }
                return inputs;
            }
        };
    }
})();