ARG BUILD_FROM=ghcr.io/hassio-addons/base/amd64:9.1.7
# hadolint ignore=DL3006
FROM $BUILD_FROM

SHELL ["/bin/bash", "-o", "pipefail", "-c"]
ENV LANG C.UTF-8
WORKDIR /data

RUN  apk add apache2 php8 php8-apache2 git && \
     rm -Rf /var/www/localhost/htdocs/* && \
     ln -sf /proc/self/fd/1 /var/log/apache2/access.log && \
     ln -sf /proc/self/fd/1 /var/log/apache2/error.log;
COPY run.sh /
RUN  git clone https://github.com/adamoutler/anycubic-3d-printer-ha-addon && \
     cd anycubic-3d-printer-ha-addon && \
     cp -R ./src/* /var/www/localhost/htdocs/ && \
     chown -R root:root /var/www/localhost/htdocs && \
     chmod -R 755 /var/www/localhost/htdocs && \
     cd anycubic-3d-printer-ha-addon && \
     chmod a+x /run.sh

EXPOSE 80


CMD [ "/run.sh" ]
