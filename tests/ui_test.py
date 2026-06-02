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
    
    # Aktifkan pencatatan log konsol browser menggunakan set_capability (kompatibel Selenium 4)
    options.set_capability("goog:loggingPrefs", {"browser": "ALL"})
    
    driver = webdriver.Chrome(
        service=Service(ChromeDriverManager().install()), 
        options=options
    )
    return driver

def test_browse_posts_page(driver):
    print("1. Menguji Penjelajahan Artikel (Anonim)...")
    driver.get(f"{BASE_URL}/posts")
    
    wait = WebDriverWait(driver, WAIT_TIME)
    title_el = wait.until(EC.presence_of_element_located((By.ID, "page-title")))
    assert "Jelajahi" in title_el.text or "Artikel" in title_el.text, f"Judul halaman salah: {title_el.text}"
    print("[✔] Penjelajahan Artikel -> OK")

def type_via_js(driver, element, value):
    driver.execute_script(
        "arguments[0].value = arguments[1]; "
        "arguments[0].dispatchEvent(new Event('input', { bubbles: true })); "
        "arguments[0].dispatchEvent(new Event('change', { bubbles: true }));", 
        element, value
    )

def print_debug_info(driver):
    print("\n=== DEBUG INFO ===")
    print(f"URL Saat Ini: {driver.current_url}")
    
    try:
        token_exists = driver.execute_script("return localStorage.getItem('jwt_token') !== null;")
        print(f"Token di LocalStorage terdeteksi: {token_exists}")
    except Exception as e:
        print(f"Gagal memeriksa localStorage: {e}")
    
    print("\n--- BROWSER CONSOLE LOGS ---")
    try:
        for entry in driver.get_log('browser'):
            print(entry)
    except Exception as log_ex:
        print(f"Gagal mengambil console logs: {log_ex}")
    print("----------------------------")
    
    print("\n--- TEKS HALAMAN (BODY) ---")
    try:
        print(driver.find_element(By.TAG_NAME, "body").text)
    except Exception as body_ex:
        print(f"Gagal mengambil teks body: {body_ex}")
    print("---------------------------")

    print("\n--- TOAST CONTAINER HTML ---")
    try:
        toast_container = driver.find_element(By.ID, "toast-container")
        print(toast_container.get_attribute("innerHTML"))
    except Exception as toast_ex:
        print(f"Gagal mengambil toast container: {toast_ex}")
    print("----------------------------")

    print("\nIsi HTML Halaman Lengkap (sebagian):")
    try:
        print(driver.page_source[:3000])
    except Exception as src_ex:
        print(f"Gagal mengambil page source: {src_ex}")
    print("==================\n")

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
    try:
        name_input = wait.until(EC.presence_of_element_located((By.ID, "name")))
        email_input = driver.find_element(By.ID, "email")
        password_input = driver.find_element(By.ID, "password")
        submit_btn = driver.find_element(By.XPATH, "//button[@type='submit']")
        
        # Buat email acak agar tidak bentrok
        random_num = random.randint(1000, 9999)
        email = f"penulis_{random_num}@gmail.com"
        
        print(f"Mendaftarkan user baru via JS: {email}...")
        type_via_js(driver, name_input, "Penulis Baru")
        type_via_js(driver, email_input, email)
        type_via_js(driver, password_input, "password123")
        
        driver.execute_script("arguments[0].click();", submit_btn)
        
        # Menunggu redirect ke posts
        wait.until(EC.presence_of_element_located((By.ID, "page-title")))
        print("[✔] Registrasi User -> OK")
        
        # Tutup pop-up notifikasi jika muncul agar tidak menghalangi tombol logout
        print("Mencoba menutup pop-up notifikasi registrasi sukses...")
        try:
            toast_close = WebDriverWait(driver, 3).until(
                EC.element_to_be_clickable((By.XPATH, "//*[@id='toast-container']//button"))
            )
            driver.execute_script("arguments[0].click();", toast_close)
            time.sleep(0.5) # Tunggu transisi fade-out animasi
            print("[✔] Menutup Pop-up Notifikasi -> OK")
        except Exception:
            # Jika pop-up tidak muncul, lewati saja
            print("Pop-up notifikasi tidak terdeteksi atau sudah hilang.")
            pass
        
        # Logout secara nyata menggunakan tombol UI
        print("Melakukan logout...")
        logout_btn = wait.until(EC.element_to_be_clickable((By.XPATH, "//button[@title='Logout']")))
        driver.execute_script("arguments[0].click();", logout_btn)
        
        # Tunggu sampai tombol "Masuk" (guest menu) muncul kembali di navbar
        wait.until(EC.presence_of_element_located((By.XPATH, "//a[contains(text(), 'Masuk')]")))
        print("[✔] Logout User -> OK")
        time.sleep(1)
        return email
    except Exception as e:
        print_debug_info(driver)
        raise e

