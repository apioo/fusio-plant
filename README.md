
# Fusio Plant

Fusio Plant is a server panel to easily self-host apps on your server.
It can be seen as a modern alternative to tools like cPanel with a simple,
performant and clean tech-stack.

## Goals

* Easily host multiple apps on your server
* Provide a web based admin panel to manage and monitor all apps
* Exposes a REST API to control your server
* Run multiple apps cost efficiently on a single serve
* Uses Nginx and Docker as tech-stack to host your apps
* Automatically obtain SSL certificates with certbot
* Secure by default so that you don't need to expose SSH on your server
* Keep you server clean and lightweight, we basically only install Docker and Nginx

## Non-Goals

* Run apps across multiple hosts, for this you should take a look at [Kubernetes](https://kubernetes.io/)

## Installation

To install Fusio Plant on your server you only need to execute the `install.sh` script as root.
Note currently only Ubuntu as OS is supported, we recommend to run this script on a fresh Ubuntu
installation. Simply download and run the `install.sh` script with the following command:

```
curl -s https://github.com/apioo/fusio-plant/install.sh | bash
```

Basically the script installs only Nginx, Docker and the Fusio Plant app.

## Folder

The following list covers all important folders of your Plant server.

### /docker

Contains all projects and each project contains a `docker-compose.yml` file.

### /opt/plant

Contains the plant executor which receives commands through the `/opt/plant/input` folder and
writes responses back to the `/opt/plant/output` folder. Since the plant app also runs in a
container those folders are mounted into the plant app and help to execute commands on the
host. The executor is a simple bash script which listens for file changes in this folder,
you can see all commands at the [install script](./install.sh).

### /cache

The cache folder which is used in case content caching is activated. 

### /etc/nginx/sites-available

Contains all available sites
