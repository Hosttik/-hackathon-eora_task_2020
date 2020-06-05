<?php

namespace console\models;

use console\classes\helpers\DatesSaveTrait;
use Yii;

/**
 * This is the model class for table "chat".
 *
 * @property int $id
 * @property int $from_user_id
 * @property int $to_user_id
 * @property string $message
 * @property int $date_created
 * @property int $date_updated
 */
class Chat extends \yii\db\ActiveRecord
{
    use DatesSaveTrait;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'chat';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['from_user_id', 'to_user_id', 'message'], 'required'],
            [['from_user_id', 'to_user_id'], 'integer'],
            [['message'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'from_user_id' => 'From User ID',
            'to_user_id' => 'To User ID',
            'message' => 'Message',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
        ];
    }
}
