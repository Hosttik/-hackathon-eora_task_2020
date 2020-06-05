<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%adverts}}`.
 */
class m200504_050608_create_adverts_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        $this->createTable('{{%adverts}}', [
            'id' => $this->primaryKey(),
            'spec_user_id' => $this->integer()->notNull(),
            'title' => $this->string()->notNull(),
            'description' => $this->string()->notNull(),
            'cost' => $this->integer()->notNull(),
            'date_created' => $this->integer()->notNull(),
            'date_updated' => $this->integer()->notNull(),
        ], $tableOptions);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%adverts}}');
    }
}
