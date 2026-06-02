<?php

namespace Tests\Feature;

use Tests\TestCase;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\WebDriverExpectedCondition;

class SeleniumWebTest extends TestCase
{
    /**
     * @var RemoteWebDriver|null
     * Variabel untuk menyimpan instance dari browser Chrome yang sedang dikendalikan.
     */
    protected ?RemoteWebDriver $driver = null;

    /**
     * setUp() adalah fungsi bawaan PHPUnit yang akan selalu dijalankan PERTAMA KALI
     * SEBELUM setiap fungsi test (seperti test_can_browse_posts_page) dieksekusi.
     * Fungsinya di sini untuk menyiapkan dan membuka browser Chrome via Selenium.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // URL tempat Selenium Server berjalan. Port 4444 adalah bawaan dari file selenium-server.jar
        $host = env('SELENIUM_URL', 'http://localhost:4444');

        // Mengatur opsi untuk browser Google Chrome
        $options = new ChromeOptions();
        $options->addArguments([
            '--disable-gpu', // Menonaktifkan akselerasi GPU (wajib untuk environment tanpa layar/headless)
            '--headless=new', // Menjalankan Chrome tanpa membuka jendela UI (berjalan di background/layar belakang)
            '--no-sandbox', // Keamanan tambahan yang dinonaktifkan agar Chrome bisa jalan mulus di lingkungan WSL/Linux
            '--disable-dev-shm-usage', // Mengatasi masalah memori terbatas (RAM) di sistem operasi Linux
            '--window-size=1920,1080', // Menetapkan ukuran layar ke Full HD agar layout web tidak menjadi mode mobile
        ]);

        // Menerapkan pengaturan Chrome di atas ke kapabilitas WebDriver
        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

        // Mencoba terhubung ke Selenium Server. 
        // Jika server mati/belum dinyalakan, test akan dilewati (skipped) alih-alih menampilkan error/crash.
        try {
            $this->driver = RemoteWebDriver::create($host, $capabilities, 5000);
        } catch (\Exception $e) {
            $this->markTestSkipped('Selenium/ChromeDriver tidak berjalan di ' . $host . '. Error: ' . $e->getMessage());
        }
    }

    /**
     * tearDown() adalah fungsi bawaan PHPUnit yang akan selalu dijalankan TERAKHIR
     * SETELAH setiap fungsi test selesai dieksekusi, baik test itu gagal maupun berhasil.
     * Fungsinya sangat krusial: menutup browser agar RAM komputer kamu tidak penuh.
     */
    protected function tearDown(): void
    {
        // Jika browser sebelumnya berhasil terbuka, maka tutup (quit)
        if ($this->driver) {
            $this->driver->quit();
        }

        parent::tearDown();
    }

    /**
     * Skenario Test 1: Memastikan halaman daftar artikel (/posts) bisa dibuka 
     * dan memiliki judul yang tepat.
     */
    public function test_can_browse_posts_page(): void
    {
        // Pastikan driver sudah siap, jika tidak batalkan test ini
        if (!$this->driver) {
            $this->markTestSkipped('WebDriver session tidak terinisialisasi.');
        }

        // Tentukan URL web Laravel yang sedang berjalan (biasanya pakai php artisan serve di port 8000)
        $appUrl = env('APP_URL', 'http://localhost:8000');
        
        // Perintahkan browser untuk mengunjungi URL /posts
        $this->driver->get($appUrl . '/posts');

        // Tunggu maksimal 5 detik sampai elemen HTML dengan ID 'page-title' muncul di layar
        $this->driver->wait(5)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('page-title'))
        );

        // Ambil teks asli dari elemen HTML yang memiliki ID 'page-title' tersebut
        $title = $this->driver->findElement(WebDriverBy::id('page-title'))->getText();
        
        // Assertion: Cocokkan apakah teksnya benar-benar persis "Jelajahi Artikel"
        $this->assertEquals('Jelajahi Artikel', $title);
    }

    /**
     * Skenario Test 2: Memastikan halaman Login merender form input (email & password) dengan benar.
     */
    public function test_login_page_renders_form_fields(): void
    {
        if (!$this->driver) {
            $this->markTestSkipped('WebDriver session tidak terinisialisasi.');
        }

        $appUrl = env('APP_URL', 'http://localhost:8000');
        
        // Buka halaman /login
        $this->driver->get($appUrl . '/login');

        // Tunggu maksimal 5 detik sampai input elemen dengan ID 'email' muncul
        $this->driver->wait(5)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('email'))
        );

        // Cari elemen input email. Jika ketemu, pastikan nilainya terbaca/tidak kosong (not null)
        $emailInput = $this->driver->findElement(WebDriverBy::id('email'));
        $this->assertNotNull($emailInput);

        // Cari elemen input password. Jika ketemu, pastikan nilainya terbaca/tidak kosong (not null)
        $passwordInput = $this->driver->findElement(WebDriverBy::id('password'));
        $this->assertNotNull($passwordInput);
    }

    /**
     * Skenario Test 3: Memastikan halaman Register merender form input (nama, email, password) secara lengkap.
     */
    public function test_register_page_renders_form_fields(): void
    {
        if (!$this->driver) {
            $this->markTestSkipped('WebDriver session tidak terinisialisasi.');
        }

        $appUrl = env('APP_URL', 'http://localhost:8000');
        
        // Buka halaman /register
        $this->driver->get($appUrl . '/register');

        // Tunggu maksimal 5 detik sampai input dengan ID 'name' muncul 
        // (Ini dipakai sebagai indikator bahwa halaman register sudah selesai dimuat penuh)
        $this->driver->wait(5)->until(
            WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id('name'))
        );

        // Pastikan input 'name' berhasil ditemukan di halaman
        $nameInput = $this->driver->findElement(WebDriverBy::id('name'));
        $this->assertNotNull($nameInput);

        // Pastikan input 'email' berhasil ditemukan di halaman
        $emailInput = $this->driver->findElement(WebDriverBy::id('email'));
        $this->assertNotNull($emailInput);

        // Pastikan input 'password' berhasil ditemukan di halaman
        $passwordInput = $this->driver->findElement(WebDriverBy::id('password'));
        $this->assertNotNull($passwordInput);
    }
}