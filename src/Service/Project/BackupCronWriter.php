<?php
/*
 * This file is part of the Fusio Plant project (https://fusio-project.org/product/plant).
 * Fusio Plant is a server control panel to easily self-host apps on your server.
 *
 * Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace App\Service\Project;

use App\Model;

readonly class BackupCronWriter
{
    public function write(int $id, Model\Project $project): string
    {
        $output = [];
        $output[] = '#!/bin/bash';
        foreach ($project->getApps() as $app) {
            if (str_starts_with($app->getImage(), 'mysql')) {
                continue;
            }

            $output[] = $this->writeCronForMysql($id, $app, $project);
        }

        return implode("\n", $output) . "\n";
    }

    private function writeCronForMysql(int $id, Model\ProjectApp $app, Model\Project $project): string
    {
        $appId = $id . '_' . $app->getName();
        $user = escapeshellarg($app->getEnvironment()->get('MYSQL_USER'));
        $password = escapeshellarg($app->getEnvironment()->get('MYSQL_PASSWORD'));
        $database = escapeshellarg($app->getEnvironment()->get('MYSQL_DATABASE'));

        $config = [];
        $config[] = 'backup_file="/backup/' . $project->getName() . '/' . $app->getName() . '-$(date +\'%Y-%m-%d\')"';
        $config[] = 'docker exec ' . $appId . ' mysqldump --user=' . $user . ' --password=' . $password . ' ' . $database . ' > "$backup_file.sql"';
        $config[] = 'zip "$backup_file.sql.zip" "$backup_file.sql"';
        $config[] = 'rm "$backup_file.sql"';
        $config[] = '';

        return implode("\n", $config) . "\n";
    }
}
