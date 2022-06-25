# bluesquare

## Getting Started

1. If not already done, [install Docker Compose](https://docs.docker.com/compose/install/)
2. Open your terminal
3. Run `git clone git@github.com:tahina36/bluesquare-test.git`
4. Run `cd bluesquare-test` to enter the repository
5. Run `docker-compose build --pull --no-cache` to build fresh images
6. Run `docker-compose up -d`
7. Run `docker-compose ps` to see the list of active containers, you should see 4 new container : myapp_nginx, myapp_php, myapp_phpmyadmin, myapp_db
8. Now you need to enter the newly created php container, you should run `docker exec -ti myapp_php bash` or `docker exec -ti myapp_php[_1] bash` (in case a controller with the same name already exists)
9. When you are inside the php container, Run `composer install -n`
10. Run `php bin/console doctrine:migration:migrate`
11. When the previous step is over, you can open `http://localhost:8000` in your favorite web browser to access the website or `http://localhost:8080` to access phpmyadmin (username & password are provided in the docker-compose.yml or the .env files
12. Run `docker-compose down --remove-orphans` to stop the Docker containers.

## Features
1. To init the database with some fixtures go to your terminal, enter the project, run `Docker exec -ti myapp_php bash` to enter the container then Run `php bin/console doctrine:fixtures:load` in the working directory
2. To run the unit tests, in your working directory Run `php ./vendor/bin/phpunit`
