FROM webdevops/php-apache
COPY src /app
COPY docker.entrypoint.d.init_variables.sh /entrypoint.d/init_variables.sh
RUN chmod 755 /entrypoint.d/init_variables.sh && \
    chown -R root:root /app && chmod -R 755 /app/*
EXPOSE 80
