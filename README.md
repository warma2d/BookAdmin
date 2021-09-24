# BookAdmin

BookAdmin - CRUD админка для управления авторами и книгами. 

## Запуск

```bash
docker-compose build app
```
```bash
docker-compose --env-file root/.env up -d
```

```bash
docker exec -it bookadmin_app_1 /bin/bash
```

```bash
cd .. && composer install
```

```bash
composer dump-autoload
```

```bash
php ./bin/console doctrine:migrations:migrate
```

```bash
php ./vendor/bin/phpunit ./tests --testdox
```
