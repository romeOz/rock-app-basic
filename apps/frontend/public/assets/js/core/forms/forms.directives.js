(function () {
    'use strict';
    angular
        .module(
            'rock.core.forms.directives',
            [
                'ui.bootstrap.progressbar',
                'template/progressbar/progress.html',
                'template/progressbar/progressbar.html'
            ]
        )
        .directive('rockFormFocus', rockFormFocus)
        .directive('rockPasswordStrong', rockPasswordStrong)
        .directive('rockMatch', rockMatch)
        .directive('rockResetField', rockResetField)
        .directive('rockResetFieldIcon', rockResetFieldIcon);

    function rockMatch()
    {
        return {
            require: 'ngModel',
            restrict: 'A',
            scope: {
                match: '=rockMatch'
            },
            link: function($scope, $element, attrs, ctrl) {
                $scope.$watch(function() {
                    var modelValue = ctrl.$modelValue || ctrl.$$invalidModelValue;
                    return (ctrl.$pristine && angular.isUndefined(modelValue)) || $scope.match === modelValue;
                }, function(currentValue) {
                    ctrl.$setValidity('match', currentValue);
                });
            }
        };
    }

    rockFormFocus.$inject = ['$timeout'];
    function rockFormFocus($timeout){
        var FOCUS_CLASS = "ng-focused";
        return {
            restrict: 'A',
            require: 'ngModel',
            link: function(scope, element, attrs, ctrl) {
                ctrl.$focused = false;
                element.bind('focus', function(evt) {
                    element.addClass(FOCUS_CLASS);
                    $timeout(function() {
                        ctrl.$focused = false;
                    }, 0);
                }).bind('blur', function(evt) {
                    element.removeClass(FOCUS_CLASS);
                    $timeout(function() {
                        ctrl.$focused = true;
                    }, 0);
                });
            }
        }
    }

    rockPasswordStrong.$inject = ['stringHelper', '$templateCache'];
    function rockPasswordStrong(StringHelper, $templateCache)
    {
        if (!$templateCache.get('form/strong-password')) {
            $templateCache.put('form/strong-password',  '<progressbar value="value" type="{{class}}">{{value}}%</progressbar>');
        }
        return {
            templateUrl: 'form/strong-password',
            restrict: 'A',
            scope: {
                pwd: '=rockPasswordStrong'
            },
            link: function(scope) {
                var
                    mesureStrength = function(p) {
                        var matches = {
                                pos: {},
                                neg: {}
                            },
                            counts = {
                                pos: {},
                                neg: {
                                    seqLetter: 0,
                                    seqNumber: 0,
                                    seqSymbol: 0
                                }
                            },
                            tmp,
                            strength = 0,
                            letters = 'abcdefghijklmnopqrstuvwxyz',
                            numbers = '01234567890',
                            symbols = '\\!@#$%&/()=?¿',
                            back,
                            forth,
                            i;

                        if (p) {
                            // Benefits
                            matches.pos.lower = p.match(/[a-z]/g);
                            matches.pos.upper = p.match(/[A-Z]/g);
                            matches.pos.numbers = p.match(/\d/g);
                            matches.pos.symbols = p.match(/[$-/:-?{-~!^_`\[\]]/g);
                            matches.pos.middleNumber = p.slice(1, -1).match(/\d/g);
                            matches.pos.middleSymbol = p.slice(1, -1).match(/[$-/:-?{-~!^_`\[\]]/g);

                            counts.pos.lower = matches.pos.lower ? matches.pos.lower.length : 0;
                            counts.pos.upper = matches.pos.upper ? matches.pos.upper.length : 0;
                            counts.pos.numbers = matches.pos.numbers ? matches.pos.numbers.length : 0;
                            counts.pos.symbols = matches.pos.symbols ? matches.pos.symbols.length : 0;

                            tmp = _.reduce(counts.pos, function(memo, val) {
                                // if has count will add 1
                                return memo + Math.min(1, val);
                            }, 0);

                            counts.pos.numChars = p.length;
                            tmp += (counts.pos.numChars >= 8) ? 1 : 0;

                            counts.pos.requirements = (tmp >= 3) ? tmp : 0;
                            counts.pos.middleNumber = matches.pos.middleNumber ? matches.pos.middleNumber.length : 0;
                            counts.pos.middleSymbol = matches.pos.middleSymbol ? matches.pos.middleSymbol.length : 0;

                            // Deductions
                            matches.neg.consecLower = p.match(/(?=([a-z]{2}))/g);
                            matches.neg.consecUpper = p.match(/(?=([A-Z]{2}))/g);
                            matches.neg.consecNumbers = p.match(/(?=(\d{2}))/g);
                            matches.neg.onlyNumbers = p.match(/^[0-9]*$/g);
                            matches.neg.onlyLetters = p.match(/^([a-z]|[A-Z])*$/g);

                            counts.neg.consecLower = matches.neg.consecLower ? matches.neg.consecLower.length : 0;
                            counts.neg.consecUpper = matches.neg.consecUpper ? matches.neg.consecUpper.length : 0;
                            counts.neg.consecNumbers = matches.neg.consecNumbers ? matches.neg.consecNumbers.length : 0;


                            // sequential letters (back and forth)
                            for (i = 0; i < letters.length - 2; i++) {
                                var p2 = p.toLowerCase();
                                forth = letters.substring(i, parseInt(i + 3));
                                back = StringHelper.reverse(forth);
                                if (p2.indexOf(forth) !== -1 || p2.indexOf(back) !== -1) {
                                    counts.neg.seqLetter++;
                                }
                            }

                            // sequential numbers (back and forth)
                            for (i = 0; i < numbers.length - 2; i++) {
                                forth = numbers.substring(i, parseInt(i + 3));
                                back = StringHelper.reverse(forth);
                                if (p.indexOf(forth) !== -1 || p.toLowerCase().indexOf(back) !== -1) {
                                    counts.neg.seqNumber++;
                                }
                            }

                            // sequential symbols (back and forth)
                            for (i = 0; i < symbols.length - 2; i++) {
                                forth = symbols.substring(i, parseInt(i + 3));
                                back = StringHelper.reverse(forth);
                                if (p.indexOf(forth) !== -1 || p.toLowerCase().indexOf(back) !== -1) {
                                    counts.neg.seqSymbol++;
                                }
                            }

                            // repeated chars
                            counts.neg.repeated = _.chain(p.toLowerCase().split('')).
                                countBy(function(val) {
                                    return val;
                                })
                                .reject(function(val) {
                                    return val === 1;
                                })
                                .reduce(function(memo, val) {
                                    return memo + val;
                                }, 0)
                                .value();

                            // Calculations
                            strength += counts.pos.numChars * 4;
                            if (counts.pos.upper) {
                                strength += (counts.pos.numChars - counts.pos.upper) * 2;
                            }
                            if (counts.pos.lower) {
                                strength += (counts.pos.numChars - counts.pos.lower) * 2;
                            }
                            if (counts.pos.upper || counts.pos.lower) {
                                strength += counts.pos.numbers * 4;
                            }
                            strength += counts.pos.symbols * 6;
                            strength += (counts.pos.middleSymbol + counts.pos.middleNumber) * 2;
                            strength += counts.pos.requirements * 2;

                            strength -= counts.neg.consecLower * 2;
                            strength -= counts.neg.consecUpper * 2;
                            strength -= counts.neg.consecNumbers * 2;
                            strength -= counts.neg.seqNumber * 3;
                            strength -= counts.neg.seqLetter * 3;
                            strength -= counts.neg.seqSymbol * 3;

                            if (matches.neg.onlyNumbers) {
                                strength -= counts.pos.numChars;
                            }
                            if (matches.neg.onlyLetters) {
                                strength -= counts.pos.numChars;
                            }
                            if (counts.neg.repeated) {
                                strength -= (counts.neg.repeated / counts.pos.numChars) * 10;
                            }
                        }

                        return Math.max(0, Math.min(100, Math.round(strength)));
                    },

                    getClass = function(s) {
                        switch (Math.round(s / 33)) {
                            case 0:
                            case 1:
                                return 'danger';
                            case 2:
                                return 'warning';
                            case 3:
                                return 'success';
                        }
                        return '';
                    };


                scope.$watch('pwd', function() {
                    scope.value = mesureStrength(scope.pwd);
                    scope.class = getClass(scope.value);
                });

            }
        };
    }

    function rockResetField(){
        return {
            restrict: 'A',
            require: 'ngModel',
            link: function($scope, element, attrs, ctrl) {
                $scope.$watch('isSend()', function(value){
                    if (value === true) {
                        ctrl.$setViewValue(undefined);
                        ctrl.$setPristine(true);
                        ctrl.$render();
                    }
                });
            }
        }
    }

    rockResetFieldIcon.$inject = ['$compile', '$templateCache', 'notification'];
    function rockResetFieldIcon($compile, $templateCache, notification){
        return {
            require: 'ngModel',
            link: function($scope, $element, $attr, $ngModel) {
                var template;
                if (!(template = $templateCache.get('form/reset-field-icon'))) {
                    template = '<i ng-show="enabled" ng-mousedown="resetField()" class="glyphicon glyphicon-remove-circle right-inner"></i>';
                    $templateCache.put('form/reset-field-icon', template);
                }
                // limit to input element of specific types
                var inputTypes = /text|search|tel|url|email|password/i;
                if ($element[0].nodeName !== "INPUT") {
                    notification.debug(new Error("'resetField' is limited to input elements"));
                    return;
                }
                if (!inputTypes.test($attr.type)) {
                    notification.debug(new Error("Invalid input type for resetField: " + $attr.type));
                    return;
                }
                $scope = $scope.$new();
                // compiled reset icon template
                template = $compile(template)($scope);
                $element.after(template);
                $scope.resetField = function() {
                    $ngModel.$setViewValue(undefined);
                    $ngModel.$setPristine(true);
                    $ngModel.$render();
                };
                $element.bind('input', function() {
                    $scope.enabled = !$ngModel.$isEmpty($element.val());
                })
                    .bind('focus', function() {
                        $scope.enabled = !$ngModel.$isEmpty($element.val());
                        $scope.$apply();
                    })
                    .bind('blur', function() {
                        $scope.enabled = false;
                        $scope.$apply();
                    });
            }
        };
    }
})();