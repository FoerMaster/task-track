import { defineConfig } from '@playwright/test';

const baseURL = 'http://127.0.0.1:8000';

export default defineConfig({
  testDir: 'tests/e2e',
  timeout: 60_000,
  expect: {
    timeout: 10_000,
  },
  retries: process.env.CI ? 2 : 0,
  workers: 1,
  use: {
    baseURL,
    trace: 'on-first-retry',
    viewport: { width: 1280, height: 720 },
  },
  webServer: {
    command:
      'sh -c "set -e; if [ ! -f .env ]; then cp .env.example .env; fi; mkdir -p database; touch database/testing.sqlite; APP_ENV=testing DB_CONNECTION=sqlite DB_DATABASE=database/testing.sqlite php artisan key:generate --force; APP_ENV=testing DB_CONNECTION=sqlite DB_DATABASE=database/testing.sqlite php artisan migrate:fresh --seed; APP_ENV=testing DB_CONNECTION=sqlite DB_DATABASE=database/testing.sqlite php artisan serve --host=127.0.0.1 --port=8000"',
    url: baseURL,
    reuseExistingServer: !process.env.CI,
  },
});
