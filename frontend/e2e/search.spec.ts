import { test, expect } from '@playwright/test'

test('search functionality', async ({ page }) => {
  await page.goto('/')

  await page.waitForLoadState('networkidle')

  const searchInput = page.getByPlaceholder(/Suche nach Team/i)

  await expect(searchInput).toBeVisible()

  await searchInput.fill('Austria')

  await page.waitForTimeout(1000)

  const results = page.locator('.absolute.w-full.mt-2')

  await expect(results).toBeVisible({
    timeout: 10000,
  })

  const austriaResult = results.getByText('Austria', {
    exact: true,
  })

  await expect(austriaResult).toBeVisible({
    timeout: 10000,
  })

  await austriaResult.click()

  await expect(page).toHaveURL(/\/team\/\d+$/, {
    timeout: 10000,
  })
})