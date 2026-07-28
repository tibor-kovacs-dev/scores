import { test, expect } from '@playwright/test'

test('home page loads correctly', async ({ page }) => {
  await page.goto('/')

  
  await page.waitForLoadState('networkidle')

  await expect(page.getByRole('heading', { name: /FIFA WORLD CUP|WM 2026/i })).toBeVisible({ timeout: 10000 })

  await expect(page.getByPlaceholder(/Suche nach Team oder Spiel/i)).toBeVisible()
})