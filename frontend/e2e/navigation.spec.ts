import { test, expect } from '@playwright/test'

test('tab navigation works', async ({ page }) => {
  await page.goto('/')
  await page.waitForLoadState('networkidle')

  await page.getByRole('button', { name: /Gruppenstand/i }).click()
  await expect(page.locator('h2', { hasText: /Gruppenstand/i })).toBeVisible()

  await page.getByRole('button', { name: /Torschützenliste/i }).click()
  await expect(page.locator('h2', { hasText: /Torschützenliste/i })).toBeVisible()
})