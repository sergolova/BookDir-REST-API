# Symfony test task.
## Creating a REST API for books and authors.

Project deployment order:
1. download the project locally:
> git pull https://github.com/sergolova/BookDir-REST-API.git master
2. Go to the /docker folder and build the images:
> docker-compose build
3. Launch environment:
> docker-compose up -d
4. Enter to the php-fpm container and run the dependency manager
> docker-compose exec -it php-fpm sh
> composer update
6. Check and run migrations:
> php bin/console doctrine:migrations:list
> php bin/console doctrine:migrations:migrate

The project is available on the port http://localhost:8034/
                                  
Database parameters (postgresql://root:root@db/database):
- user: *root*
- pass: *root*
- name: *database*
- host: *db*

Example requests for Phpstorm IDE in the /requests folder