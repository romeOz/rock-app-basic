<?php

namespace rockunit\forms\models;


use apps\common\models\forms\BaseSignupForm;
use rock\components\ModelEvent;
use rock\i18n\i18n;
use rockunit\db\models\Users;

class SignupFormMock extends BaseSignupForm
{

    protected function validateExistsUser()
    {
        if ($this->hasErrors()) {
            return true;
        }
        if (Users::existsByUsernameOrEmail($this->email, $this->username, null)) {
            $this->addErrorAsPlaceholder(i18n::t('existsUsernameOrEmail'), 'e_signup');
            return false;
        }
        return true;
    }

    public function afterSignup()
    {
        if (!$users = Users::create($this->getAttributes())) {
            $this->addErrorAsPlaceholder(i18n::t('failSignup'), 'e_signup');
            return false;
        }
        $this->users = $users;
        $this->isSignup = true;
        $result = $users->toArray();

        $event = new ModelEvent();
        $event->result = $result;
        $this->trigger(self::EVENT_AFTER_SIGNUP, $event);

        return true;
    }
} 