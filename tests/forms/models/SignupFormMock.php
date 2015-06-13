<?php

namespace rockunit\forms\models;


use apps\common\models\forms\BaseSignupForm;
use rock\components\ModelEvent;
use rock\i18n\i18n;
use rockunit\db\models\Users;

class SignupFormMock extends BaseSignupForm
{
    public function validateExistsUser()
    {
        if ($this->hasErrors()) {
            return;
        }
        if (Users::existsByUsernameOrEmail($this->email, $this->username, null)) {
            $this->addError('e_signup', i18n::t('existsUsernameOrEmail'));
        }
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
} 