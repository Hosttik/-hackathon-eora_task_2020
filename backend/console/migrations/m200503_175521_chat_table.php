<?php

use yii\db\Migration;

/**
 * Class m200503_175521_chat_table
 */
class m200503_175521_chat_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';

        $this->createTable('chat', [
            'id' => $this->primaryKey(),
            'from_user_id' => $this->integer()->notNull(),
            'to_user_id' => $this->integer()->notNull(),
            'message' => $this->string()->notNull(),
            'date_created' => $this->integer()->notNull(),
            'date_updated' => $this->integer()->notNull(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200503_175521_chat_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200503_175521_chat_table cannot be reverted.\n";

        return false;
    }
    */
}
