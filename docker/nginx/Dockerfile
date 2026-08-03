FROM nginx:alpine

COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf

# Set working directory to match the app container
WORKDIR /var/www

# We don't copy the code here since Nginx will serve static files through volumes 
# or we can copy the public directory directly to serve assets
COPY public /var/www/public
