
import subprocess
import time
import os
import re
from playwright.sync_api import sync_playwright, Page, expect

# --- Configuration ---
LARAVEL_PORT = 8000
LARAVEL_URL = f"http://localhost:{LARAVEL_PORT}"
SCREENSHOT_DIR = "doc/screenshots/US-101-105-106"
DESKTOP_VIEWPORT = {"width": 1920, "height": 1080}
MOBILE_VIEWPORT = {"width": 375, "height": 812}

# --- Helper Functions ---

def run_command(command, cwd, capture_output=False, text=False):
    """Runs a command and handles errors."""
    print(f"Running command: {' '.join(command)} in {cwd}")
    try:
        result = subprocess.run(
            command,
            cwd=cwd,
            check=True,
            capture_output=capture_output,
            text=text
        )
        return result
    except subprocess.CalledProcessError as e:
        print(f"Error running command: {' '.join(command)}")
        if capture_output:
            print("Stderr:", e.stderr)
            print("Stdout:", e.stdout)
        raise

def setup_environment():
    """Sets up the database and frontend assets."""
    print("--- Setting up environment ---")
    # It's safer to ensure a clean slate
    if os.path.exists("pifpaf/database/database.sqlite"):
        os.remove("pifpaf/database/database.sqlite")
    run_command(["touch", "database/database.sqlite"], cwd="pifpaf")
    run_command(["php", "artisan", "migrate:fresh", "--seed"], cwd="pifpaf")
    run_command(["npm", "install"], cwd="pifpaf")
    run_command(["npm", "run", "build"], cwd="pifpaf")
    print("--- Environment setup complete ---")

def start_servers():
    """Starts Laravel and Vite servers in the background."""
    print("--- Starting servers ---")
    laravel_log = open("/tmp/laravel_verify.log", "w")
    vite_log = open("/tmp/vite_verify.log", "w")

    laravel_server = subprocess.Popen(
        ["php", "artisan", "serve", f"--port={LARAVEL_PORT}"],
        cwd="pifpaf",
        stdout=laravel_log,
        stderr=laravel_log
    )
    time.sleep(5) # Give Laravel time to start

    vite_server = subprocess.Popen(
        ["npm", "run", "dev"],
        cwd="pifpaf",
        stdout=vite_log,
        stderr=vite_log
    )
    time.sleep(5) # Give Vite time to start

    print("--- Servers started ---")
    return laravel_server, vite_server, laravel_log, vite_log

def stop_servers(laravel_server, vite_server, laravel_log, vite_log):
    """Stops the background servers."""
    print("--- Stopping servers ---")
    laravel_server.terminate()
    vite_server.terminate()
    laravel_server.wait()
    vite_server.wait()
    laravel_log.close()
    vite_log.close()
    print("--- Servers stopped ---")

def get_test_user_id():
    """Gets the ID of the default test user."""
    print("--- Getting test user ID ---")
    result = run_command(
        ["php", "artisan", "tinker", "--execute", "echo \\App\\Models\\User::where('email', 'test@example.com')->first()->id;"],
        cwd="pifpaf",
        capture_output=True,
        text=True
    )
    user_id = result.stdout.strip().split('\n')[-1]
    print(f"Found user ID: {user_id}")
    return user_id

def capture_screenshots(page: Page, user_id: str):
    """Captures screenshots of welcome (guest) and profile (auth) pages."""
    print("--- Capturing screenshots ---")

    # Create screenshot directory if it doesn't exist
    os.makedirs(SCREENSHOT_DIR, exist_ok=True)

    # --- Guest View Screenshots (Welcome Page) ---
    print("Capturing GUEST screenshots...")
    page.goto(LARAVEL_URL)
    expect(page.get_by_role("heading", name="Trouvez la perle rare")).to_be_visible()

    # Desktop
    page.set_viewport_size(DESKTOP_VIEWPORT)
    page.screenshot(path=f"{SCREENSHOT_DIR}/welcome-desktop.png", full_page=True)
    print("Captured welcome-desktop.png")

    # Mobile
    page.set_viewport_size(MOBILE_VIEWPORT)
    page.screenshot(path=f"{SCREENSHOT_DIR}/welcome-mobile.png", full_page=True)
    print("Captured welcome-mobile.png")

    # --- Authenticated View Screenshots (Profile Page) ---
    print("Capturing AUTHENTICATED screenshots...")

    # Login
    print("Logging in...")
    page.goto(f"{LARAVEL_URL}/login")
    page.get_by_label("Adresse e-mail").fill("test@example.com")
    page.get_by_label("Mot de passe").fill("password")
    page.get_by_role("button", name="Log in").click()
    expect(page).to_have_url(re.compile(r".*/dashboard$"))
    print("Login successful.")

    # Navigate to the correct public profile page
    profile_url = f"{LARAVEL_URL}/profile/{user_id}"
    print(f"Navigating to profile page: {profile_url}")
    page.goto(profile_url)
    expect(page.get_by_role("heading", name="Articles en vente")).to_be_visible()

    # Desktop
    page.set_viewport_size(DESKTOP_VIEWPORT)
    page.screenshot(path=f"{SCREENSHOT_DIR}/profile-desktop.png", full_page=True)
    print("Captured profile-desktop.png")

    # Mobile
    page.set_viewport_size(MOBILE_VIEWPORT)
    page.screenshot(path=f"{SCREENSHOT_DIR}/profile-mobile.png", full_page=True)
    print("Captured profile-mobile.png")

    print("--- Screenshot capture complete ---")


def main():
    """Main function to run the verification process."""
    setup_environment()
    test_user_id = get_test_user_id()
    laravel_server, vite_server, laravel_log, vite_log = start_servers()

    try:
        with sync_playwright() as p:
            browser = p.chromium.launch(headless=True)
            page = browser.new_page()
            capture_screenshots(page, test_user_id)
            browser.close()
    finally:
        stop_servers(laravel_server, vite_server, laravel_log, vite_log)

if __name__ == "__main__":
    main()
