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
pipe=/opt/plant/pipe
while true
do
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
        echo "> service nginx reload" > "$pipe"
        service nginx reload > "$pipe"
        echo "Exit code: $?" > "$pipe"
        echo "$backup" > "/etc/cron.daily/backup-$name"
        chmod +x "/etc/cron.daily/backup-$name"
        pushd "/docker/$name"
        echo "> docker compose pull" > "$pipe"
        docker compose pull > "$pipe" 2>&1
        echo "Exit code: $?" > "$pipe"
        echo "> docker compose up -d" > "$pipe"
        docker compose up -d > "$pipe" 2>&1
        echo "Exit code: $?" > "$pipe"
        popd
        ;;
      "project-remove")
        name=$(echo "$command" | jq -r ".name")
        name="${name//[^[:alnum:]]/_}"
        rm "/etc/nginx/sites-enabled/$name"
        rm "/etc/nginx/sites-available/$name"
        rm "/etc/cron.daily/backup-$name"
        echo "> service nginx reload" > "$pipe"
        service nginx reload
        echo "Exit code: $?" > "$pipe"
        pushd "/docker/$name"
        echo "> docker compose down" > "$pipe"
        docker compose down > "$pipe" 2>&1
        echo "Exit code: $?" > "$pipe"
        popd
        rm -r "/docker/$name"
        ;;
      "project-deploy")
        name=$(echo "$command" | jq -r ".name")
        name="${name//[^[:alnum:]]/_}"
        rm "$command"
        pushd "/docker/$name"
        echo "> docker compose pull" > "$pipe"
        docker compose pull > "$pipe" 2>&1
        echo "Exit code: $?" > "$pipe"
        echo "> docker compose up -d" > "$pipe"
        docker compose up -d > "$pipe" 2>&1
        echo "Exit code: $?" > "$pipe"
        popd
        ;;
      "project-down")
        name=$(echo "$command" | jq -r ".name")
        name="${name//[^[:alnum:]]/_}"
        pushd "/docker/$name"
        echo "> docker compose down" > "$pipe"
        docker compose down > "$pipe" 2>&1
        echo "Exit code: $?" > "$pipe"
        popd
        ;;
      "project-logs")
        name=$(echo "$command" | jq -r ".name")
        name="${name//[^[:alnum:]]/_}"
        pushd "/docker/$name"
        docker compose logs --no-color --tail=256 > "$pipe" 2>&1
        popd
        ;;
      "project-ps")
        name=$(echo "$command" | jq -r ".name")
        name="${name//[^[:alnum:]]/_}"
        pushd "/docker/$name"
        docker compose ps --format=json > "$pipe" 2>&1
        popd
        ;;
      "project-pull")
        name=$(echo "$command" | jq -r ".name")
        name="${name//[^[:alnum:]]/_}"
        pushd "/docker/$name"
        echo "> docker compose pull" > "$pipe"
        docker compose pull > "$pipe" 2>&1
        echo "Exit code: $?" > "$pipe"
        popd
        ;;
      "project-stats")
        name=$(echo "$command" | jq -r ".name")
        name="${name//[^[:alnum:]]/_}"
        pushd "/docker/$name"
        docker compose stats --no-stream --format=json > "$pipe" 2>&1
        popd
        ;;
      "project-up")
        name=$(echo "$command" | jq -r ".name")
        name="${name//[^[:alnum:]]/_}"
        pushd "/docker/$name"
        echo "> docker compose up -d" > "$pipe"
        docker compose up -d > "$pipe" 2>&1
        echo "Exit code: $?" > "$pipe"
        popd
        ;;
      "certbot")
        domain=$(printf "%b" "$(echo "$command" | jq -r ".domain")")
        email=$(printf "%b" "$(echo "$command" | jq -r ".email")")
        echo "> certbot --nginx" > "$pipe"
        certbot --nginx --non-interactive --agree-tos -m "$email" -d "$domain" > "$pipe" 2>&1
        echo "Exit code: $?" > "$pipe"
        ;;
      "login")
        domain=$(printf "%b" "$(echo "$command" | jq -r ".domain")")
        username=$(printf "%b" "$(echo "$command" | jq -r ".username")")
        password=$(printf "%b" "$(echo "$command" | jq -r ".password")")
        echo "> docker login" > "$pipe"
        docker login "$domain" -u "$username" -p "$password" > "$pipe" 2>&1
        echo "Exit code: $?" > "$pipe"
        ;;
      "images")
        docker images --format=json > "$pipe" 2>&1
        ;;
      "ps")
        docker ps --format=json > "$pipe" 2>&1
        ;;
      "stats")
        docker stats --no-stream --format=json > "$pipe" 2>&1
        ;;
    esac
    echo "" > "$pipe"
    echo "--PLANT--" > "$pipe"
    echo "" > "$pipe"
  done < $pipe
done
