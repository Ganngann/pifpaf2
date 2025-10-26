
import subprocess
import re
from playwright.sync_api import sync_playwright, Page, expect

def setup_database():
    """Reset and seed the database for verification."""
    try:
        subprocess.run(
            ["php", "artisan", "migrate:fresh", "--seed", "--seeder=VerificationDataSeeder"],
            cwd="pifpaf",
            check=True,
            capture_output=True,
            text=True
        )
        # Get the ID of the created item
        result = subprocess.run(
            ["php", "artisan", "tinker", "--execute", "echo \\App\\Models\\Item::first()->id;"],
            cwd="pifpaf",
            check=True,
            capture_output=True,
            text=True
        )
        item_id = result.stdout.strip().split('\n')[-1]
        return item_id
    except subprocess.CalledProcessError as e:
        print("Failed to set up the database or get item ID.")
        print("Stderr:", e.stderr)
        print("Stdout:", e.stdout)
        raise

def capture_scenarios(page: Page):
    """Capture screenshots for both buyer and seller views."""
    print("Setting up database and getting item ID...")
    item_id = setup_database()
    item_url = f"http://localhost:8000/items/{item_id}"

    # --- Scenario 1: Buyer's View (Offer button is VISIBLE) ---
    print("Scenario 1: Running as Buyer...")
    page.goto("http://localhost:8000/login")
    page.get_by_label("Email").fill("buyer-verify@example.com")
    page.get_by_label("Password").fill("password")
    page.get_by_role("button", name="Log in").click()

    print(f"Navigating to item page: {item_url}")
    page.goto(item_url)
    expect(page.get_by_role("heading", name="Faire une offre")).to_be_visible()
    print("Taking screenshot of buyer's view...")
    page.screenshot(path="offer_button_visible.png")

    # Logout
    page.get_by_role("button", name="Acheteur Test").click()
    page.get_by_role("link", name="Déconnexion").click()
    expect(page).to_have_url(re.compile(r".*/$"))

    # --- Scenario 2: Seller's View (Offer button is HIDDEN) ---
    print("Scenario 2: Running as Seller...")
    page.goto("http://localhost:8000/login")
    page.get_by_label("Email").fill("seller-verify@example.com")
    page.get_by_label("Password").fill("password")
    page.get_by_role("button", name="Log in").click()

    print(f"Navigating to item page: {item_url}")
    page.goto(item_url)
    expect(page.get_by_role("heading", name="Faire une offre")).not_to_be_visible()
    print("Taking screenshot of seller's view...")
    page.screenshot(path="offer_button_hidden.png")

    print("Verification script completed successfully.")

def run_verification():
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        page = browser.new_page()
        capture_scenarios(page)
        browser.close()

if __name__ == "__main__":
    run_verification()
