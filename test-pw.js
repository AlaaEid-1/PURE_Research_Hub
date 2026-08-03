const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: true });
  const page = await browser.newPage();

  page.on('console', msg => console.log('BROWSER CONSOLE:', msg.text()));
  page.on('pageerror', error => console.log('BROWSER ERROR:', error.message));

  console.log("Navigating to http://127.0.0.1:8000 ...");
  await page.goto('http://127.0.0.1:8000');
  
  await page.waitForTimeout(2000); // Wait for Alpine to initialize

  const alpineTestBtn = await page.$('#alpine-test-btn');
  if (alpineTestBtn) {
    console.log("Found Alpine test button, clicking...");
    await alpineTestBtn.click();
    await page.waitForTimeout(500);
  } else {
    console.log("Alpine test button not found!");
  }

  // Check if window.Alpine exists
  const hasAlpine = await page.evaluate(() => typeof window.Alpine !== 'undefined');
  console.log('window.Alpine exists?', hasAlpine);

  await browser.close();
})();
