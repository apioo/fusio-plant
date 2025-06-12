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
mkdir /backup
mkdir /cache
mkdir /docker
chown -R www-data: /cache
mkdir /opt/plant
mkdir /opt/plant/input
mkdir /opt/plant/output
chown -R www-data: /opt/plant/input
chown -R www-data: /opt/plant/output
mkdir /opt/plant/www
cat > /opt/plant/www/index.html <<EOF
<!DOCTYPE>
<html lang="en">
<head>
  <title>Fusio - Plant</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
</head>
<body>

<div class="container" style="margin-top:64px;max-width:640px">
  <div class="text-center">
    <img src="data:image/gif;base64,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">
  </div>
  <hr>

  <p>Welcome, this Server is powered by Fusio <a href="https://github.com/apioo/fusio-plant">Plant</a>.
  You see this page because you have visited a domain which is not mapped to a project.
  This is most likely a problem with the server configuration, please contact the server administrator.</p>
</div>

</body>
</html>
EOF
chown -R www-data: /opt/plant/www
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
mysql_password=$(tr -dc 'A-Za-z0-9_' < /dev/urandom | head -c 20)
backend_username=$(tr -dc 'A-Za-z0-9' < /dev/urandom | head -c 8)
backend_password=$(tr -dc 'A-Za-z0-9_' < /dev/urandom | head -c 16)
rm /etc/nginx/sites-enabled/default
cat > /etc/nginx/sites-available/plant <<EOF
server {
  server_name $domain;
  location / {
    proxy_pass http://127.0.0.1:8900;
  }
}

server {
  listen 80 default_server;
  server_name _;
  location / {
    root /opt/plant/www;
    index index.html;
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
      FUSIO_PROJECT_KEY: "$project_key"
      FUSIO_ENV: "prod"
      FUSIO_DEBUG: "false"
      FUSIO_CONNECTION: "pdo-mysql://fusio:$mysql_password@mysql-fusio/fusio"
      FUSIO_BACKEND_USER: "$backend_username"
      FUSIO_BACKEND_EMAIL: "info@$domain"
      FUSIO_BACKEND_PW: "$backend_password"
      FUSIO_MAIL_SENDER: "info@$domain"
      FUSIO_URL: "https://$domain"
      FUSIO_APPS_URL: "https://$domain/apps"
      FUSIO_TRUSTED_IP_HEADER: "X-Forwarded-For"
      FUSIO_TENANT_ID: ""
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
pushd /docker/plant
docker compose up -d
popd
certbot --nginx --non-interactive --agree-tos -m "info@$domain" -d "$domain"
if [ $? -ne 0 ]; then
    echo ""
    echo "NOTICE: We could not obtain an SSL certificate for your domain $domain"
    echo "You need to point the DNS A/AAAA Record of your domain to your server"
    echo "After this you can obtain the certificate by running the following command"
    echo "> certbot --nginx --agree-tos -m \"info@$domain\" -d \"$domain\""
    echo ""
fi
supervisorctl reload
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
