#! /bin/bash

echo "Install dependencies :"
composer install

echo "Setup database :"
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate -n

echo "Setup JWT token :"
mkdir -p config/jwt
jwt_passphrase=${JWT_PASSPHRASE:-$(grep ''^JWT_PASSPHRASE='' .env | cut -f 2 -d ''='')}
echo "$jwt_passphrase" | openssl genpkey -out config/jwt/private.pem -pass stdin -aes256 -algorithm rsa -pkeyopt rsa_keygen_bits:4096
echo "$jwt_passphrase" | openssl pkey -in config/jwt/private.pem -passin stdin -out config/jwt/public.pem -pubout

chown www-data:www-data var/* vendor/*
chmod 775 var/* vendor/*
