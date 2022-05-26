<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSubmissions extends Migration
{
    public function up()
    {
        $fields = [
            'directive'   => ['type' => 'varchar', 'constraint' => 63, 'null' => true],
            'subreddit'   => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'kind'        => ['type' => 'varchar', 'constraint' => 15, 'null' => true],
            'name'        => ['type' => 'varchar', 'constraint' => 15, 'null' => true],
            'author'      => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'url'         => ['type' => 'varchar', 'constraint' => 63, 'null' => true],
            'thumbnail'   => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'title'       => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'body'        => ['type' => 'text', 'null' => true],
            'html'        => ['type' => 'text', 'null' => true],
            'match'       => ['type' => 'varchar', 'constraint' => 63, 'null' => true],
            'excerpt'     => ['type' => 'varchar', 'constraint' => 255, 'null' => true],
            'executed_at' => ['type' => 'datetime', 'null' => true],
            'created_at'  => ['type' => 'datetime', 'null' => true],
            'updated_at'  => ['type' => 'datetime', 'null' => true],
        ];

        $this->forge->addField('id');
        $this->forge->addField($fields);

        $this->forge->addKey('name');
        $this->forge->addKey('created_at');
        $this->forge->addKey('executed_at');

        $this->forge->createTable('submissions');
    }

    //--------------------------------------------------------------------

    public function down()
    {
        $this->forge->dropTable('submissions');
    }
}
