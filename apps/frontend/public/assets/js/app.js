(function () {
    'use strict';
    angular
        .module('app', ['pascalprecht.translate', 'rock.core'])
        .config(config)
        .run(run);

    config.$inject = ['$locationProvider', '$httpProvider', '$translateProvider', '$provide', 'notificationProvider'];

    /**
         *  Configure application.
         * @param $locationProvider
         * @param $httpProvider
         * @param $translateProvider
         * @param $provide
         * @param {notificationProvider} notificationProvider
         */
    function config($locationProvider, $httpProvider, $translateProvider, $provide, notificationProvider){

        // configure location
        $locationProvider.html5Mode({
            enabled: true,
            rewriteLinks: false
        });

        // configure notification
        notificationProvider.debugEnabled(true);

        // configure http
        $httpProvider.defaults.headers.common['Content-Type'] = 'application/json';
        $httpProvider.interceptors.push(httpProvider);

        // configure i18n
        var lang = i18nProvider($translateProvider);
        $provide.value('rock', {lang: lang});
    }

    function i18nProvider($translateProvider) {
        var nav = window.navigator,
            lang = (
            angular.element(document.querySelector( 'html' )).attr('lang') || nav.language ||
            nav.browserLanguage || nav.systemLanguage || nav.userLanguage || 'en'
            ).split('-')[0];

        $translateProvider.translations('en', {"lang":{"notPage":"page not found","notContent":"content is empty","notFound":"not found","search":"search","username":"username","email":"e-mail","password":"password","confirmPassword":"confirm password","token":"token","captcha":"captcha","invalidEmail":"Email is invalid.","invalidTokenActivated":"Wrong token or user is already activated.","invalidPasswordOrEmail":"Password or email is invalid.","invalidPasswordOrUsername":"Password or login is invalid.","existsUsername":"User with this name already exists.","existsUsernameOrEmail":"User with this name\/e-mail already exists.","notExistsUser":"User with this email does not exist or is blocked.","notActivatedUser":"Account is not activated","failLogin":"Fail authorization.","failRecovery":"Fail recovery password.","failSignup":"Fail registration.","failSendEmail":"Email not sent.","failActivated":"Fail activated.","failLogout":"Fail logout.","successLogin":"You successfully login.","successLogout":"You successfully logout.","successSignup":"Thanks for signing up!<br\/>On e-mail  <b>{{email}}<\/b>, sent an email with an activation code.","successRecovery":"Your new password has been sent to your e-mail <b>{{email}}<\/b>.","successActivate":"Your account is activated.","signup":"sign up","login":"login","signin":"sign in","activation":"activation","close":"close","activate":"activate","registration":"registration","authorization":"login","resetPassword":"reset password","recovery":"recovery","loginLogout":"You're sign in. Authorization is required to <a href=\"{{url}}\" rel=\"nofollow\">logout<\/a> of your profile","signupLogout":"You're sign in. The registration must be <a href=\"{{url}}\" rel=\"nofollow\">logout<\/a> of your profile","recoveryLogout":"You're sign in. To recover the password required to <a href=\"{{url}}\" rel=\"nofollow\">logout<\/a> of your profile","notJs":"Your browser does not support JavaScript. Try to fix this in the browser settings.","logout":"logout","characters":"characters","failHTTPRequest":"HTTP-request error.","failServer":"Server error.","failAccess":"Denied access.","error":"error","value":"value","success":"success","forgotPassword":"forgot password"},"validate":{"required":"{{name}} must not be empty","notRequired":"{{name}} must be empty","min":"{{name}} must be greater than {{minValue}}","minInclusive":"{{name}} must be greater than or equals {{minValue}}","notMin":"{{name}} must not be greater than {{minValue}}","notMinInclusive":"{{name}} must not be greater than or equals {{minValue}}","max":"{{name}} must be lower than {{maxValue}}","maxInclusive":"{{name}} must be lower than or equals {{maxValue}}","notMax":"{{name}} must not be lower than {{maxValue}}","notMaxInclusive":"{{name}} must not be lower than or equals {{maxValue}}","email":"{{name}} must be valid","notEmail":"{{name}} must not be valid","regex":"{{name}} contains invalid characters","notRegex":"{{name}} does not contain invalid characters","captcha":"captcha must be valid","notCaptcha":"captcha must not be valid","confirm":"values must be equals","notConfirm":"values must not be equals","call":"{{name}} must be valid","unique":"{{value}} has already been taken","notUnique":"{{value}} not already been taken.","csrf":"CSRF-token must be valid","notCsrf":"CSRF-token must not be valid","date":"{{name}} must be date","dateFormat":"{{name}} must be a valid date. Sample format: {{format}}","notDate":"{{name}} must not be date","notDateFormat":"{{name}} must not be a valid date in the format {{format}}"}});
        $translateProvider.translations('ru', {"lang":{"notPage":"страница не найдена","notContent":"материал отсутсвует","notFound":"ничего не найдено","resetPassword":"сбросить пароль","recovery":"восстановление пароля","signup":"зарегистрироваться","signin":"войти","activation":"активация","password":"пароль","confirmPassword":"подтверждение пароля","token":"токен","captcha":"код подтверждения","login":"логин","successLogged":"вы успешно авторизированы","close":"закрыть","notJs":"Ваш браузер не поддерживает JavaScript. Попробуйте исправить это в настройках браузера.","email":"e-mail","username":"логин\/псевдоним","existsUsername":"Пользователь с таким именем уже существует.","existsUsernameOrEmail":"Пользователь с таким именем\/e-mail уже существует.","notExistsUser":"Пользователя с таким email не существует или блокирован.","invalidEmail":"Указан неверный email.","invalidPasswordOrEmail":"Указан неверный пароль или email.","invalidPasswordOrUsername":"Указан неверный пароль или логин.","notActivatedUser":"Учётная запись не активирована.","invalidTokenActivated":"Неверный токен или пользователь уже активирован.","successLogin":"Вы успешно авторизировались.","successLogout":"Вы успешно разлогинились.","successSignup":"Спасибо за регистрацию!<br\/>На указанный Вами адрес электронной почты <b>{{email}}<\/b>, отправлено письмо с подтверждением.","successRecovery":"Новый пароль, был отправлен на Ваш адрес электронной почты <b>{{email}}<\/b>.","successActivate":"Ваша учётная запись активирована.","failLogin":"Ошибка при авторизации.","failRecovery":"Ошибка при восстановлении пароля.","failSignup":"Ошибка при регистрации.","failActivated":"Ошибка при активации.","failLogout":"Ошибка при разлогировании.","failSendEmail":"email не отправлен.","loginLogout":"Вы авторизированы. Для повторной авторизации требуется <a href=\"{{url}}\" rel=\"nofollow\">выйти<\/a> из своего профиля.","signupLogout":"Вы авторизированы. Для регистрации требуется <a href=\"{{url}}\" rel=\"nofollow\">выйти<\/a> из своего профиля.","recoveryLogout":"Вы авторизированы. Для для восстановлении пароля требуется <a href=\"{{url}}\" rel=\"nofollow\">выйти<\/a> из своего профиля.","logout":"выход","characters":"символов","failHTTPRequest":"Ошибка HTTP-запроса.","failServer":"Ошибка сервера.","failAccess":"Отказано в доступе.","error":"ошибка","value":"значение","forgotPassword":"забыли пароль","success":"успех"},"validate":{"required":"{{name}} не должно быть пустым","notRequired":"{{name}} должно быть пустым","min":"{{name}} должно быть больше {{minValue}}","minInclusive":"{{name}} должно быть больше или равно {{minValue}}","notMin":"{{name}} не должно быть больше {{minValue}}","notMinInclusive":"{{name}} не должно быть больше или равно {{minValue}}","max":"{{name}} должно быть меньше {{maxValue}}","maxInclusive":"{{name}} должно быть меньше или равно {{maxValue}}","notMax":"{{name}} не должно быть меньше {{maxValue}}","notMaxInclusive":"{{name}} не дожно быть меньше или равно {{maxValue}}","email":"{{name}} должен быть верным","notEmail":"{{name}} не должен быть верным","regex":"{{name}} содержит неверные символы","notRegex":"{{name}} не содержит верные символы","captcha":"каптча должна быть верной","notCaptcha":"каптча не должна быть верной","confirm":"значения должны совпадать","notConfirm":"значения не должны совпадать","call":"{{name}} должно быть верным","unique":"{{value}} уже существует","notUnique":"{{value}} должно существовать","csrf":"CSRF-токен должен быть верным","notCsrf":"CSRF-токен не должен быть верным","date":"{{name}} должно быть датой","dateFormat":"{{name}} должно соответствовать формату: {{format}}","notDate":"{{name}} не должно быть датой","notDateFormat":"{{name}} не должно соответствовать формату: {{format}}"}});
        $translateProvider.preferredLanguage(lang);
        return lang;
    }

    httpProvider.$inject = ['$q', '$injector'];
    function httpProvider($q, $injector) {

        return {
            response : function(response) {
                /**
                                 * @type {httpUtils} httpUtils
                                 */
                var httpUtils = $injector.get('httpUtils');
                if (!response.config.cache) {
                    httpUtils.csrf(response.data, response.headers);
                }
                return response;

            },
            responseError: function (response) {
                // do something on error
                /**
                                 * @type {httpUtils} httpUtils
                                 */
                var httpUtils = $injector.get('httpUtils');
                if (response.config && !response.config.cache) {
                    httpUtils.csrf(response.data, response.headers);
                }
                httpUtils.error(response.data, response.status, response.statusText);
                return $q.reject(response);
            }
        };
    }

    run.$inject = ['$rootScope','$http', 'csrfUtils', 'userUtils', 'alias', 'rock', 'htmlUtils'];
    /**
         *
         * @param $rootScope
         * @param $http
         * @param {csrfUtils} csrfUtils
         * @param {userUtils} userUtils
         * @param {alias} alias
         * @param rock
         * @param {htmlUtils} htmlUtils
         */
    function run($rootScope, $http, csrfUtils, userUtils, alias, rock, htmlUtils)
    {
        runCSRF(csrfUtils);
        $rootScope.rock = {};
        /** @type {string} */
        $rootScope.rock.lang = rock.lang;
        /** @type {csrfUtils} */
        $rootScope.rock.csrf = csrfUtils;
        /**  @type {userUtils} */
        $rootScope.rock.user = userUtils;
        /**  @type {htmlUtils} */
        $rootScope.rock.html = htmlUtils;
        /**  @type {alias} */
        $rootScope.rock.alias = alias;

        $rootScope.$watch(function(scope){
            return scope.rock.csrf.getToken();
        }, function(value) {
            if (!value) {
                return;
            }
            $http.defaults.headers.post['X-CSRF-Token'] = value;
        });
    }

    function runCSRF(csrfUtils)
    {
        var csrfParam = angular.element(document.querySelector('meta[name=csrf-param]')).attr('content'),
            csrfToken = angular.element(document.querySelector('meta[name=csrf-token]')).attr('content');

        if (csrfParam && csrfToken) {
            csrfUtils.addToken(csrfToken);
            csrfUtils.addParam(csrfParam);
        }
    }
})();