<?php

namespace rockunit\forms\models;


use apps\common\models\forms\BaseRecoveryForm;
use rock\i18n\i18n;
use rockunit\db\models\Users;

class RecoveryFormMock extends BaseRecoveryForm
{
    /**
     * Finds user by `email`
     *
     * @return Users
     */
    public function getUsers()
    {
        if (!isset($this->_users)) {
            if (!$this->_users = Users::findOneByEmail($this->email, Users::STATUS_ACTIVE, false)) {
                $this->addErrorAsPlaceholder(i18n::t('invalidEmail'), 'e_recovery');
            }
        }

        return $this->_users;
    }
} 