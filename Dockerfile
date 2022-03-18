FROM webdevops/php-apache
COPY src /app
RUN chown -R root:root /app && chmod -R 755 /app/*
EXPOSE 80
