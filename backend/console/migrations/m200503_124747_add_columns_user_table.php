<?php

use common\models\User;
use yii\db\Migration;

/**
 * Class m200503_124747_add_columns_user_table
 */
class m200503_124747_add_columns_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user', 'description', $this->string()->after('username')->null());
        $this->addColumn('user', 'first_name', $this->string()->after('username'));
        $this->addColumn('user', 'job_name', $this->integer()->after('username'));
        $this->addColumn('user', 'type', $this->integer()->after('username')->defaultValue(User::TYPE_SPECIALIST));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('user', 'description');
        $this->dropColumn('user', 'first_name');
        $this->dropColumn('user', 'job_name');
        $this->dropColumn('user', 'type');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200503_124747_add_columns_user_table cannot be reverted.\n";

        return false;
    }
    */
}
