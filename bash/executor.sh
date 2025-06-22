#!/bin/bash
# This file is part of the Fusio Plant project (https://fusio-project.org/product/plant).
# Fusio Plant is a server control panel to easily self-host apps on your server.
#
# Copyright (c) Christoph Kappestein <christoph.kappestein@gmail.com>
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU Affero General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU Affero General Public License for more details.
#
# You should have received a copy of the GNU Affero General Public License
# along with this program.  If not, see <https://www.gnu.org/licenses/>.
while inotifywait -q -e modify /opt/plant/input
do
  for command in /opt/plant/input/*.cmd; do
    type=$(jq -r ".type" "$command")
    output=$(basename -- "$command")
    outputFile="/opt/plant/output/$output.lock"
    resultFile="/opt/plant/output/$output"
    touch "$outputFile"
    chown www-data: "$outputFile"
    case $type in
      "project-setup")
        name=$(jq -r ".name" "$command")
        name="${name//[^[:alnum:]]/_}"
        compose=$(jq -r ".compose" "$command")
        nginx=$(jq -r ".nginx" "$command")
        backup=$(jq -r ".backup" "$command")
        rm "$command"
        mkdir "/backup/$name"
        mkdir "/docker/$name"
        echo "$compose" > "/docker/$name/docker-compose.yml"
        echo "$nginx" > "/etc/nginx/sites-available/$name"
        ln -s "/etc/nginx/sites-available/$name" "/etc/nginx/sites-enabled/$name"
        echo "> service nginx reload" >> "$outputFile"
        service nginx reload
        echo "Exit code: $?" >> "$outputFile"
        echo "$backup" > "/etc/cron.daily/backup-$name"
        chmod +x "/etc/cron.daily/backup-$name"
        pushd "/docker/$name" || continue
        echo "> docker compose pull" >> "$outputFile"
        docker compose pull >> "$outputFile" 2>&1
        echo "Exit code: $?" >> "$outputFile"
        echo "> docker compose up -d" >> "$outputFile"
        docker compose up -d >> "$outputFile" 2>&1
        echo "Exit code: $?" >> "$outputFile"
        popd || continue
        ;;
      "project-remove")
        name=$(jq -r ".name" "$command")
        name="${name//[^[:alnum:]]/_}"
        rm "$command"
        rm "/etc/nginx/sites-enabled/$name"
        rm "/etc/nginx/sites-available/$name"
        rm "/etc/cron.daily/backup-$name"
        echo "> service nginx reload" >> "$outputFile"
        service nginx reload
        echo "Exit code: $?" >> "$outputFile"
        pushd "/docker/$name" || continue
        echo "> docker compose down" >> "$outputFile"
        docker compose down >> "$outputFile" 2>&1
        echo "Exit code: $?" >> "$outputFile"
        popd || continue
        rm -r "/docker/$name"
        ;;
      "project-deploy")
        name=$(jq -r ".name" "$command")
        name="${name//[^[:alnum:]]/_}"
        rm "$command"
        pushd "/docker/$name" || continue
        echo "> docker compose pull" >> "$outputFile"
        docker compose pull >> "$outputFile" 2>&1
        echo "Exit code: $?" >> "$outputFile"
        echo "> docker compose up -d" >> "$outputFile"
        docker compose up -d >> "$outputFile" 2>&1
        echo "Exit code: $?" >> "$outputFile"
        popd || continue
        ;;
      "project-down")
        name=$(jq -r ".name" "$command")
        name="${name//[^[:alnum:]]/_}"
        rm "$command"
        pushd "/docker/$name" || continue
        echo "> docker compose down" >> "$outputFile"
        docker compose down >> "$outputFile" 2>&1
        echo "Exit code: $?" >> "$outputFile"
        popd || continue
        ;;
      "project-logs")
        name=$(jq -r ".name" "$command")
        name="${name//[^[:alnum:]]/_}"
        rm "$command"
        pushd "/docker/$name" || continue
        docker compose logs --no-color --tail=256 >> "$outputFile" 2>&1
        popd || continue
        ;;
      "project-ps")
        name=$(jq -r ".name" "$command")
        name="${name//[^[:alnum:]]/_}"
        rm "$command"
        pushd "/docker/$name" || continue
        docker compose ps --format=json >> "$outputFile" 2>&1
        popd || continue
        ;;
      "project-pull")
        name=$(jq -r ".name" "$command")
        name="${name//[^[:alnum:]]/_}"
        rm "$command"
        pushd "/docker/$name" || continue
        echo "> docker compose pull" >> "$outputFile"
        docker compose pull >> "$outputFile" 2>&1
        echo "Exit code: $?" >> "$outputFile"
        popd || continue
        ;;
      "project-stats")
        name=$(jq -r ".name" "$command")
        name="${name//[^[:alnum:]]/_}"
        rm "$command"
        pushd "/docker/$name" || continue
        docker compose stats --no-stream --format=json >> "$outputFile" 2>&1
        popd || continue
        ;;
      "project-up")
        name=$(jq -r ".name" "$command")
        name="${name//[^[:alnum:]]/_}"
        rm "$command"
        pushd "/docker/$name" || continue
        echo "> docker compose up -d" >> "$outputFile"
        docker compose up -d >> "$outputFile" 2>&1
        echo "Exit code: $?" >> "$outputFile"
        popd || continue
        ;;
      "certbot")
        domain=$(printf "%b" "$(jq -r ".domain" "$command")")
        email=$(printf "%b" "$(jq -r ".email" "$command")")
        rm "$command"
        echo "> certbot --nginx" >> "$outputFile"
        certbot --nginx --non-interactive --agree-tos -m "$email" -d "$domain" >> "$outputFile" 2>&1
        echo "Exit code: $?" >> "$outputFile"
        ;;
      "login")
        domain=$(printf "%b" "$(jq -r ".domain" "$command")")
        username=$(printf "%b" "$(jq -r ".username" "$command")")
        password=$(printf "%b" "$(jq -r ".password" "$command")")
        rm "$command"
        echo "> docker login" >> "$outputFile"
        docker login "$domain" -u "$username" -p "$password" >> "$outputFile" 2>&1
        echo "Exit code: $?" >> "$outputFile"
        ;;
      "images")
        rm "$command"
        docker images --format=json >> "$outputFile" 2>&1
        ;;
      "ps")
        rm "$command"
        docker ps --format=json >> "$outputFile" 2>&1
        ;;
      "stats")
        rm "$command"
        docker stats --no-stream --format=json >> "$outputFile" 2>&1
        ;;
      *)
        rm "$command"
        ;;
    esac
    mv "$outputFile" "$resultFile"
  done
  sleep 1
done
