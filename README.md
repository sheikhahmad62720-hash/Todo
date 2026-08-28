# TaskFlow

A simple single-page todo application built with Laravel, Inertia and Vue.

Keep track of your tasks with priorities and due dates. No login required.

## Features

- Create, edit and delete tasks
- Mark tasks as completed / active
- Set priority (low / medium / high) and due date
- Search and filter tasks
- Task statistics (total / completed / pending)

## Stack

- Laravel
- Inertia.js + Vue 3
- Tailwind CSS
- Vite
- SQLite

## Getting Started

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

php artisan migrate --seed
npm run dev
```

Then visit the app at `http://localhost:8000` (run `php artisan serve` if needed).

## Production Build

```bash
npm run build
```

## Tests

```bash
php artisan test
```

## License

Open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).