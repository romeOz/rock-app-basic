<?php

namespace apps\common\models\forms;


use apps\common\models\users\Users;
use rock\captcha\Captcha;
use rock\components\Model;
use rock\components\ModelEvent;
use rock\csrf\CSRF;
use rock\di\Container;
use rock\helpers\Instance;
use rock\i18n\i18n;
use rock\security\Security;
use rock\validate\Validate;

class BaseRecoveryForm extends Model
{
    const EVENT_BEFORE_RECOVERY = 'beforeRecovery';
    const EVENT_AFTER_RECOVERY = 'afterRecovery';


    /** @var  string */
    public $email;
    /** @var  string */
    public $captcha;
    /** @var  string */
    public $_csrf;

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
                ['email', 'captcha'], 'trim'
            ],
            [
                ['email', 'captcha'], 'required',
            ],
            [
                'email', 'length' => [4, 80, true], 'email'
            ],
            [
                'captcha', 'length' => [null, 7, true], 'captcha' => [$this->captchaInstance->getSession()]
            ],
            [
                'email', '!lowercase'
            ],
            [
                ['email', 'captcha'], 'removeTags'
            ],
            [
                'email', 'validateEmail'
            ],
        ];
    }

    public function safeAttributes()
    {
        return ['email', 'captcha', $this->csrfInstance->csrfParam];
    }

    public function attributeLabels()
    {
        return [
            'email' => i18n::t('email'),
            'captcha'=> i18n::t('captcha'),
        ];
    }

    public function validate(array $attributes = NULL, $clearErrors = true)
    {
        if (!$this->beforeRecovery() || !parent::validate()) {
            return false;
        }

        $this->afterRecovery();
        return true;
    }

    /**
     * Validates the email.
     * This method serves as the inline validation for password.
     */
    public function validateEmail()
    {
        if ($this->hasErrors()) {
            return;
        }
        $this->getUsers();
    }

    public function validateCSRF($input)
    {
        $v = Validate::required()->csrf()->placeholders(['name' => 'CSRF-token']);
        if (!$v->validate($input)) {
            $this->addError('alerts', $v->getFirstError());
        }
    }

    /** @var  Users */
    protected $users;

    /**
     * Finds user by `email`
     *
     * @return Users
     */
    protected function getUsers()
    {
        if (!isset($this->users)) {
            if (!$this->users = Users::findOneByEmail($this->email, Users::STATUS_ACTIVE, false)) {
                $this->addError('alerts', i18n::t('invalidEmail'));
            }
        }

        return $this->users;
    }

    public function beforeRecovery()
    {
        $event = new ModelEvent();
        $this->trigger(self::EVENT_BEFORE_RECOVERY, $event);
        return $event->isValid;
    }

    public function afterRecovery()
    {
        $users = $this->getUsers();
        /** @var Security $security */
        $security = Container::load('security');
        $password = $security->generateRandomString(7);
        $users->setPassword($password);
        if (!$users->save()) {
            $this->addError('alerts', i18n::t('failRecovery'));
            return;
        };
        $event = new ModelEvent();
        $users->password = $password;
        $event->result = $users;
        $this->trigger(self::EVENT_AFTER_RECOVERY, $event);
    }
}