import puppeteer from 'puppeteer';

(async () => {
    const browser = await puppeteer.launch({ headless: "new" });
    const page = await browser.newPage();
    
    // Capture console output
    page.on('console', msg => console.log('PAGE LOG:', msg.text()));
    page.on('pageerror', error => console.log('PAGE ERROR:', error.message));

    console.log('Navigating to local test page...');
    await page.goto('http://127.0.0.1:8000/test-alpine', { waitUntil: 'networkidle0' });
    
    // Wait for Alpine to initialize
    await new Promise(r => setTimeout(r, 1000));
    
    console.log('Clicking the button...');
    await page.click('#test-button');
    
    await new Promise(r => setTimeout(r, 500));
    
    const displayStyle = await page.evaluate(() => {
        return document.getElementById('test-content').style.display;
    });
    
    console.log('Test Content Display Style:', displayStyle);
    
    await browser.close();
})();
