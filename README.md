# Description 
Demo Laravel project with creating/updating/deleting/viewing data through the API.
## Requirements
1. Git
2. Docker
## Installation
- `git clone https://github.com/zuldan-dev/LaravelAPI.git`
- `cd www/laravel-api`
- `cp .env.example .env` or `copy .env.example .env` and fill correct credentials
- `docker-compose up -d --build`
- `docker-compose exec php composer install`
- `docker-compose exec php php artisan vendor:publish --tag=public --force`
- `docker-compose exec php php artisan storage:link`
- `docker-compose exec php php artisan key:generate`
- `docker-compose exec php php artisan migrate --seed`
- `open http://localhost:8000`
## Usage

