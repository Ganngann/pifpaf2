import os
from playwright.sync_api import sync_playwright, expect
from pathlib import Path
import subprocess

# --- Configuration ---
BASE_URL = "http://localhost:8000"
SCREENSHOT_DIR = Path("jules-scratch/verification")
SCREENSHOT_DIR.mkdir(exist_ok=True)

# --- Fonctions utilitaires ---
def setup_database():
    """Prépare la base de données pour le test."""
    print("--- Préparation de la base de données ---")
    result = subprocess.run(
        ["php", "artisan", "migrate:fresh", "--seed"],
        cwd="pifpaf",
        capture_output=True,
        text=True
    )
    if result.returncode != 0:
        print("Erreur lors de la préparation de la base de données:")
        print(result.stderr)
        exit(1)
    print("Base de données prête.")

# --- Script principal ---
def run_browser_automation():
    with sync_playwright() as p:
        browser = p.chromium.launch()
        page = browser.new_page()

        # 1. Page d'accueil
        print("\n--- Test de la page d'accueil ---")
        page.goto(BASE_URL)
        # S'assurer que les images sont chargées
        page.wait_for_selector("img", state="visible")

        screenshot_path_welcome = SCREENSHOT_DIR / "04_welcome_page_with_images.png"
        page.screenshot(path=screenshot_path_welcome, full_page=True)
        print(f"Capture d'écran enregistrée : {screenshot_path_welcome}")

        # 2. Page de détail
        print("\n--- Test de la page de détail d'un article ---")
        page.goto(f"{BASE_URL}/items/1")
        page.wait_for_selector("img", state="visible")

        screenshot_path_show = SCREENSHOT_DIR / "05_show_page_with_gallery.png"
        page.screenshot(path=screenshot_path_show, full_page=True)
        print(f"Capture d'écran enregistrée : {screenshot_path_show}")

        browser.close()

if __name__ == "__main__":
    setup_database()
    run_browser_automation()
