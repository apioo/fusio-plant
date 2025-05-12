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
cd /
echo ""
echo "Fusio-Plant installation"
echo ""
echo "To install Fusio-Plant you need to provide the domain of your server i.e. myserver.com"
echo "Make sure that the DNS A/AAAA record of your domain already points to your server"
echo ""
read -p "Domain: " domain
echo ""
apt-get update
apt-get install nginx zip unzip certbot python3-certbot-nginx ca-certificates curl jq inotify-tools supervisor -y
install -m 0755 -d /etc/apt/keyrings
curl -fsSL https://download.docker.com/linux/ubuntu/gpg -o /etc/apt/keyrings/docker.asc
chmod a+r /etc/apt/keyrings/docker.asc
echo "deb [arch=$(dpkg --print-architecture) signed-by=/etc/apt/keyrings/docker.asc] https://download.docker.com/linux/ubuntu $(. /etc/os-release && echo "${UBUNTU_CODENAME:-$VERSION_CODENAME}") stable" | tee /etc/apt/sources.list.d/docker.list > /dev/null
apt-get update
apt-get install docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin -y
mkdir /docker
mkdir /cache
chown -R www-data: /cache
mkdir /opt/plant
mkdir /opt/plant/input
mkdir /opt/plant/output
chown -R www-data: /opt/plant/input
chown -R www-data: /opt/plant/output
curl -fsSL https://raw.githubusercontent.com/apioo/fusio-plant/refs/heads/main/bash/executor.sh -o /opt/plant/executor
chmod +x /opt/plant/executor
ln -s /opt/plant/executor /usr/bin/plant-executor
cat > /etc/supervisor/conf.d/plant.conf <<EOF
[program:plant]
command=/usr/bin/plant-executor
user=root
process_name=%(program_name)s_%(process_num)s
numprocs=1
directory=/docker
autostart=true
autorestart=true
EOF
curl -fsSL https://raw.githubusercontent.com/apioo/fusio-plant/refs/heads/main/bash/prune.sh -o /etc/cron.daily/docker-prune
chmod +x /etc/cron.daily/docker-prune
project_key=$(tr -dc 'A-Za-z0-9' < /dev/urandom | head -c 40)
mysql_password=$(tr -dc 'A-Za-z0-9!?%=' < /dev/urandom | head -c 20)
backend_username=$(tr -dc 'A-Za-z0-9' < /dev/urandom | head -c 8)
backend_password=$(tr -dc 'A-Za-z0-9!?%=' < /dev/urandom | head -c 16)
rm /etc/nginx/sites-enabled/default
cat > /etc/nginx/sites-available/plant <<EOF
server {
  server_name $domain;
  location / {
    proxy_pass http://127.0.0.1:8900;
  }
}

EOF
ln -s /etc/nginx/sites-available/plant /etc/nginx/sites-enabled/plant
service nginx reload
mkdir /docker/plant
cat > /docker/plant/docker-compose.yml <<EOF
version: '3'
services:
  plant:
    image: fusio/plant
    restart: always
    environment:
      FUSIO_TENANT_ID: ""
      FUSIO_PROJECT_KEY: "$project_key"
      FUSIO_URL: "https://$domain"
      FUSIO_APPS_URL: "https://$domain/apps"
      FUSIO_ENV: "prod"
      FUSIO_DEBUG: "false"
      FUSIO_CONNECTION: "pdo-mysql://fusio:$mysql_password@mysql-fusio/fusio"
      FUSIO_BACKEND_USER: "$backend_username"
      FUSIO_BACKEND_EMAIL: "info@$domain"
      FUSIO_BACKEND_PW: "$backend_password"
      FUSIO_MAIL_SENDER: "info@$domain"
      FUSIO_TRUSTED_IP_HEADER: "X-Forwarded-For"
    volumes:
      - /opt/plant/input:/var/www/html/fusio/input
      - /opt/plant/output:/var/www/html/fusio/output
    links:
      - mysql-fusio
    ports:
      - "127.0.0.1:8900:80"

  mysql-fusio:
    image: mysql:8.0
    restart: always
    environment:
      MYSQL_RANDOM_ROOT_PASSWORD: "1"
      MYSQL_USER: "fusio"
      MYSQL_PASSWORD: "$mysql_password"
      MYSQL_DATABASE: "fusio"
    volumes:
      - ./db:/var/lib/mysql
EOF
docker compose up -d
certbot --nginx --non-interactive --agree-tos -m "info@$domain" -d "$domain"
if [ $? -ne 0 ]; then
    echo ""
    echo "NOTICE: We could not obtain an SSL certificate for your domain $domain"
    echo "You need to point the DNS A/AAAA Record of your domain to your server"
    echo "After this you can obtain the certificate by running the following command"
    echo "> certbot --nginx --agree-tos -m \"info@$domain\" -d \"$domain\""
    echo ""
fi
supervisord
echo ""
echo "Fusio Plant successfully installed"
echo ""
echo "The backend app is available at:"
echo "API: https://$domain"
echo "App: https://$domain/apps/plant"
echo ""
echo "You can login at the backend with the following credentials:"
echo "Username: $backend_username"
echo "Password: $backend_password"
echo ""
