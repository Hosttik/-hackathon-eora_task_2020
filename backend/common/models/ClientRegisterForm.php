<?php
namespace common\models;

use Yii;
use yii\base\Model;

/**
 * Login form
 */
class ClientRegisterForm extends Model
{
    public $email;
    public $password;
    public $first_name;

    private $_user;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'password', 'first_name'], 'required'],
            ['email', 'unique']
        ];
    }

    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function register()
    {
        $user = new User();
        $user->setPassword($this->password);
        $user->generateAuthKey();
        $user->generatePasswordResetToken();
        $user->generateEmailVerificationToken();
        $user->email = $this->email;
        $user->status = User::STATUS_ACTIVE;
        $user->first_name = $this->first_name;
        $user->type = User::TYPE_CLIENT;

        if ($user->save()) {
            return Yii::$app->user->login($this->getUser(),3600 * 24 * 30);
        }

        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findByEmail($this->email);
        }

        return $this->_user;
    }
}
