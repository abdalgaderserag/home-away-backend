cp ./env.example .env
docker compose up -d --build
docker compose exec app composer install
docker compose exec app php artisan key:generate