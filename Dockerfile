# Use the official PHP image
FROM php:apache

# Copy the PHP file into the container
COPY index.php /var/www/html/
COPY css /var/www/html/css

# Expose port 80
EXPOSE 80