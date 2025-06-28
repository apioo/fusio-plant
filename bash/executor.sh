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

execute_command () {
  type=$(echo "$1" | jq -r ".type")
  tempFile=/opt/plant/temp
  case $type in
    "project-setup")
      name=$(echo "$1" | jq -r ".name")
      name="${name//[^[:alnum:]]/_}"
      compose=$(echo "$1" | jq -r ".compose")
      nginx=$(echo "$1" | jq -r ".nginx")
      backup=$(echo "$1" | jq -r ".backup")
      mkdir "/backup/$name"
      mkdir "/docker/$name"
      echo "$compose" > "/docker/$name/docker-compose.yml"
      echo "$nginx" > "/etc/nginx/sites-available/$name"
      ln -s "/etc/nginx/sites-available/$name" "/etc/nginx/sites-enabled/$name"
      echo "> service nginx reload" > "$tempFile"
      service nginx reload > "$tempFile"
      echo "Exit code: $?" > "$tempFile"
      echo "$backup" > "/etc/cron.daily/backup-$name"
      chmod +x "/etc/cron.daily/backup-$name"
      pushd "/docker/$name"
      echo "> docker compose pull" > "$tempFile"
      docker compose pull > "$tempFile" 2>&1
      echo "Exit code: $?" > "$tempFile"
      echo "> docker compose up -d" > "$tempFile"
      docker compose up -d > "$tempFile" 2>&1
      echo "Exit code: $?" > "$tempFile"
      popd
      ;;
    "project-remove")
      name=$(echo "$1" | jq -r ".name")
      name="${name//[^[:alnum:]]/_}"
      rm "/etc/nginx/sites-enabled/$name"
      rm "/etc/nginx/sites-available/$name"
      rm "/etc/cron.daily/backup-$name"
      echo "> service nginx reload" > "$tempFile"
      service nginx reload
      echo "Exit code: $?" > "$tempFile"
      pushd "/docker/$name"
      echo "> docker compose down" > "$tempFile"
      docker compose down > "$tempFile" 2>&1
      echo "Exit code: $?" > "$tempFile"
      popd
      rm -r "/docker/$name"
      ;;
    "project-deploy")
      name=$(echo "$1" | jq -r ".name")
      name="${name//[^[:alnum:]]/_}"
      pushd "/docker/$name"
      echo "> docker compose pull" > "$tempFile"
      docker compose pull > "$tempFile" 2>&1
      echo "Exit code: $?" > "$tempFile"
      echo "> docker compose up -d" > "$tempFile"
      docker compose up -d > "$tempFile" 2>&1
      echo "Exit code: $?" > "$tempFile"
      popd
      ;;
    "project-down")
      name=$(echo "$1" | jq -r ".name")
      name="${name//[^[:alnum:]]/_}"
      pushd "/docker/$name"
      echo "> docker compose down" > "$tempFile"
      docker compose down > "$tempFile" 2>&1
      echo "Exit code: $?" > "$tempFile"
      popd
      ;;
    "project-logs")
      name=$(echo "$1" | jq -r ".name")
      name="${name//[^[:alnum:]]/_}"
      pushd "/docker/$name"
      docker compose logs --no-color --tail=256 > "$tempFile" 2>&1
      popd
      ;;
    "project-ps")
      name=$(echo "$1" | jq -r ".name")
      name="${name//[^[:alnum:]]/_}"
      pushd "/docker/$name"
      docker compose ps --format=json > "$tempFile" 2>&1
      popd
      ;;
    "project-pull")
      name=$(echo "$1" | jq -r ".name")
      name="${name//[^[:alnum:]]/_}"
      pushd "/docker/$name"
      echo "> docker compose pull" > "$tempFile"
      docker compose pull > "$tempFile" 2>&1
      echo "Exit code: $?" > "$tempFile"
      popd
      ;;
    "project-stats")
      name=$(echo "$1" | jq -r ".name")
      name="${name//[^[:alnum:]]/_}"
      pushd "/docker/$name"
      docker compose stats --no-stream --format=json > "$tempFile" 2>&1
      popd
      ;;
    "project-up")
      name=$(echo "$1" | jq -r ".name")
      name="${name//[^[:alnum:]]/_}"
      pushd "/docker/$name"
      echo "> docker compose up -d" > "$tempFile"
      docker compose up -d > "$tempFile" 2>&1
      echo "Exit code: $?" > "$tempFile"
      popd
      ;;
    "certbot")
      domain=$(printf "%b" "$(echo "$1" | jq -r ".domain")")
      email=$(printf "%b" "$(echo "$1" | jq -r ".email")")
      echo "> certbot --nginx" > "$tempFile"
      certbot --nginx --non-interactive --agree-tos -m "$email" -d "$domain" > "$tempFile" 2>&1
      echo "Exit code: $?" > "$tempFile"
      ;;
    "login")
      domain=$(printf "%b" "$(echo "$1" | jq -r ".domain")")
      username=$(printf "%b" "$(echo "$1" | jq -r ".username")")
      password=$(printf "%b" "$(echo "$1" | jq -r ".password")")
      echo "> docker login" > "$tempFile"
      docker login "$domain" -u "$username" -p "$password" > "$tempFile" 2>&1
      echo "Exit code: $?" > "$tempFile"
      ;;
    "images")
      docker images --format=json > "$tempFile" 2>&1
      ;;
    "ps")
      docker ps --format=json > "$tempFile" 2>&1
      ;;
    "stats")
      docker stats --no-stream --format=json > "$tempFile" 2>&1
      ;;
  esac
  echo "" > "$tempFile"
  echo "--PLANT--" > "$tempFile"
  echo "" > "$tempFile"
  cat $tempFile > $output
}

while read -r line; do execute_command "$line"; done < $input