def test_login(driver, email):
    print(f"3. Menguji Login dengan Kredensial Baru: {email}...")
    driver.get(f"{BASE_URL}/login")
    
    wait = WebDriverWait(driver, WAIT_TIME)
    try:
        email_input = wait.until(EC.presence_of_element_located((By.ID, "email")))
        password_input = driver.find_element(By.ID, "password")
        btn_login = driver.find_element(By.ID, "btn-login")
        
        print("Memasukkan kredensial login via JS...")
        type_via_js(driver, email_input, email)
        type_via_js(driver, password_input, "password123")
        
        driver.execute_script("arguments[0].click();", btn_login)
        
        wait.until(EC.presence_of_element_located((By.ID, "page-title")))
        print("[✔] Login Pengguna -> OK")
    except Exception as e:
        print_debug_info(driver)
        raise e

def test_create_post(driver):
    print("4. Menguji Pembuatan Artikel Baru...")
    # Asumsi driver sudah login dari langkah sebelumnya
    driver.get(f"{BASE_URL}/posts/create")
    
    wait = WebDriverWait(driver, WAIT_TIME)
    try:
        # Mencari elemen secara spesifik
        title_input = wait.until(EC.element_to_be_clickable((By.CSS_SELECTOR, "input#title")))
        content_input = wait.until(EC.element_to_be_clickable((By.CSS_SELECTOR, "textarea#content-input")))
        submit_btn = wait.until(EC.element_to_be_clickable((By.XPATH, "//button[@type='submit']")))
        
        # Buat post dengan judul acak agar unik
        random_id = random.randint(100, 999)
        post_title = f"Artikel Selenium Ke-{random_id}"
        post_content = "Ini adalah konten artikel uji coba otomatis menggunakan Python Selenium."
        
        print(f"Mengisi formulir artikel baru via JS: '{post_title}'...")
        
        type_via_js(driver, title_input, post_title)
        type_via_js(driver, content_input, post_content)
        
        # Debug: Cetak nilai input untuk verifikasi
        print(f"DEBUG: Nilai input judul terisi = '{title_input.get_attribute('value')}'")
        print(f"DEBUG: Nilai input isi terisi = '{content_input.get_attribute('value')}'")
        
        print("Menekan tombol Terbitkan Artikel...")
        # Klik submit menggunakan Javascript Click
        driver.execute_script("arguments[0].click();", submit_btn)
        
        # Verifikasi pengalihan ke list postingan dan artikel baru muncul
        print("Menunggu artikel baru muncul di daftar...")
        wait.until(EC.presence_of_element_located((By.XPATH, f"//h4[contains(text(), '{post_title}')]")))
        print("[✔] Pembuatan Artikel -> OK")
    except Exception as e:
        print_debug_info(driver)
        raise e

def main():
    driver = None
    try:
        driver = get_driver()
        
        # Jalankan rangkaian tes secara sekuensial
        test_browse_posts_page(driver)
        registered_email = test_register_new_user(driver)
        test_login(driver, registered_email)
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
