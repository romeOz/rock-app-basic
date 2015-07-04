<?php

namespace rockunit\forms\models;

use apps\common\models\forms\BaseLoginForm;
use rock\i18n\i18n;
use rockunit\db\models\Users;

class LoginFormMock extends BaseLoginForm
{
    /**
     * Finds user by `email`
     *
     * @return Users
     */
    public function getUsers()
    {
        if (!isset($this->users)) {
            if (!$this->users = Users::findOneByEmail($this->email, null, false)) {
                $this->addError('alerts', i18n::t('notExistsUser'));
            }
        }

        return $this->users;
    }
}