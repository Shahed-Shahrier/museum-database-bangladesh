FROM mcr.microsoft.com/devcontainers/php:8.3

# Install PHP extensions
RUN apt-get update && apt-get install -y \
    php8.3-mysqli \
    php8.3-mysql \
    && phpenmod mysqli \
    && rm -rf /var/lib/apt/lists/*
