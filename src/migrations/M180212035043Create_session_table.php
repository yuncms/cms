<?php

namespace yuncms\migrations;

use yii\db\Migration;

class M180212035043Create_session_table extends Migration
{

    public function safeUp()
    {
        $dataType = $this->binary();
        $tableOptions = null;

        switch ($this->db->driverName) {
            case 'mysql':
                // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
                $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
                break;
            case 'sqlsrv':
            case 'mssql':
            case 'dblib':
                $dataType = $this->text();
                break;
        }

        $this->createTable('{{%session}}', [
            'id' => $this->string()->notNull(),
            'expire' => $this->integer(),
            'data' => $dataType,
            'PRIMARY KEY ([[id]])',
        ], $tableOptions);
    }

    public function safeDown()
    {
        $this->dropTable('{{%session}}');
    }


    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "M180212035043Create_session_table cannot be reverted.\n";

        return false;
    }
    */
}
