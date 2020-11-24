<?php

/**
 * Class AddLanguages
 * Creates languages table in database
 *
 * @copyright  2020 Podlibre
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html AGPL3
 * @link       https://castopod.org/
 */

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPodcastsUsers extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'podcast_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'group_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
        ]);
        $this->forge->addPrimaryKey(['user_id', 'podcast_id']);
        $this->forge->addForeignKey('user_id', 'users', 'id');
        $this->forge->addForeignKey('podcast_id', 'podcasts', 'id');
        $this->forge->addForeignKey('group_id', 'auth_groups', 'id');
        $this->forge->createTable('podcasts_users');
    }

    public function down()
    {
        $this->forge->dropTable('podcasts_users');
    }
}