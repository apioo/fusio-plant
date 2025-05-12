FROM fusio/fusio:5.2
ARG FUSIO_APP_PLANT="0.1.1"
COPY ./resources /var/www/html/fusio/resources
COPY ./src /var/www/html/fusio/src
COPY ./.fusio.yml /var/www/html/fusio/.fusio.yml
COPY ./composer.json /var/www/html/fusio/composer.json
COPY ./composer.lock /var/www/html/fusio/composer.lock
COPY ./configuration.php /var/www/html/fusio/configuration.php
COPY ./container.php /var/www/html/fusio/container.php
COPY ./provider.php /var/www/html/fusio/provider.php
RUN chown -R www-data: /var/www/html/fusio
RUN cd /var/www/html/fusio && composer install
RUN mkdir /var/www/html/fusio/public/apps/plant
RUN wget -O /var/www/html/fusio/public/apps/plant/plant.zip "https://github.com/apioo/fusio-plant-frontend/releases/download/v${FUSIO_APP_PLANT}/plant.zip"
RUN cd /var/www/html/fusio/public/apps/plant && unzip plant.zip
RUN rm /var/www/html/fusio/public/apps/plant/plant.zip
