# Restaurant API

Using Laravel 11 and applying TDD, I have built a API REST that allows restaurant owners to digitize their businesses. 
The platform offers functionalities such as restaurant registration, menu management, and QR code generation for a more 
interactive customer experience.

## ✅ Features

- Auth users
- Show restaurants
- Show restaurant by id
- Create restaurants
- Update restaurant
- Delete restaurants
- Show menus
- Show menu by id
- Create menus
- Update menu
- Delete menus
- Show dishes
- Show dish by id
- Create dish
- Update dish
- Delete dish
- Generate QR code
- Show QR code

## ⚙️ Tech Stack

- Laravel 11
- tymon/jwt-auth 2.*
- giauphan/laravel-qr-code 1.*
- Postgres 14


## 💾 Installation

Install and run

1. Clone and move to folder
```bash
$ git clone git@github.com:abrahamuchos/restaurant-tdd-api.git
$ cd restaurant-tdd-api
```

2. Install dependencies
```bash
$  composer install
```
3. Create a copy of the `.env.example` file and rename it to `.env`. Next, configure the necessary environment variables.

4. Run `php artisan jwt:secret` to generate a secret key for JWT. This will update your `.env` file with something like JWT_SECRET=foobar

5. Generate an application key by running `php artisan key:generate`.

5. Run `php artisan migrate` to create the database tables.

6. Run `php artisandb:seed` to create dummy data and admin user.

7. Run `php artisan serve` to start the Laravel development server.

8. Run `php artisan storage:link` to start the Laravel development server.

To run Jobs (Qr Image Generator) `php artisan queue:work` to start the Laravel queue worker (Check POST Menu endpoint)

## 📦 Environment Variables

To run this project, you will need to add the following environment variables to your .env file

```
DB_HOST
DB_PORT
DB_DATABASE
DB_USERNAME
DB_PASSWORD
JWT_SECRET
```

## 🗂️ Docs

[Documentation Restaurant TDD API - Postman](https://documenter.getpostman.com/view/6168326/2sAYQZGrZR)

You can find a .json with the endpoints in `/docs/Restaurant TDD.postman_collection.json`

## 🧑‍💻 Authors

- [@abrahamuchos](https://github.com/abrahamuchos)
- [Contact mail](mailto:j.abraham29@gmail.com)

## 📄 License

[MIT](https://choosealicense.com/licenses/mit/)
