from playwright.sync_api import sync_playwright

def run_cuj(page):
    # Navigate to the welcome page
    page.goto("http://localhost:8000")
    page.wait_for_timeout(1000)

    # Perform a search that will return no results
    page.get_by_placeholder("Que recherchez-vous ?").fill("ThisItemDoesNotExist123")
    page.wait_for_timeout(500)
    page.get_by_role("button", name="Rechercher").click()
    page.wait_for_timeout(2000)

    # Take screenshot of the empty state
    page.screenshot(path="/home/jules/verification/screenshots/verification.png")
    page.wait_for_timeout(1000)

if __name__ == "__main__":
    import os
    os.makedirs("/home/jules/verification/videos", exist_ok=True)
    os.makedirs("/home/jules/verification/screenshots", exist_ok=True)

    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        context = browser.new_context(
            record_video_dir="/home/jules/verification/videos",
            viewport={"width": 1280, "height": 720}
        )
        page = context.new_page()
        try:
            run_cuj(page)
        finally:
            context.close()
            browser.close()
