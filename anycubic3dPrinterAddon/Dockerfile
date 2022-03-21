ARG BUILD_FROM=ghcr.io/hassio-addons/base/amd64:9.1.7
# hadolint ignore=DL3006
FROM $BUILD_FROM
SHELL ["/bin/bash", "-o", "pipefail", "-c"]
ENV LANG C.UTF-8
WORKDIR /data

# hadolint ignore=DL3003,DL3018
RUN apk add --no-cache php8-apache2 git && \
     httpd && \
     sleep 10 && \
     killall httpd && \
     rm -Rf /var/www/localhost/htdocs/* && \
     ln -sf /proc/self/fd/1 /var/log/apache2/access.log && \
     ln -sf /proc/self/fd/1 /var/log/apache2/error.log;
COPY run.sh /
# hadolint ignore=SC2015
RUN  test -d anycubic-3d-printer-ha-addon && \
     git pull https://github.com/adamoutler/anycubic-3d-printer-ha-addon.git|| \ 
     /usr/bin/git clone https://github.com/adamoutler/anycubic-3d-printer-ha-addon.git
WORKDIR /data/anycubic-3d-printer-ha-addon
RUN cp -R ./src/* /var/www/localhost/htdocs/ && \
     chown -R root:root /var/www/localhost/htdocs && \
     chmod -R 755 /var/www/localhost/htdocs && \
     chmod a+x /run.sh
EXPOSE 80
CMD [ "/run.sh" ]
