<?php

namespace apps\common\models\forms;

use apps\common\models\users\Users;
use rock\captcha\Captcha;
use rock\components\Model;
use rock\components\ModelEvent;
use rock\csrf\CSRF;
use rock\db\Connection;
use rock\helpers\Instance;
use rock\i18n\i18n;
use rock\validate\Validate;

class BaseSignupForm extends Model
{
    const EVENT_BEFORE_SIGNUP = 'beforeSignup';
    const EVENT_AFTER_SIGNUP = 'afterSignup';


    public $email;
    public $username;
    public $_csrf;
    public $password_confirm;
    public $captcha;
    public $password;


    public $defaultStatus = Users::STATUS_NOT_ACTIVE;
    public $generateToken = true;

    /** @var Connection */
    protected $connection;

    /** @var  CSRF */
    protected $csrfInstance = 'csrf';
    /** @var  Captcha */
    protected $captchaInstance = 'captcha';

    public function init()
    {
        parent::init();

        $this->csrfInstance = Instance::ensure($this->csrfInstance);
        $this->captchaInstance = Instance::ensure($this->captchaInstance);
    }

    public function rules()
    {
        return [
            [
                '_csrf', 'validateCSRF', 'one'
            ],
            [
                ['email', 'username', 'password', 'password_confirm', 'captcha'], 'trim'
            ],
            [
                ['email', 'username', 'password', 'password_confirm', 'captcha'], 'required',
            ],
            [
                'email', 'length' => [4, 80, true], 'email'
            ],
            [
                'username', 'length' => [3, 80, true], 'regex' => ['/^[\w\s\-\*\@\%\#\!\?\.\)\(\+\=\~\:]+$/i']
            ],
            [
                'password', 'length' => [6, 20, true], 'regex' => ['/^[a-z\d\-\_\.]+$/i']
            ],
            [
                'password_confirm', 'confirm' => [$this->password]
            ],
            [
                'captcha', 'captcha' => [$this->captchaInstance->getSession()]
            ],
            [
                'email', '!lowercase'
            ],
            [
                ['email', 'username', 'password', 'password_confirm', 'captcha'], 'removeTags'
            ],
            [
                'username', 'validateExistsUser'
            ],
        ];
    }


    public function safeAttributes()
    {
        return ['email', 'username', 'password', 'password_confirm', 'captcha', $this->csrfInstance->csrfParam];
    }

    public function attributeLabels()
    {
        return [
            'email'=> i18n::t('email'),
            'password'=> i18n::t('password'),
            'username'=> i18n::t('username'),
            'password_confirm'=> i18n::t('confirmPassword'),
            'captcha'=> i18n::t('captcha'),
        ];
    }

    /**
     * @var Users
     */
    protected $users;

    public function validate(array $attributes = NULL, $clearErrors = true)
    {
        if (!$this->beforeSignup() || !parent::validate()) {
            return false;
        }

        $this->afterSignup();
        return true;
    }

    public function validateExistsUser()
    {
        if ($this->hasErrors()) {
            return;
        }
        if (Users::existsByUsernameOrEmail($this->email, $this->username, null)) {
            $this->addError('e_signup', i18n::t('existsUsernameOrEmail'));
        }
    }

    public function validateCSRF($input)
    {
        $v = Validate::required()->csrf()->placeholders(['name' => 'CSRF-token']);
        if (!$v->validate($input)) {
            $this->addError('e_signup', $v->getFirstError());
        }
    }

    public function beforeSignup()
    {
        $event = new ModelEvent();
        $this->trigger(self::EVENT_BEFORE_SIGNUP, $event);
        return $event->isValid;
    }

    public function afterSignup()
    {
        if (!$users = Users::create($this->getAttributes(), $this->defaultStatus, $this->generateToken)) {
            $this->addError('e_signup', i18n::t('failSignup'));
            return;
        }
        $this->users = $users;
        $this->users->id = $this->users->primaryKey;

        $event = new ModelEvent();
        $event->result = $users;
        $this->trigger(self::EVENT_AFTER_SIGNUP, $event);
    }

    /**
     * @return Users
     */
    public function getUsers()
    {
        return $this->users;
    }

    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;
    }
}