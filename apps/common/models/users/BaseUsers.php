<?php

namespace apps\common\models\users;

use rock\db\ActiveRecord;
use rock\helpers\NumericHelper;
use rock\Rock;

/**
 * @property int id
 * @property string username
 * @property string password
 * @property string email
 * @property string token
 * @property int status
 * @property int ctime
 * @property int login_last
 */
class BaseUsers extends ActiveRecord
{
    const S_REGISTRATION = 'registration';

    const STATUS_DELETED = 0;
    const STATUS_BLOCKED = 1;
    const STATUS_NOT_ACTIVE = 2;
    const STATUS_ACTIVE = 3;

    /**
     * Declares the name of the database table associated with this AR class.
     *
     * @return string
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     * @return BaseUsersQuery
     */
    public static function find()
    {
        return new BaseUsersQuery(get_called_class());
    }

    /**
     * Finds url by `username`.
     * @param string    $username `username` of user.
     * @param int|null  $status `status` of user.
     * @return bool|string
     */
    public static function findUrlByUsername($username, $status = self::STATUS_ACTIVE)
    {
        $query = static::find()->select(['url'])->byUsername($username);
        if (isset($status)) {
            $query->status($status);
        }
        return $query->asArray()->scalar();
    }

    /**
     * Finds user by `id`.
     * @param int  $id      `id` of user.
     * @param int|null  $status `status` of user.
     * @param bool $asArray result as `Array`.
     * @return static|array
     */
    public static function findOneById($id, $status = self::STATUS_ACTIVE, $asArray = true)
    {
        $query = static::find()->byId($id);
        if (isset($status)) {
            $query->status($status);
        }
        return $query->asArray($asArray)->one();
    }

    /**
     * Finds user by `username`.
     *
     * @param  string $username `username` of user.
     * @param int|null  $status `status` of user.
     * @param bool    $asArray  result as `Array`.
     * @return static|array
     */
    public static function findOneByUsername($username, $status = self::STATUS_ACTIVE, $asArray = true)
    {
        $query = static::find()->byUsername($username);
        if (isset($status)) {
            $query->status($status);
        }
        return $query->asArray($asArray)->one();
    }

    /**
     * Finds user by `email`.
     *
     * @param  string $email   `email` of user.
     * @param int|null  $status `status` of user.
     * @param bool    $asArray result as `Array`.
     * @return static|array
     */
    public static function findOneByEmail($email, $status = self::STATUS_ACTIVE, $asArray = true)
    {
        $query = static::find()->byEmail($email);
        if (isset($status)) {
            $query->status($status);
        }
        return $query->asArray($asArray)->one();
    }

    /**
     * Finds user by `token`.
     *
     * @param  string      $token `token` of user
     * @param int|null  $status `status` of user
     * @param bool    $asArray result as `Array`
     * @return static|array
     */
    public static function findByToken($token, $status = self::STATUS_ACTIVE, $asArray = true)
    {
        $query = static::find()->byToken($token);
        if (isset($status)) {
            $query->status($status);
        }
        return $query->asArray($asArray)->one();
    }

    /**
     * Exists user by `id`.
     *
     * @param  int $id `id` of user.
     * @param int|null  $status `status` of user.
     * @return bool
     */
    public static function existsById($id, $status = self::STATUS_ACTIVE)
    {
        $query = static::find()->byId($id);
        if (isset($status)) {
            $query->status($status);
        }
        return $query->exists();
    }

    /**
     * Exists user by `username`.
     *
     * @param  string $username `username` of user.
     * @param int|null  $status `status` of user.
     * @return bool
     */
    public static function existsByUsername($username, $status = self::STATUS_ACTIVE)
    {
        $query = static::find()->byUsername($username);
        if (isset($status)) {
            $query->status($status);
        }
        return $query->exists();
    }

    /**
     * Exists user by `email`.
     *
     * @param  string $email `email` of user.
     * @param int|null  $status `status` of user.
     * @return bool
     */
    public static function existsByEmail($email, $status = self::STATUS_ACTIVE)
    {
        $query = static::find()->byEmail($email);
        if (isset($status)) {
            $query->status($status);
        }
        return $query->exists();
    }

    /**
     * Exists user by `email` or `username`.
     *
     * @param     $email    `email` of user.
     * @param     $username `username` of user.
     * @param int|null  $status `status` of user.
     * @return bool
     */
    public static function existsByUsernameOrEmail($email, $username, $status = self::STATUS_ACTIVE)
    {
        $table = static::tableName();
        $query = static::find()
            ->orWhere(
                "{{{$table}}}.[[email_hash]]=UNHEX(MD5(:email))",
                [':email' => $email]
            )
            ->orWhere(
                "{{{$table}}}.[[username_hash]]=UNHEX(MD5(:username))",
                [':username' => $username]
            );
        if (isset($status)) {
            $query->status($status);
        }
        return $query->exists();
    }

    /**
     * Creates a new user.
     *
     * @param  array $attributes the attributes given by field => value.
     * @param int    $defaultStatus
     * @param bool   $generateToken
     * @return static the newly created model, or null on failure.
     */
    public static function create($attributes, $defaultStatus = self::STATUS_NOT_ACTIVE, $generateToken = true)
    {
        /** @var Users $user */
        $user = new static();
        $user->setScenario(self::S_REGISTRATION);
        $user->setAttributes($attributes);
        $user->setPassword($attributes['password']);
        $user->setHash(['username', 'email']);
        if ($generateToken === true) {
            $user->generateToken();
        }
        $user->setStatus($defaultStatus);
        if ($user->save()) {
            return $user;
        }
        return null;
    }

    /**
     * Deletes user by `username`.
     * @param string $username `username` of user.
     * @return int
     */
    public static function deleteByUsername($username)
    {
        $users = static::find();
        return static::deleteAll($users->byUsername($username)->where, $users->params);
    }

    /**
     * Activate user.
     * @param string $token
     * @return static
     */
    public static function activate($token)
    {
        if (empty($token) || (!$user = static::findByToken($token, static::STATUS_NOT_ACTIVE, false, []))) {
            return null;
        }
        $user->removeToken();
        $user->setStatus(static::STATUS_ACTIVE);
        if ($user->save()) {
            return $user;
        }
        return null;
    }

    /**
     * Validates password.
     *
     * @param  string  $password password to validate.
     * @return boolean if password provided is valid for current user.
     */
    public function validatePassword($password)
    {
        return Rock::$app->security->validatePassword($password, $this->password);
    }

    /**
     * Set status.
     *
     * @param int $status `status` of user.
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Generates password hash from password and sets it to the model.
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = Rock::$app->security->generatePasswordHash($password);
    }

    /**
     * Set hash for attributes.
     *
     * @param array $attributes
     */
    public function setHash(array $attributes)
    {
        foreach ($attributes as $attribute) {
            $this->{$attribute .'_hash'} = NumericHelper::hexToBin(md5($this->$attribute));
        }
    }

    /**
     * Generates new password reset token.
     */
    public function generateToken()
    {
        $this->token = Rock::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token.
     */
    public function removeToken()
    {
        $this->token = null;
    }
}