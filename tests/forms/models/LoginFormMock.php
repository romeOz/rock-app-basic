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
        if (!isset($this->_users)) {
            if (!$this->_users = Users::findOneByEmail($this->email, null, false)) {
                $this->addErrorAsPlaceholder(i18n::t('notExistsUser'), 'e_login');
            }
        }

        return $this->_users;
    }
}