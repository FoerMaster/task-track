# Abris

[![Lint](https://github.com/FoerMaster/task-track/actions/workflows/lint.yml/badge.svg)](https://github.com/FoerMaster/task-track/actions/workflows/lint.yml)
[![Tests](https://github.com/FoerMaster/task-track/actions/workflows/tests.yml/badge.svg)](https://github.com/FoerMaster/task-track/actions/workflows/tests.yml)

Abris — система управления задачами и проектами для командной работы. Проекты, роли, канбан‑доски и комментарии к задачам в одном приложении.

## Возможности
- Проекты и роли участников
- Задачи с типами, статусами и дедлайнами
- Канбан‑доска для личных задач
- Комментарии и вложения к задачам

## Технологии
- Laravel 12
- Vue 3 + Inertia
- Vite + Tailwind CSS
- SQLite/MySQL (на выбор)

<p>
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/php/php-original.svg" alt="PHP" height="32" />
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/laravel/laravel-original.svg" alt="Laravel" height="32" />
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/vuejs/vuejs-original.svg" alt="Vue" height="32" />
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/vitejs/vitejs-original.svg" alt="Vite" height="32" />
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/tailwindcss/tailwindcss-original.svg" alt="Tailwind CSS" height="32" />
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/typescript/typescript-original.svg" alt="TypeScript" height="32" />
  <img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/mysql/mysql-original.svg" alt="MySQL" height="32" />
</p>

## Требования
- PHP 8.2+
- Composer
- Node.js 22 + npm

## Быстрый старт
```bash
composer install
npm ci
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
php artisan storage:link
```

Запуск разработки:
```bash
php artisan serve
npm run dev
```

Приложение будет доступно по адресу `http://127.0.0.1:8000`.

## Сборка
```bash
npm run build
npm run build:ssr
```

## Тесты и проверки
```bash
vendor/bin/pint --test
npm run format:check
npm run lint
./vendor/bin/pest
```

## E2E
```bash
npx playwright install
npm run e2e
```

## Конфигурация
Ключевые переменные окружения находятся в `.env`:
```env
APP_NAME=Abris
APP_URL=http://localhost
DB_CONNECTION=sqlite
```

## Лицензия
MIT
