<?php

namespace apps\frontend\controllers;


use apps\common\models\users\Users;
use apps\frontend\models\RecoveryForm;
use rock\base\BaseException;
use rock\components\ModelEvent;
use rock\csrf\CSRF;
use rock\events\Event;
use rock\i18n\i18n;
use rock\log\Log;
use rock\mail\Mail;
use rock\session\Session;
use rock\user\User;

class RecoveryController extends BaseAuthController
{
    public $emailBodyTpl = '@common.views/email/{lang}/recovery';
    protected $keySessionFlash  = 'successRecovery';

    public function activeRecovery(User $user, Session $session, CSRF $CSRF, Mail $mail)
    {
        if ($user->isLogged()) {
            $placeholders['content'] = $this->getMessageLogout($CSRF, 'recoveryLogout');

            return $this->render('success', $placeholders);
        }

        if (($message = $session->getFlash($this->keySessionFlash)) && isset($message['email'])) {
            $placeholders['content'] = $this->template->getChunk(
                '@common.views/elements/alert-success',
                ['output' => i18n::t($this->keySessionFlash, ['email' => $message['email']])]
            );

            return $this->render('success', $placeholders);
        }

        $model = new RecoveryForm();
        // redirect
        Event::on($model, RecoveryForm::EVENT_AFTER_RECOVERY, function(ModelEvent $event) use($session, $mail, $model){
            $this->sendMail($mail, $event->result, $model);
            $session->setFlash('successRecovery', ['email' => $event->result->email]);
            $this->redirect();
        });
        $model->load($_POST);
        $placeholders['model'] = $model;

        return $this->render('index', $placeholders);
    }


    protected function sendMail(Mail $mail, Users $users, RecoveryForm $model)
    {
        $subject = i18n::t('subjectRecovery', ['site_name' => i18n::t('siteName')]);
        $body = $this->prepareBody($users);
        try {
            $mail
                ->address($users->email)
                ->subject($subject)
                ->body($body)
                ->send();
        } catch (\Exception $e) {
            $model->addError('alerts', i18n::t('failSendEmail'));
            Log::warn(BaseException::convertExceptionToString($e));
        }
    }

    protected function prepareBody(Users $users)
    {
        return $this->template->getChunk($this->emailBodyTpl, $users->toArray());
    }
}