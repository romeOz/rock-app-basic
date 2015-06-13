<?php

namespace apps\common\models\forms;


use apps\common\models\users\Users;
use rock\components\Model;
use rock\components\ModelEvent;
use rock\csrf\CSRF;
use rock\date\DateTime;
use rock\helpers\Instance;
use rock\i18n\i18n;
use rock\validate\Validate;

class BaseLoginForm extends Model
{
    const EVENT_BEFORE_LOGIN = 'beforeLogin';
    const EVENT_AFTER_LOGIN = 'afterLogin';

    /** @var  string */
    public $email;
    /** @var  string */
    public $password;
    /** @var  string */
    public $_csrf;

    /** @var  CSRF */
    protected $csrfInstance = 'csrf';

    public function init()
    {
        parent::init();
        $this->csrfInstance = Instance::ensure($this->csrfInstance);
    }

    public function rules()
    {
        return [
            [
                '_csrf', 'validateCSRF', 'one'
            ],
            [
                ['email', 'password'], 'trim'
            ],
            [
                ['email', 'password'], 'required',
            ],
            [
                'email', 'length' => [4, 80, true], 'email'
            ],
            [
                'password', 'length' => [6, 20, true], 'regex' => ['/^[a-z\d\-\_\.]+$/i']
            ],
            [
                'email', '!lowercase'
            ],
            [
                ['email', 'password'], 'removeTags'
            ],
            [
                'password', 'validatePassword', 'validateStatus'
            ],
        ];
    }

    public function safeAttributes()
    {
        return ['email', 'password', $this->csrfInstance->csrfParam];
    }


    public function attributeLabels()
    {
        return [
            'email' => i18n::t('email'),
            'password'=> i18n::t('password')
        ];
    }

    public function validate(array $attributes = null, $clearErrors = true)
    {
        if (!$this->beforeLogin() || !parent::validate($attributes, $clearErrors)) {
            return false;
        }
        $this->afterLogin();
        return true;
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param $password
     * @return bool
     */
    public function validatePassword($password)
    {
        if ($this->hasErrors()) {
            return;
        }
        if (!$user = $this->getUsers()) {
            return;
        }
        if (!$user->validatePassword($password)) {
            $this->addError('e_login', i18n::t('invalidPasswordOrEmail'));
        }
    }

    public function validateCSRF($input)
    {
        $v = Validate::required()->csrf()->placeholders(['name' => 'CSRF-token']);
        if (!$v->validate($input)) {
            $this->addError('e_login', $v->getFirstError());
        }
    }

    public function validateStatus()
    {
        if ($this->hasErrors()) {
            return;
        }
        if (!$user = $this->getUsers()) {
            return;
        }

        if ($user->status !== Users::STATUS_ACTIVE) {
            $this->addError('e_login', i18n::t('notActivatedUser'));
        }
    }

    /** @var  Users */
    protected $users;

    /**
     * Finds user by `email`.
     *
     * @return Users
     */
    public function getUsers()
    {
        if (!isset($this->users)) {
            if (!$this->users = Users::findOneByEmail($this->email, null, false)) {
                $this->addError('e_login', i18n::t('notExistsUser'));
            }
        }

        return $this->users;
    }

    public function beforeLogin()
    {
        $event = new ModelEvent();
        $this->trigger(self::EVENT_BEFORE_LOGIN, $event);
        return $event->isValid;
    }

    public function afterLogin()
    {
        $users = $this->getUsers();
        $users->login_last = DateTime::set()->isoDatetime();
        if (!$users->save()) {
            $this->addError('e_login', i18n::t('failLogin'));
            return;
        }

        $event = new ModelEvent();
        $event->result = $users;
        $this->trigger(self::EVENT_AFTER_LOGIN, $event);
    }
}