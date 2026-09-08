FROM php:8.3-fpm

# Instalacja zależności systemowych
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm

# Czyszczenie pamięci podręcznej apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalacja rozszerzeń PHP wymaganych przez Laravel / MySQL
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Pobranie najnowszej wersji Composera
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Ustawienie katalogu roboczego
WORKDIR /var/www

EXPOSE 9000
CMD ["php-fpm"]