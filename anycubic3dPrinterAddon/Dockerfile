ARG BUILD_FROM=ghcr.io/hassio-addons/base/amd64:9.1.7
# hadolint ignore=DL3006
FROM $BUILD_FROM
SHELL ["/bin/bash", "-o", "pipefail", "-c"]
ENV LANG C.UTF-8
WORKDIR /data
RUN apk add --no-cache apache2=2.4.53-r0 php8=8.0.16-r0 git=2.35.1-r0 && \
     httpd && \
     sleep 10 && \
     killall httpd && \
     rm -Rf /var/www/localhost/htdocs/* && \
     ln -sf /proc/self/fd/1 /var/log/apache2/access.log && \
     ln -sf /proc/self/fd/1 /var/log/apache2/error.log;
COPY run.sh /
RUN /usr/bin/git clone https://github.com/adamoutler/anycubic-3d-printer-ha-addon.git
WORKDIR /data/anycubic-3d-printer-ha-addon
RUN cp -R ./src/* /var/www/localhost/htdocs/ && \
     chown -R root:root /var/www/localhost/htdocs && \
     chmod -R 755 /var/www/localhost/htdocs && \
     chmod a+x /run.sh
EXPOSE 80
CMD [ "/run.sh" ]
