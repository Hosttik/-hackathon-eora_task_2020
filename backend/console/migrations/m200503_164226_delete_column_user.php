<?php

use yii\db\Migration;

/**
 * Class m200503_164226_delete_column_user
 */
class m200503_164226_delete_column_user extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropColumn('user', 'username');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200503_164226_delete_column_user cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200503_164226_delete_column_user cannot be reverted.\n";

        return false;
    }
    */
}
