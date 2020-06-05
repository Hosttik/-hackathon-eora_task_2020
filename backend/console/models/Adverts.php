<?php

namespace console\models;

use console\classes\helpers\DatesSaveTrait;
use Yii;

/**
 * This is the model class for table "adverts".
 *
 * @property int $id
 * @property int $spec_user_id
 * @property string $title
 * @property string $description
 * @property int $cost
 * @property int $date_created
 * @property int $date_updated
 */
class Adverts extends \yii\db\ActiveRecord
{
    use DatesSaveTrait;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'adverts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['spec_user_id', 'title', 'description', 'cost'], 'required'],
            [['spec_user_id', 'cost'], 'integer'],
            [['title', 'description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'spec_user_id' => 'Spec User ID',
            'title' => 'Title',
            'description' => 'Description',
            'cost' => 'Cost',
            'date_created' => 'Date Created',
            'date_updated' => 'Date Updated',
        ];
    }
}
