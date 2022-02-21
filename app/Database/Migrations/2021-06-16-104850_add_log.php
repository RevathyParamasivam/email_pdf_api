<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddOtp extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => [
                'type'           => 'INT',
                'constraint'     => 16,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'message'    => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'user_id'    => [
                'type'       => 'VARCHAR',
                'constraint' => '45',
                'null'       => true,
            ],
            'module'     => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => false,
            ],
            'data_value' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'user_agent' => [
                'type' => 'text',
                'null' => false,
            ],
            'status'     => [
                'type'       => 'ENUM',
                'constraint' => ['1', '0', '2', '3', '4', '5'],
                'default'    => '1',
            ],

            'created_at datetime default current_timestamp',
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('app_logs', true);
    }

    public function down()
    {
        $this->forge->dropTable('otp_verification', true);
    }
}
