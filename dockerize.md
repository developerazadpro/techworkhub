TechWorkHub Docker Setup — Start to Finish
Step 0 — Project Root

    Make sure you are in your Laravel project root (techworkhub-backend) containing:

    Dockerfile
    .env
    composer.json
    artisan

Step 1 — Stop any running containers
    docker compose down

Step 2 — Create docker-compose.yml
    Create file docker-compose.yml in project root:

    version: '3.8'

    services:
    app:
        build: .
        container_name: techworkhub-backend
        restart: unless-stopped
        working_dir: /var/www
        volumes:
        - .:/var/www
        ports:
        - "8000:8000"
        depends_on:
        - mysql
        environment:
        DB_CONNECTION: mysql
        DB_HOST: mysql
        DB_PORT: 3306
        DB_DATABASE: techworkhub
        DB_USERNAME: root
        DB_PASSWORD: root
        command: php artisan serve --host=0.0.0.0 --port=8000

    mysql:
        image: mysql:8
        container_name: techworkhub-mysql
        restart: unless-stopped
        environment:
        MYSQL_DATABASE: techworkhub
        MYSQL_ROOT_PASSWORD: root
        ports:
        - "3307:3306"
        volumes:
        - mysql_data:/var/lib/mysql

    phpmyadmin:
        image: phpmyadmin:latest
        container_name: techworkhub-phpmyadmin
        restart: unless-stopped
        environment:
        PMA_HOST: mysql
        PMA_USER: root
        PMA_PASSWORD: root
        ports:
        - "8080:80"
        depends_on:
        - mysql

    volumes:
    mysql_data:

Step 2 Complete: This defines Laravel app, MySQL, phpMyAdmin, and persistent volume.

Step 3 — Verify Laravel .env for Docker
    DB_CONNECTION=mysql
    DB_HOST=mysql
    DB_PORT=3306
    DB_DATABASE=techworkhub
    DB_USERNAME=root
    DB_PASSWORD=root

DB_HOST must match the MySQL service name in docker-compose (mysql).

Step 4 — Start all containers
    docker compose up -d

Check containers:
docker ps

You should see:
techworkhub-backend
techworkhub-mysql
techworkhub-phpmyadmin

Step 5 — Run Laravel migrations
    docker exec -it techworkhub-backend php artisan migrate

    Creates tables in Docker MySQL container (techworkhub-mysql)

    Safe: does NOT touch your Laragon MySQL

Step 6 — Optional: Seed test data
    docker exec -it techworkhub-backend php artisan db:seed

    Generates realistic test data for development

Step 7 — Access phpMyAdmin

    Open browser:

    http://localhost:8080

    Server: mysql

    Username: root

    Password: root

    Browse tables, run queries, manage DB

Step 8 — Access Laravel App

    Open browser:

    http://localhost:8000

    Laravel welcome page or API routes should work

    Fully containerized backend

Step 9 — Stop containers when done
    docker compose down

    Stops all containers

    Data persists in volume mysql_data
