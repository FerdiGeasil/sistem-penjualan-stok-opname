# Menggunakan image PHP versi 8.2 dengan Apache
FROM php:8.2-apache

# Menginstal ekstensi MySQLi agar PHP bisa konek ke database
RUN docker-php-ext-install mysqli

# Perbaikan: Menyalin seluruh isi folder root proyek ke direktori kerja Apache di dalam container
COPY . /var/www/html/

# Memberikan izin akses folder
RUN chown -R www-data:www-data /var/www/html