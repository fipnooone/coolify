<?php

namespace Database\Seeders;

use App\Models\InstanceSettings;
use App\Models\PrivateKey;
use App\Models\Project;
use App\Models\Server;
use App\Models\Team;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        if (InstanceSettings::find(0) == null) {
            InstanceSettings::create([
                'id' => 0
            ]);
        }

        // Add first Team if it doesn't exist
        if (Team::find(0) == null) {
            Team::create([
                'id' => 0,
                'name' => "Root's Team",
                'personal_team' => true,
            ]);
        }

        // Save SSH Keys for the Coolify Host
        $coolify_key_name = "id.root@host.docker.internal";
        $coolify_key = Storage::disk('local')->get("ssh-keys/{$coolify_key_name}");

        if ($coolify_key) {
            $private_key = PrivateKey::find(0);
            if ($private_key == null) {
                PrivateKey::create([
                    'id' => 0,
                    'name' => 'localhost\'s key',
                    'description' => 'The private key for the Coolify host machine (localhost).',
                    'private_key' => $coolify_key,
                    'team_id' => 0,
                ]);
            } else {
                $private_key->private_key = $coolify_key;
                $private_key->save();
            }
        } else {
            echo "No SSH key found for the Coolify host machine (localhost).\n";
            echo "Please generate one and save it in storage/app/ssh-keys/{$coolify_key_name}\n";
            exit(1);
        }

        // Add Coolify host (localhost) as Server if it doesn't exist
        if (Server::find(0) == null) {
            Server::create([
                'id' => 0,
                'name' => "localhost",
                'description' => "This is the local machine",
                'user' => 'root',
                'ip' => "host.docker.internal",
                'team_id' => 0,
                'private_key_id' => 0,
            ]);
        }
    }
}