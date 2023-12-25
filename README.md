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
This application has several API-routes for working with Tasks
### GET Routes
- `api/tasks` - displays tasks in list-mode
- `api/tasks/tree` - displays tasks in tree-mode
- `api/tasks/{id}` - displays current task by *Id*
### POST Routes
- `api/login` - login by *email* and *password*
- `api/logout` - logging out
- `api/tasks` - create new task
### PUT Routes
- `api/tasks/{id}` - update task by *Id*
- `api/tasks/{id}/complete` - mark task **completed** by *Id*
### DELETE Routes
- `api/tasks/{id}` - remove task by *Id*

[Download](https://documenter.getpostman.com/view/13008132/2s9Ykt4dyk) API documentation.
