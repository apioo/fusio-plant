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
    type=$(jq ".type" "$command")
    output=$(basename -- "$command")
    outputFile="/opt/plant/output/$output"
    touch "$outputFile"
    chown www-data: "$outputFile"
    case $type in
      setup)
        name=$(jq ".name" "$command")
        name="${name//[^[:alnum:]]/_}"
        compose=$(jq ".compose" "$command")
        nginx=$(jq ".nginx" "$command")
        rm "$command"
        mkdir "/docker/$name"
        echo "$compose" > "/docker/$name/docker-compose.yml"
        echo "$nginx" > "/etc/nginx/sites-available/$name"
        ln -s "/etc/nginx/sites-available/$name" "/etc/nginx/sites-enabled/$name"
        service nginx reload
        pushd "/docker/$name"
        docker compose up -d >> "$outputFile"
        popd
        ;;
      remove)
        name=$(jq ".name" "$command")
        name="${name//[^[:alnum:]]/_}"
        rm "$command"
        rm "/etc/nginx/sites-enabled/$name"
        rm "/etc/nginx/sites-available/$name"
        service nginx reload
        pushd "/docker/$name"
        docker compose down >> "$outputFile"
        popd
        rm -r "/docker/$name"
        ;;
      certbot)
        domain=$(printf "%b" "$(jq ".domain" "$command")")
        email=$(printf "%b" "$(jq ".email" "$command")")
        rm "$command"
        certbot --nginx --non-interactive --agree-tos -m "$email" -d "$domain" >> "$outputFile"
        ;;
      pull)
        name=$(jq ".name" "$command")
        name="${name//[^[:alnum:]]/_}"
        rm "$command"
        pushd "/docker/$name"
        docker compose pull >> "$outputFile"
        popd
        ;;
      up)
        name=$(jq ".name" "$command")
        name="${name//[^[:alnum:]]/_}"
        rm "$command"
        pushd "/docker/$name"
        docker compose up -d >> "$outputFile"
        popd
        ;;
      down)
        name=$(jq ".name" "$command")
        name="${name//[^[:alnum:]]/_}"
        rm "$command"
        pushd "/docker/$name"
        docker compose down >> "$outputFile"
        popd
        ;;
      logs)
        name=$(jq ".name" "$command")
        name="${name//[^[:alnum:]]/_}"
        rm "$command"
        pushd "/docker/$name"
        docker compose logs --no-color --tail=256 >> "$outputFile"
        popd
        ;;
      ps)
        name=$(jq ".name" "$command")
        name="${name//[^[:alnum:]]/_}"
        rm "$command"
        pushd "/docker/$name"
        docker compose ps --format=json >> "$outputFile"
        popd
        ;;
      stats)
        name=$(jq ".name" "$command")
        name="${name//[^[:alnum:]]/_}"
        rm "$command"
        pushd "/docker/$name"
        docker compose stats --no-stream --format=json >> "$outputFile"
        popd
        ;;
      login)
        username=$(printf "%b" "$(jq ".username" "$command")")
        password=$(printf "%b" "$(jq ".password" "$command")")
        rm "$command"
        echo "$password" | docker login -u "$username" --password-stdin >> "$outputFile"
        ;;
      *)
        rm "$command"
        ;;
    esac
    echo "COMPLETE" >> "$outputFile"
  done
  sleep 1
done
