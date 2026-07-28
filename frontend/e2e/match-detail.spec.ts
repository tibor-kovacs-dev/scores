import { test, expect } from '@playwright/test'

test('match detail page works', async ({ page }) => {
  await page.goto('/')

  await page.waitForLoadState('networkidle')

  const matchCards = page.getByTestId('match-card')

  await expect(matchCards.first()).toBeVisible({
    timeout: 15000,
  })

  expect(await matchCards.count()).toBeGreaterThan(0)

  await matchCards.first().click()

  await expect(page).toHaveURL(/\/match\/\d+$/, {
    timeout: 15000,
  })

  await page.waitForLoadState('networkidle')

  const scoreBoard = page.locator('.text-7xl')

  await expect(scoreBoard).toBeVisible({
    timeout: 15000,
  })

  await expect(scoreBoard).toContainText('-')
})