FROM php:8.3-cli-bookworm

RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app
COPY . .
RUN mkdir -p /app/assets/uploads/gallery /app/assets/uploads/news /app/assets/uploads/residents \
    && chmod -R 775 /app/assets/uploads

ENV PORT=10000
EXPOSE 10000

CMD php -S 0.0.0.0:${PORT} -t /app /app/router.php
