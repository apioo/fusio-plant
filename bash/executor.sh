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
input=/opt/plant/input
output=/opt/plant/output
while true; do
  while read -r command; do
    type=$(echo "$command" | jq -r ".type")
    case $type in
      "project-setup")
        name=$(echo "$command" | jq -r ".name")
        name="${name//[^[:alnum:]]/_}"
        compose=$(echo "$command" | jq -r ".compose")
        nginx=$(echo "$command" | jq -r ".nginx")
        backup=$(echo "$command" | jq -r ".backup")
        mkdir "/backup/$name"
        mkdir "/docker/$name"
        echo "$compose" > "/docker/$name/docker-compose.yml"
        echo "$nginx" > "/etc/nginx/sites-available/$name"
        ln -s "/etc/nginx/sites-available/$name" "/etc/nginx/sites-enabled/$name"
        echo "> service nginx reload" > "$output"
        service nginx reload > "$output"
        echo "Exit code: $?" > "$output"
        echo "$backup" > "/etc/cron.daily/backup-$name"
        chmod +x "/etc/cron.daily/backup-$name"
        pushd "/docker/$name"
        echo "> docker compose pull" > "$output"
        docker compose pull > "$output" 2>&1
        echo "Exit code: $?" > "$output"
        echo "> docker compose up -d" > "$output"
        docker compose up -d > "$output" 2>&1
        echo "Exit code: $?" > "$output"
        popd
        ;;
      "project-remove")
        name=$(echo "$command" | jq -r ".name")
        name="${name//[^[:alnum:]]/_}"
        rm "/etc/nginx/sites-enabled/$name"
        rm "/etc/nginx/sites-available/$name"
        rm "/etc/cron.daily/backup-$name"
        echo "> service nginx reload" > "$output"
        service nginx reload
        echo "Exit code: $?" > "$output"
        pushd "/docker/$name"
        echo "> docker compose down" > "$output"
        docker compose down > "$output" 2>&1
        echo "Exit code: $?" > "$output"
        popd
        rm -r "/docker/$name"
        ;;
      "project-deploy")
        name=$(echo "$command" | jq -r ".name")
        name="${name//[^[:alnum:]]/_}"
        rm "$command"
        pushd "/docker/$name"
        echo "> docker compose pull" > "$output"
        docker compose pull > "$output" 2>&1
        echo "Exit code: $?" > "$output"
        echo "> docker compose up -d" > "$output"
        docker compose up -d > "$output" 2>&1
        echo "Exit code: $?" > "$output"
        popd
        ;;
      "project-down")
        name=$(echo "$command" | jq -r ".name")
        name="${name//[^[:alnum:]]/_}"
        pushd "/docker/$name"
        echo "> docker compose down" > "$output"
        docker compose down > "$output" 2>&1
        echo "Exit code: $?" > "$output"
        popd
        ;;
      "project-logs")
        name=$(echo "$command" | jq -r ".name")
        name="${name//[^[:alnum:]]/_}"
        pushd "/docker/$name"
        docker compose logs --no-color --tail=256 > "$output" 2>&1
        popd
        ;;
      "project-ps")
        name=$(echo "$command" | jq -r ".name")
        name="${name//[^[:alnum:]]/_}"
        pushd "/docker/$name"
        docker compose ps --format=json > "$output" 2>&1
        popd
        ;;
      "project-pull")
        name=$(echo "$command" | jq -r ".name")
        name="${name//[^[:alnum:]]/_}"
        pushd "/docker/$name"
        echo "> docker compose pull" > "$output"
        docker compose pull > "$output" 2>&1
        echo "Exit code: $?" > "$output"
        popd
        ;;
      "project-stats")
        name=$(echo "$command" | jq -r ".name")
        name="${name//[^[:alnum:]]/_}"
        pushd "/docker/$name"
        docker compose stats --no-stream --format=json > "$output" 2>&1
        popd
        ;;
      "project-up")
        name=$(echo "$command" | jq -r ".name")
        name="${name//[^[:alnum:]]/_}"
        pushd "/docker/$name"
        echo "> docker compose up -d" > "$output"
        docker compose up -d > "$output" 2>&1
        echo "Exit code: $?" > "$output"
        popd
        ;;
      "certbot")
        domain=$(printf "%b" "$(echo "$command" | jq -r ".domain")")
        email=$(printf "%b" "$(echo "$command" | jq -r ".email")")
        echo "> certbot --nginx" > "$output"
        certbot --nginx --non-interactive --agree-tos -m "$email" -d "$domain" > "$output" 2>&1
        echo "Exit code: $?" > "$output"
        ;;
      "login")
        domain=$(printf "%b" "$(echo "$command" | jq -r ".domain")")
        username=$(printf "%b" "$(echo "$command" | jq -r ".username")")
        password=$(printf "%b" "$(echo "$command" | jq -r ".password")")
        echo "> docker login" > "$output"
        docker login "$domain" -u "$username" -p "$password" > "$output" 2>&1
        echo "Exit code: $?" > "$output"
        ;;
      "images")
        docker images --format=json > "$output" 2>&1
        ;;
      "ps")
        docker ps --format=json > "$output" 2>&1
        ;;
      "stats")
        docker stats --no-stream --format=json > "$output" 2>&1
        ;;
    esac
    echo "" > "$output"
    echo "--PLANT--" > "$output"
    echo "" > "$output"
  done < $input
done
