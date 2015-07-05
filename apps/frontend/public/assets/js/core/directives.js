(function () {
    'use strict';
    angular
        .module('rock.core.directives', [])
        .directive('bindCompiledHtml', bindCompiledHtml)
        .directive('rockMetaCsrf', rockMetaCsrf)
        .directive('rockUrl', rockUrl);

    rockMetaCsrf.$inject = ['csrfUtils'];
    /**
         * @ngdoc directive
         * @name metaCsrf
         * @restrict A
         */
    function rockMetaCsrf(csrfUtils){
        return {
            restrict : 'A',
            link: function($scope, $element) {
                $scope.$root.$watch(function(){
                    return csrfUtils.getToken();
                }, function(value) {
                    if (!value) {
                        return;
                    }
                    $element.attr('content', value);
                });
            }
        }
    }

    /**
         * @ngdoc directive
         * @name bindCompiledHtml
         * @restrict A
         */
    bindCompiledHtml.$inject = ['$compile'];
    function bindCompiledHtml($compile) {
        return {
            restrict : 'A',
            scope: {
                rawHtml: '=bindCompiledHtml'
            },
            link: function($scope, $element) {
                $scope.$watch('rawHtml', function(value) {
                    if (!value) return;
                    // we want to use the scope OUTSIDE of this directive
                    // (which itself is an isolate scope).

                    var newElem = $compile(value)($scope.$parent);
                    $element.contents().remove();
                    $element.append(newElem);
                });
            }
        }
    }

    /**
         * @ngdoc directive
         * @name rockUrl
         * @restrict A
         */
    function rockUrl(){
        return {
            restrict : 'A',
            scope: {
                options: '=rockUrl'
            },
            link: function($scope, $elem, $attr) {
                if (!$scope.options || !angular.isObject($scope.options)) {
                    return;
                }
                var attribute = 'href',
                    options = $scope.options;
                if (options.attr) {
                    attribute = options.attr;
                }
                if (!$attr[attribute]) {
                    return;
                }
                var url = URI($attr[attribute]);

                if (options.self){
                    url.pathname(URI().pathname())
                }
                $elem.attr(attribute, url);
                if (!options.csrf) {
                    return;
                }

                $scope.$root.$watch(function(scope){return scope.rock.csrf.getToken()}, function(value) {
                    if (!value) {
                        return;
                    }
                    $elem.attr(attribute, url.setSearch($scope.$root.rock.csrf.get()));
                });
            }
        };
    }
})();