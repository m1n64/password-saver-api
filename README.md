# WIP
*****

### How to startup (if your php >= 8):
1. composer install
2. npm install
3. php artisan key:generate
4. create database
   1. create DB in mysql
   2. create file *keys_db.sqlite* in *database* folder
5. copy .env.example to .env and config DB_\* connection
5. php artisan migrate --seed
6. php artisan serve
7. npm run dev

### How to startup (via docker & sail):
1. ./vendor/bin/sail composer install
2. ./vendor/bin/sail npm install
3. ./vendor/bin/sail artisan key:generate
4. create database
    1. create DB in mysql (./vendor/bin/sail mysql -u root)
    2. create file *keys_db.sqlite* in *database* folder
5. copy .env.example to .env and config DB_\* connection
5. ./vendor/bin/sail artisan migrate --seed
6. ./vendor/bin/sail up
7. ./vendor/bin/sail npm run dev

or use DockStation or another Docker GUI app.

### How to get access key:
Without sail:
1. php artisan admin:generate-code

With sail:
1. ./vendor/bin/sail artisan admin:generate-code
