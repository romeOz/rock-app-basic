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
        if (!isset($this->users)) {
            if (!$this->users = Users::findOneByEmail($this->email, Users::STATUS_ACTIVE, false)) {
                $this->addError('alerts', i18n::t('invalidEmail'));
            }
        }

        return $this->users;
    }
} 