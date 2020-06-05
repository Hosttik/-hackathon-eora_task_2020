<?php

use common\models\User;
use yii\db\Migration;

/**
 * Class m200504_175953_create_default_data
 */
class m200504_175953_create_default_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $user = new User();
        $user->setPassword(123123);
        $user->generateAuthKey();
        $user->generatePasswordResetToken();
        $user->generateEmailVerificationToken();
        $user->email = 'plumber@mail.ru';
        $user->status = User::STATUS_ACTIVE;
        $user->first_name = 'Николай';
        $user->type = User::TYPE_SPECIALIST;
        $user->job_name = User::JOB_PLUMBER;
        $user->description = 'Лучший сантехник на все времена';
        $user->save();

        $user = new User();
        $user->setPassword(123123);
        $user->generateAuthKey();
        $user->generatePasswordResetToken();
        $user->generateEmailVerificationToken();
        $user->email = 'lawyer@mail.ru';
        $user->status = User::STATUS_ACTIVE;
        $user->first_name = 'Анастасия';
        $user->type = User::TYPE_SPECIALIST;
        $user->job_name = User::JOB_LAWYER;
        $user->description = 'Помогу с любым юридическим вопросом';
        $user->save();

        $user = new User();
        $user->setPassword(123123);
        $user->generateAuthKey();
        $user->generatePasswordResetToken();
        $user->generateEmailVerificationToken();
        $user->email = 'doctor@mail.ru';
        $user->status = User::STATUS_ACTIVE;
        $user->first_name = 'Владимир';
        $user->type = User::TYPE_SPECIALIST;
        $user->job_name = User::JOB_DOCTOR;
        $user->description = 'Врач-офтольмолог';
        $user->save();

        $user = new User();
        $user->setPassword(123123);
        $user->generateAuthKey();
        $user->generatePasswordResetToken();
        $user->generateEmailVerificationToken();
        $user->email = 'sem@mail.ru';
        $user->status = User::STATUS_ACTIVE;
        $user->first_name = 'Семен';
        $user->type = User::TYPE_CLIENT;
        $user->save();

        $user = new User();
        $user->setPassword(123123);
        $user->generateAuthKey();
        $user->generatePasswordResetToken();
        $user->generateEmailVerificationToken();
        $user->email = 'kirill@mail.ru';
        $user->status = User::STATUS_ACTIVE;
        $user->first_name = 'Кирилл';
        $user->type = User::TYPE_CLIENT;
        $user->save();

        $this->saveChats();
        $this->saveAdverts();
    }


    private function saveChats()
    {
        $kir = User::findByEmail('kirill@mail.ru');
        $plumber = User::findByEmail('plumber@mail.ru');
        $time = time();
        $columns = \console\models\Chat::getTableSchema()->columnNames;
        array_shift($columns);
        $rows = [
            [
                $kir->id,
                $plumber->id,
                'Добрый вечер, не поможете, в ванной кран течет',
                $time + 10,
                $time + 10,
            ],[
                $plumber->id,
                $kir->id,
                'Добрый вечер, да конечно в чем конкретно ваша проблема? подтекает в месте соединения? Форма крана гусак?',
                $time + 100,
                $time + 100,
            ],[
                $kir->id,
                $plumber->id,
                'Я не разбираюсь в видах крана, не могли бы по видеосвязи посмотреть =)',
                $time + 200,
                $time + 200,
            ],[
                $plumber->id,
                $kir->id,
                'Да, звоните',
                $time + 300,
                $time + 300,
            ]
        ];
        $this->batchInsert(\console\models\Chat::tableName(), $columns, $rows);
    }

    private function saveAdverts()
    {
        $plumber = User::findByEmail('plumber@mail.ru');
        $lawyer = User::findByEmail('lawyer@mail.ru');
        $doctor = User::findByEmail('doctor@mail.ru');
        $time = time();
        $columns = \console\models\Adverts::getTableSchema()->columnNames;
        array_shift($columns);
        $rows = [
            [
                $plumber->id,
                'Устранение протечки',
                'Оказываю любую помощь, по протечкам труб',
                100,
                $time + 10,
                $time + 10,
            ],[
                $plumber->id,
                'Подключение посудомойки, стиралки',
                '',
                200,
                $time + 10,
                $time + 10,
            ],[
                $lawyer->id,
                'Помощь по оформлению документов на недвижимость',
                '',
                300,
                $time + 10,
                $time + 10,
            ],[
                $lawyer->id,
                'Помощь по гражданскому делу',
                'А также обращайтесь по семейным вопросам',
                400,
                $time + 10,
                $time + 10,
            ],[
                $doctor->id,
                'Офтольмолог',
                'Проверю зрение онлайн, дам рекомендации',
                500,
                $time + 10,
                $time + 10,
            ]
        ];
        $this->batchInsert(\console\models\Adverts::tableName(), $columns, $rows);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200504_175953_create_default_data cannot be reverted.\n";

        return false;
    }
}
