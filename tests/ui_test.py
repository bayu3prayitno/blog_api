import os
import sys
import time
import random
from selenium import webdriver
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from webdriver_manager.chrome import ChromeDriverManager

BASE_URL = os.getenv("APP_URL", "http://127.0.0.1:8000")
WAIT_TIME = 15

def get_driver():
    options = webdriver.ChromeOptions()
    options.add_argument("--headless=new")
    options.add_argument("--no-sandbox")
    options.add_argument("--disable-gpu")
    options.add_argument("--disable-dev-shm-usage")
    options.add_argument("--window-size=1920,1080")
    
    driver = webdriver.Chrome(service=Service(ChromeDriverManager().install()), options=options)
    return driver

def test_browse_posts_page(driver):
    print("1. Menguji Penjelajahan Artikel (Anonim)...")
    driver.get(f"{BASE_URL}/posts")
    
    wait = WebDriverWait(driver, WAIT_TIME)
    title_el = wait.until(EC.presence_of_element_located((By.ID, "page-title")))
    assert "Jelajahi" in title_el.text or "Artikel" in title_el.text, f"Judul halaman salah: {title_el.text}"
    print("[✔] Penjelajahan Artikel -> OK")

def test_register_new_user(driver):
    print("2. Menguji Registrasi User Baru...")
    driver.get(f"{BASE_URL}/register")
    
    wait = WebDriverWait(driver, WAIT_TIME)
    name_input = wait.until(EC.presence_of_element_located((By.ID, "name")))
    email_input = driver.find_element(By.ID, "email")
    password_input = driver.find_element(By.ID, "password")
    submit_btn = driver.find_element(By.XPATH, "//button[@type='submit']")
    
    # Buat email acak agar tidak bentrok
    random_num = random.randint(1000, 9999)
    email = f"penulis_{random_num}@gmail.com"
    
    print(f"Mendaftarkan user baru: {email}...")
    name_input.send_keys("Penulis Baru")
    email_input.send_keys(email)
    password_input.send_keys("password123")
    submit_btn.click()
    
    # Menunggu redirect ke posts
    wait.until(EC.presence_of_element_located((By.ID, "page-title")))
    print("[✔] Registrasi User -> OK")
    
    # Logout agar tes login seeded user bersih
    driver.execute_script("handleGlobalLogout();")
    time.sleep(1)

def test_login(driver):
    print("3. Menguji Login dengan Kredensial Seeder...")
    driver.get(f"{BASE_URL}/login")
    
    wait = WebDriverWait(driver, WAIT_TIME)
    email_input = wait.until(EC.presence_of_element_located((By.ID, "email")))
    password_input = driver.find_element(By.ID, "password")
    btn_login = driver.find_element(By.ID, "btn-login")
    
    print("Memasukkan kredensial login...")
    email_input.send_keys("bayu@gmail.com")
    password_input.send_keys("password123")
    btn_login.click()
    
    wait.until(EC.presence_of_element_located((By.ID, "page-title")))
    print("[✔] Login Pengguna -> OK")

def test_create_post(driver):
    print("4. Menguji Pembuatan Artikel Baru...")
    # Asumsi driver sudah login dari langkah sebelumnya
    driver.get(f"{BASE_URL}/posts/create")
    
    wait = WebDriverWait(driver, WAIT_TIME)
    title_input = wait.until(EC.presence_of_element_located((By.ID, "title")))
    content_input = driver.find_element(By.ID, "content-input")
    submit_btn = driver.find_element(By.XPATH, "//button[@type='submit']")
    
    # Buat post dengan judul acak agar unik
    random_id = random.randint(100, 999)
    post_title = f"Artikel Selenium Ke-{random_id}"
    post_content = "Ini adalah konten artikel uji coba otomatis menggunakan Python Selenium."
    
    print(f"Membuat artikel baru: '{post_title}'...")
    title_input.send_keys(post_title)
    content_input.send_keys(post_content)
    submit_btn.click()
    
    # Verifikasi pengalihan ke list postingan dan artikel baru muncul
    print("Menunggu artikel baru muncul di daftar...")
    wait.until(EC.presence_of_element_located((By.XPATH, f"//h4[contains(text(), '{post_title}')]")))
    print("[✔] Pembuatan Artikel -> OK")

def main():
    driver = None
    try:
        driver = get_driver()
        
        # Jalankan rangkaian tes secara sekuensial
        test_browse_posts_page(driver)
        test_register_new_user(driver)
        test_login(driver)
        test_create_post(driver)
        
        print("\n🎉 Semua pengujian UI Selenium Python berhasil dengan sukses!")
    except Exception as e:
        print(f"\n[-] Terjadi kesalahan saat pengujian: {e}", file=sys.stderr)
        sys.exit(1)
    finally:
        if driver:
            driver.quit()

if __name__ == "__main__":
    main()
