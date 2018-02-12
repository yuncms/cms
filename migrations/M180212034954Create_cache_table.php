<?php

namespace yuncms\migrations;

use yii\db\Migration;

/**
 * Class M180212034954Create_cache_table
 *
 * @author Tongle Xu <xutongle@gmail.com>
 * @since 3.0
 */
class M180212034954Create_cache_table extends Migration
{

    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%cache}}', [
            'id' => $this->string(128)->notNull(),
            'expire' => $this->integer(),
            'data' => $this->binary(),
            'PRIMARY KEY ([[id]])',
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%cache}}');
    }


    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M180212034954Create_cache_table cannot be reverted.\n";

        return false;
    }
    */
}
