#!/bin/bash
# This file is part of the Fusio Plant project (https://plant.fusio-project.org).
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
while inotifywait -q -e modify /opt/input
do
  for command in /opt/input/*.cmd; do
    type=$(jq ".type" "$command")
    output=$(basename -- "$command")
    outputFile="/opt/output/$output"
    echo "" > "$outputFile"
    chown www-data: "$outputFile"
    if [[ "$type" == "setup" ]]; then
      name=$(jq ".name" "$command")
      compose=$(jq ".compose" "$command")
      nginx=$(jq ".nginx" "$command")
      rm "$command"
      mkdir "/docker/$name"
      echo "$compose" > "/docker/$name/docker-compose.yml"
      echo "$nginx" > "/etc/nginx/sites-available/$name"
      ln -s "/etc/nginx/sites-available/$name" "/etc/nginx/sites-enabled/$name"
      service nginx reload
      pushd "/docker/$name"
      docker compose up -d > "$outputFile"
      popd
    elif [[ "$type" == "remove" ]]; then
      name=$(jq ".name" "$command")
      rm "$command"
      rm "/etc/nginx/sites-enabled/$name"
      rm "/etc/nginx/sites-available/$name"
      service nginx reload
      pushd "/docker/$name"
      docker compose down > "$outputFile"
      popd
      rm -r "/docker/$name"
    elif [[ "$type" == "certbot" ]]; then
      domain=$(jq ".domain" "$command")
      email=$(jq ".email" "$command")
      rm "$command"
      certbot --nginx --non-interactive --agree-tos -m "$email" -d "$domain" > "$outputFile"
    elif [[ "$type" == "pull" ]]; then
      name=$(jq ".name" "$command")
      rm "$command"
      pushd "/docker/$name"
      docker compose pull > "$outputFile"
      popd
    elif [[ "$type" == "up" ]]; then
      name=$(jq ".name" "$command")
      rm "$command"
      pushd "/docker/$name"
      docker compose up -d > "$outputFile"
      popd
    elif [[ "$type" == "down" ]]; then
      name=$(jq ".name" "$command")
      rm "$command"
      pushd "/docker/$name"
      docker compose down > "$outputFile"
      popd
    elif [[ "$type" == "logs" ]]; then
      name=$(jq ".name" "$command")
      rm "$command"
      pushd "/docker/$name"
      docker compose logs --no-color --tail=256 > "$outputFile"
      popd
    elif [[ "$type" == "ps" ]]; then
      name=$(jq ".name" "$command")
      rm "$command"
      pushd "/docker/$name"
      docker compose ps --format=json > "$outputFile"
      popd
    elif [[ "$type" == "stats" ]]; then
      name=$(jq ".name" "$command")
      rm "$command"
      pushd "/docker/$name"
      docker compose stats --no-stream --format=json > "$outputFile"
      popd
    elif [[ "$type" == "login" ]]; then
      username=$(jq ".username" "$command")
      password=$(jq ".password" "$command")
      rm "$command"
      echo "$password" | docker login -u "$username" --password-stdin > "$outputFile"
    fi
  done
  sleep 1
done
