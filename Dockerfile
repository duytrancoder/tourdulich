FROM php:8.2-apache

# Cài đặt các thư viện hệ thống và PHP extensions cần thiết
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql mysqli \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Kích hoạt module rewrite của Apache (để dùng .htaccess)
RUN a2enmod rewrite

# Cấu hình Apache cho phép ghi đè cấu hình (.htaccess) trong thư mục /var/www/
RUN sed -i '/<Directory \/var\/www\/>/,/<\/Directory>/ s/AllowOverride None/AllowOverride All/' /etc/apache2/apache2.conf

# Thiết lập thư mục làm việc mặc định trong container
WORKDIR /var/www/html

# Copy toàn bộ mã nguồn dự án vào container (dành cho production/build độc lập)
COPY . /var/www/html/

# Phân quyền sở hữu thư mục cho user chạy Apache (www-data)
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80
