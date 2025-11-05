import re
from playwright.sync_api import sync_playwright, expect

def run(playwright):
    browser = playwright.chromium.launch(headless=True)
    context = browser.new_context()
    page = context.new_page()

    try:
        # Go to login page
        page.goto("http://localhost:8000/login")

        # Fill in credentials and log in
        page.get_by_label("Adresse e-mail").fill("admin@example.com")
        page.get_by_label("Mot de passe").fill("password")
        page.get_by_role("button", name="Log in").click()

        # Wait for navigation to the dashboard after login
        expect(page).to_have_url(re.compile(r".*/dashboard"))
        print("Login successful, redirected to user dashboard.")

        # Go to admin dashboard
        page.goto("http://localhost:8000/admin/dashboard")
        print("Navigated to admin dashboard.")

        # Check for the presence of the stat cards
        expect(page.get_by_role("heading", name="Utilisateurs")).to_be_visible()
        print("Found 'Utilisateurs' stat card.")
        expect(page.get_by_role("heading", name="Annonces")).to_be_visible()
        print("Found 'Annonces' stat card.")
        expect(page.get_by_role("heading", name="Transactions")).to_be_visible()
        print("Found 'Transactions' stat card.")

        print("All stat cards are visible.")

        # Take screenshot
        screenshot_path = "storage/screenshots/admin_dashboard_refactor.png"
        page.screenshot(path=screenshot_path)
        print(f"Screenshot saved to {screenshot_path}")

    except Exception as e:
        print(f"An error occurred: {e}")
        page.screenshot(path="storage/screenshots/error.png")

    finally:
        # Clean up
        context.close()
        browser.close()

with sync_playwright() as playwright:
    run(playwright)
