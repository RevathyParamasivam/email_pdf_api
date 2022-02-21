<?php
  
namespace App\Database\Migrations;  
use CodeIgniter\Database\Migration;

class AddOtp extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'mobile_number' => [
                'type' => 'VARCHAR',
                'constraint' => '45',
                'null' => false
            ],
            'phone_number' => [
                'type' => 'VARCHAR',
                'constraint' => '45',
                'null' => false
            ],
            'otp' => [
                'type' => 'INT',
                'constraint' => '10',
                'null' => false
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['1', '0'],
                'default' => '1',
            ],
            'is_verified' => [
                'type' => 'ENUM',
                'constraint' => ['1', '0'],
                'default' => '0',
            ],
        'created_at datetime default current_timestamp',
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('phone_number');
        $this->forge->addKey('mobile_number');
        $this->forge->addKey('otp');
        //$this->forge->dropTable('otp_verification',true);
        $this->forge->createTable('otp_verification',true);
    }

    public function down()
    {
        $this->forge->dropTable('otp_verification',true);
    }
}