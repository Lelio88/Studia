FROM dunglas/frankenphp

ENV SERVER_NAME=:80

# Installation des extensions PHP requises par Symfony et le projet
RUN install-php-extensions \
    intl \
    zip \
    pdo_mysql \
    opcache \
    xsl \
    dom \
    mbstring

# Copie du projet
COPY . /app
WORKDIR /app


