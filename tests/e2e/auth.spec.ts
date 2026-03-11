import { test, expect } from '@playwright/test';

test('user can register and reach dashboard', async ({ page }) => {
  const stamp = Date.now();

  await page.goto('/register');
  await page.getByTestId('register-full-name').fill(`Test User ${stamp}`);
  await page.getByTestId('register-username').fill(`user_${stamp}`);
  await page.getByTestId('register-email').fill(`user_${stamp}@example.com`);
  await page.getByTestId('register-password').fill('Password!1234');
  await page.getByTestId('register-password-confirmation').fill('Password!1234');
  await page.getByTestId('register-submit').click();

  await page.waitForURL('**/dashboard');
  await expect(page).toHaveURL(/dashboard/);
});
