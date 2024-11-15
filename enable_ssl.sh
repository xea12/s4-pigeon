#!/bin/bash

# Enable SSL module
a2enmod ssl

# Enable the default SSL site
a2ensite default-ssl

# Generate a self-signed certificate if not already present
if [ ! -f /etc/ssl/certs/apache-selfsigned.crt ]; then
  openssl req -x509 -nodes -days 365 -newkey rsa:2048 \
  -keyout /etc/ssl/private/apache-selfsigned.key \
  -out /etc/ssl/certs/apache-selfsigned.crt -subj \
  "/C=PL/ST=State/L=City/O=Organization/OU=Unit/CN=localhost"
fi

# Start Apache in foreground
apache2-foreground