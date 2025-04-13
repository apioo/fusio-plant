#!/bin/sh
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
cd /
apt-get update
apt-get install nginx zip unzip certbot python3-certbot-nginx ca-certificates curl jq inotify-tools supervisor
install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg -o /etc/apt/keyrings/docker.asc
chmod a+r /etc/apt/keyrings/docker.asc
echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/ubuntu $(. /etc/os-release && echo "${UBUNTU_CODENAME:-$VERSION_CODENAME}") stable" | tee /etc/apt/sources.list.d/docker.list > /dev/null
apt-get update
apt-get install docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
mkdir /docker
mkdir /cache
chown -R www-data: /cache
mkdir /opt/plant
mkdir /opt/plant/input
mkdir /opt/plant/output
chown -R www-data: /opt/plant/input
chown -R www-data: /opt/plant/output
cat > /opt/plant/executor <<- "EOF"
#!/bin/sh
while inotifywait -q -e modify /opt/input
do
  for command in /opt/input/*.cmd; do
    type = $(jq ".type" $command)
    output = $(basename -- "$command")
    outputFile = "/opt/output/$output"
    echo "" > $outputFile
    chown www-data: $outputFile
    if [[ "$type" == "setup" ]]; then
      domain = $(jq ".domain" $command)
      compose = $(jq ".compose" $command)
      nginx = $(jq ".nginx" $command)
      rm $command
      mkdir "/docker/$domain"
      echo "$compose" > "/docker/$domain/docker-compose.yml"
      echo "$nginx" > "/etc/nginx/sites-available/$domain"
      ln -s "/etc/nginx/sites-available/$domain" "/etc/nginx/sites-enabled/$domain"
      service nginx reload
      pushd "/docker/$domain"
      docker compose up -d > $outputFile
      popd
    elif [[ "$type" == "remove" ]]; then
      domain = $(jq ".domain" $command)
      rm $command
      rm "/etc/nginx/sites-enabled/$domain"
      rm "/etc/nginx/sites-available/$domain"
      service nginx reload
      pushd "/docker/$domain"
      docker compose down > $outputFile
      popd
      rm -r "/docker/$domain"
    elif [[ "$type" == "certbot" ]]; then
      domain = $(jq ".domain" $command)
      rm $command
      certbot --nginx -d $domain > $outputFile
    elif [[ "$type" == "pull" ]]; then
      domain = $(jq ".domain" $command)
      rm $command
      pushd "/docker/$domain"
      docker compose pull > $outputFile
      popd
    elif [[ "$type" == "up" ]]; then
      domain = $(jq ".domain" $command)
      rm $command
      pushd "/docker/$domain"
      docker compose up -d > $outputFile
      popd
    elif [[ "$type" == "down" ]]; then
      domain = $(jq ".domain" $command)
      rm $command
      pushd "/docker/$domain"
      docker compose down > $outputFile
      popd
    elif [[ "$type" == "logs" ]]; then
      domain = $(jq ".domain" $command)
      rm $command
      pushd "/docker/$domain"
      docker compose logs --no-color --tail=256 > $outputFile
      popd
    elif [[ "$type" == "ps" ]]; then
      domain = $(jq ".domain" $command)
      rm $command
      pushd "/docker/$domain"
      docker compose ps --format=json > $outputFile
      popd
    elif [[ "$type" == "stats" ]]; then
      domain = $(jq ".domain" $command)
      rm $command
      pushd "/docker/$domain"
      docker compose stats --no-stream --format=json > $outputFile
      popd
    elif [[ "$type" == "login" ]]; then
      username = $(jq ".username" $command)
      password = $(jq ".password" $command)
      rm $command
      echo $password | docker login -u $username --password-stdin > $outputFile
    fi
  done
  sleep 1
done
EOF
chmod +x /opt/plant/executor
ln -s /opt/plant/executor /usr/bin/plant-executor
cat > /etc/supervisor/conf.d/plant.conf <<- "EOF"
[program:plant]
command=/usr/bin/plant-executor
user=root
process_name=%(program_name)s_%(process_num)s
numprocs=1
directory=/docker
autostart=true
autorestart=true
EOF

hostname=$(hostname)
random_id=$(uuidgen)
panel_app_hostname="plant.$random_id.$hostname"
panel_api_hostname="api.$random_id.$hostname"
project_key=$(tr -dc 'A-Za-z0-9' < /dev/urandom | head -c 40)
mysql_password=$(tr -dc 'A-Za-z0-9!?%=' < /dev/urandom | head -c 20)
backend_password=$(tr -dc 'A-Za-z0-9!?%=' < /dev/urandom | head -c 16)
rm /etc/nginx/sites-enabled/default
cat > /etc/nginx/sites-available/plant <<- "EOF"
server {
  server_name $panel_app_hostname;
  location / {
    proxy_pass http://127.0.0.1:8900;
  }
}

server {
  server_name $panel_api_hostname;
  location / {
    proxy_pass http://127.0.0.1:8901;
  }
}

EOF
ln -s /etc/nginx/sites-available/plant /etc/nginx/sites-enabled/plant
service nginx reload


cat > /etc/nginx/sites-available/plant <<- "EOF"
version: '3'
services:
  frontend:
    image: ghcr.io/apioo/fusio-plant-frontend:main
    restart: always
    ports:
      - "127.0.0.1:8900:80"

  backend:
    image: ghcr.io/apioo/fusio-plant-backend:main
    restart: always
    environment:
      FUSIO_TENANT_ID: ""
      FUSIO_PROJECT_KEY: "$project_key"
      FUSIO_URL: "https://$panel_api_hostname"
      FUSIO_APPS_URL: "https://$panel_api_hostname/apps"
      FUSIO_ENV: "prod"
      FUSIO_DEBUG: "false"
      FUSIO_CONNECTION: "pdo-mysql://fusio:$mysql_password@mysql-fusio/fusio"
      FUSIO_BACKEND_USER: "fusio"
      FUSIO_BACKEND_EMAIL: "christoph.kappestein@gmail.com"
      FUSIO_BACKEND_PW: "$backend_password"
      FUSIO_MAIL_SENDER: "info@$hostname"
    volumes:
      - /opt/plant/input:/var/www/html/fusio/input
      - /opt/plant/output:/var/www/html/fusio/output
    links:
      - mysql-fusio
    ports:
      - "127.0.0.1:8901:80"

  mysql-fusio:
    image: mysql:8.0
    restart: always
    environment:
      MYSQL_ROOT_PASSWORD: "$mysql_password"
      MYSQL_USER: "fusio"
      MYSQL_PASSWORD: "$mysql_password"
      MYSQL_DATABASE: "fusio"
    volumes:
      - ./db:/var/lib/mysql
EOF

supervisord

echo "Fusio Plant successfully installed"
echo "The app is available at:"
echo "App: $panel_app_hostname"
echo "API: $panel_api_hostname"
echo ""
echo "In case you want to use a different hostname simply adjust the following file:"
echo "/etc/nginx/sites-available/plant"
echo ""
