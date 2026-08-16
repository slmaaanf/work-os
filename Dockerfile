FROM php:8.3-apache

# 1. Install dependensi sistem & Node.js (untuk Vite)
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

# 2. Bersihkan cache sistem
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# 3. Install ekstensi PHP yang dibutuhkan Laravel
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 4. Aktifkan mod_rewrite Apache (wajib untuk routing Laravel)
RUN a2enmod rewrite

# 5. Ubah DocumentRoot Apache agar langsung mengarah ke folder /public
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 6. Set direktori kerja di dalam server
WORKDIR /var/www/html

# 7. Salin seluruh kode aplikasi kita ke dalam server
COPY . .

# 8. Install Composer & jalankan install
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer install --optimize-autoloader --no-dev

# 9. Build aset UI/UX (Vite)
RUN npm install
RUN npm run build

# 10. Berikan hak akses untuk folder storage agar Laravel bisa menyimpan log & sesi
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80