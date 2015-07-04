<?php

namespace apps\frontend\controllers;


use apps\common\models\users\Users;
use apps\frontend\models\SignupForm;
use rock\base\BaseException;
use rock\components\ModelEvent;
use rock\core\Controller;
use rock\csrf\CSRF;
use rock\events\Event;
use rock\i18n\i18n;
use rock\log\Log;
use rock\mail\Mail;
use rock\session\Session;
use rock\url\Url;
use rock\user\User;

class SignupController extends Controller
{
    protected $activateUrl      = '@link.home/activation.html';
    protected $keySessionFlash  = 'successSignup';
    protected $emailBodyTpl     = '@common.views/email/{lang}/activate';

    public function actionSignup(User $user, CSRF $CSRF, Session $session, Mail $mail)
    {
        if ($user->isLogged()) {
            $placeholders['content'] = $this->getMessageLogout($CSRF, 'signupLogout');

            return $this->render('success', $placeholders);
        }

        if (($message = $session->getFlash($this->keySessionFlash)) && isset($message['email'])) {
            $placeholders['content'] = $this->template->getChunk(
                '@common.views/elements/alert-success',
                ['output' => i18n::t($this->keySessionFlash, ['email' => $message['email']])]
            );

            return $this->render('success', $placeholders);
        }

        $model = new SignupForm();

        // redirect
        Event::on($model, SignupForm::EVENT_AFTER_SIGNUP, function(ModelEvent $event) use($session, $mail, $model){
            $this->sendMail($mail, $event->result, $model);
            $this->redirect($session, $event->result);
        });
        $model->load($_POST);
        $placeholders['model'] = $model;

        return $this->render('index.php', $placeholders);
    }

    protected function getMessageLogout(CSRF $CSRF, $key, $layout = '@common.views/elements/alert-info')
    {
        $args = [
            $CSRF->csrfParam => $CSRF->get(),
            'service' => 'logout'
        ];
        $content = i18n::t($key, ['url' => Url::set()->addArgs($args)->getRelativeUrl(true)]);
        return $this->template->getChunk($layout, ['output' => $content]);
    }

    /**
     * @param Session $session
     * @param Users $users
     */
    protected function redirect(Session $session, Users $users)
    {
        $session->setFlash('successSignup', ['email' => $users->email]);
        if (!isset($this->redirectUrl)) {
            $this->response->refresh()->send(true);
            return;
        }
        $this->response->redirect($this->redirectUrl)->send(true);
    }

    /**
     * @param Mail $mail
     * @param Users $users
     * @param SignupForm $model
     */
    public function sendMail(Mail $mail, Users $users, SignupForm $model)
    {
        $subject = i18n::t('subjectRegistration', ['site_name' => i18n::t('siteName')]);
        $body = $this->prepareBody($model, $users);

        try {
            $mail->address($users->email)
                ->subject($subject)
                ->body($body)
                ->send();
        } catch (\Exception $e) {
            $model->addError('alerts', i18n::t('failSendEmail'));
            Log::warn(BaseException::convertExceptionToString($e));
        }
    }

    protected function prepareBody(SignupForm $model, Users $users)
    {
        $placeholders = $users->toArray();
        $placeholders['password'] = $model->password;
        if ($model->generateToken) {
            $placeholders['url'] = Url::set($this->activateUrl)
                ->addArgs(['token' => $placeholders['token']])
                ->getAbsoluteUrl();
        }

        return $this->template->getChunk($this->emailBodyTpl, $placeholders);
    }
}