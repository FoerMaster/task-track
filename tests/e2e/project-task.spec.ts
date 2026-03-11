import { test, expect } from '@playwright/test';

async function register(page: any) {
  const stamp = Date.now();

  await page.goto('/register');
  await page.getByTestId('register-full-name').fill(`Test User ${stamp}`);
  await page.getByTestId('register-username').fill(`user_${stamp}`);
  await page.getByTestId('register-email').fill(`user_${stamp}@example.com`);
  await page.getByTestId('register-password').fill('Password!1234');
  await page.getByTestId('register-password-confirmation').fill('Password!1234');
  await page.getByTestId('register-submit').click();
  await page.waitForURL('**/dashboard');
}

test('user can create a project and a task', async ({ page }) => {
  await register(page);

  await page.getByTestId('project-create-open').click();
  await page.getByTestId('project-create-name').fill('Abris Demo');
  await page.getByTestId('project-create-code').fill('ABRIS');
  await page.getByTestId('project-create-submit').click();

  await expect(page.getByText('Abris Demo')).toBeVisible();

  await page.getByTestId('create-task-open').click();
  await page.waitForURL('**/tasks**');

  await page.getByTestId('create-task-name').fill('First Task');
  await page.getByTestId('create-task-description').fill('Task created in E2E test.');

  await page.getByTestId('create-task-project').click();
  await page.locator('[data-testid^="create-task-project-option-"]').first().click();

  await page.getByTestId('create-task-status').click();
  await page.locator('[data-testid^="create-task-status-option-"]').first().click();

  await page.getByTestId('create-task-type').click();
  await page.locator('[data-testid^="create-task-type-option-"]').first().click();

  await page.getByTestId('create-task-deadline').click();
  await page.locator('[data-today]').first().click();

  await page.getByTestId('create-task-submit').click();

  await page.waitForURL('**/tasks/**');
  await expect(page).toHaveURL(/\/tasks\//);
});
