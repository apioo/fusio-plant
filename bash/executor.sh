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
    touch "$outputFile":
    chown www-data: "$outputFile"
    case $type in
      "setup")
        name=$(jq -r ".name" "$command")
        name="${name//[^[:alnum:]]/_}"
        compose=$(jq -r ".compose" "$command")
        nginx=$(jq -r ".nginx" "$command")
        rm "$command"
        mkdir "/docker/$name"
        echo "$compose" > "/docker/$name/docker-compose.yml"
        echo "$nginx" > "/etc/nginx/sites-available/$name"
        ln -s "/etc/nginx/sites-available/$name" "/etc/nginx/sites-enabled/$name"
        service nginx reload
        pushd "/docker/$name" || continue
        docker compose pull
        docker compose up -d >> "$outputFile"
        popd || continue
        ;;
      "remove")
        name=$(jq -r ".name" "$command")
        name="${name//[^[:alnum:]]/_}"
        rm "$command"
        rm "/etc/nginx/sites-enabled/$name"
        rm "/etc/nginx/sites-available/$name"
        service nginx reload
        pushd "/docker/$name" || continue
        docker compose down >> "$outputFile"
        popd || continue
        rm -r "/docker/$name"
        ;;
      "certbot")
        domain=$(printf "%b" "$(jq -r ".domain" "$command")")
        email=$(printf "%b" "$(jq -r ".email" "$command")")
        rm "$command"
        certbot --nginx --non-interactive --agree-tos -m "$email" -d "$domain" >> "$outputFile"
        ;;
      "pull")
        name=$(jq -r ".name" "$command")
        name="${name//[^[:alnum:]]/_}"
        rm "$command"
        pushd "/docker/$name" || continue
        docker compose pull >> "$outputFile"
        popd || continue
        ;;
      "up")
        name=$(jq -r ".name" "$command")
        name="${name//[^[:alnum:]]/_}"
        rm "$command"
        pushd "/docker/$name" || continue
        docker compose up -d >> "$outputFile"
        popd || continue
        ;;
      "down")
        name=$(jq -r ".name" "$command")
        name="${name//[^[:alnum:]]/_}"
        rm "$command"
        pushd "/docker/$name" || continue
        docker compose down >> "$outputFile"
        popd || continue
        ;;
      "logs")
        name=$(jq -r ".name" "$command")
        name="${name//[^[:alnum:]]/_}"
        rm "$command"
        pushd "/docker/$name" || continue
        docker compose logs --no-color --tail=256 >> "$outputFile"
        popd || continue
        ;;
      "ps")
        name=$(jq -r ".name" "$command")
        name="${name//[^[:alnum:]]/_}"
        rm "$command"
        pushd "/docker/$name" || continue
        docker compose ps --format=json >> "$outputFile"
        popd || continue
        ;;
      "stats")
        name=$(jq -r ".name" "$command")
        name="${name//[^[:alnum:]]/_}"
        rm "$command"
        pushd "/docker/$name" || continue
        docker compose stats --no-stream --format=json >> "$outputFile"
        popd || continue
        ;;
      "login")
        username=$(printf "%b" "$(jq -r ".username" "$command")")
        password=$(printf "%b" "$(jq -r ".password" "$command")")
        rm "$command"
        echo "$password" | docker login -u "$username" --password-stdin >> "$outputFile"
        ;;
      *)
        rm "$command"
        ;;
    esac
    mv "$outputFile" "$resultFile"
  done
  sleep 1
done
