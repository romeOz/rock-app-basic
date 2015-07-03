<?php

namespace apps\common\models\users;

use rock\db\ActiveQuery;

class BaseUsersQuery extends ActiveQuery
{
    public static function tableName()
    {
        return 'users';
    }

    // Fields

    public function fields()
    {
        return $this->select([
            'id', 'username', 'token', 'status',
            'email', 'login_last'
        ]);
    }

    public function fieldsSmall()
    {
        return $this->select(['id', 'username']);
    }


    // WHERE by

    public function byId($id)
    {
        return $this->andWhere(['{{' . static::tableName() . '}}.[[id]]' => $id]);
    }

    public function byIds(array $ids)
    {
        $query = $this
            ->status()
            ->andWhere(['{{' . static::tableName() . '}}.[[id]]' => $ids]);
        return $query;
    }

    public function byUsername($username)
    {
        $table = static::tableName();
        return $this->andWhere(
            "{{{$table}}}.[[username_hash]]=UNHEX(MD5(:username))",
            [':username' => $username]
        );
    }

    /**
     * @param string $email
     * @return static
     */
    public function byEmail($email)
    {
        $table = static::tableName();
        return $this->andWhere(
            "{{{$table}}}.[[email_hash]]=UNHEX(MD5(:email))",
            [':email' => $email]
        );
    }

    /**
     * @param string $token
     * @return static
     */
    public function byToken($token)
    {
        return $this->andWhere(['{{' . static::tableName() . '}}.[[token]]' => $token]);
    }

    /**
     * @param int $status
     * @return static
     */
    public function status($status = Users::STATUS_ACTIVE)
    {
        return $this->andWhere(['{{' . static::tableName() . '}}.[[status]]' => $status]);
    }
}