### **Petunjuk Penggunaan**
Proyek ini menggunakan Laravel 10 dan JWT (Authentikasi), proyek ini berisikan API untuk pengajuan izin

### **Software pendukung yang perlu disiapkan sebelum memulai**
- PHP
- Composer
- NodeJs
- Git

### **Panduan Instalasi**

- Download code pada github, setelah itu extract
- Buka terminal atau command line, kemudian arahkan ke folder ***approval_be***
- Jalankan perintah:
  ```
  composer install
  ```
- Langkah selanjutnya, jalankan perintah:
  ```
  php -r "copy('.env.example', '.env');";
  ```
- Kemudian membuat application key baru, dengan cara jalankan perintah:
  ```
  php artisan key:generate
  ```
- Selanjutnya, generate secret key untuk JWT, seperti berikut :
  ```
  php artisan jwt:secret
  ```
- Buat database dengan nama ***approval***
- Sesuaikan parameter pada file **.env**, seperti berikut:
  ```
  APP_URL=http://localhost

  DB_CONNECTION=mysql
  DB_HOST=127.0.0.1
  DB_PORT=3306
  DB_DATABASE=approval
  DB_USERNAME=root
  DB_PASSWORD=
  ```
- Selanjutnya, jalankan perintah:
  ```
  php artisan migrate --seed
  ```
- Buka command line yang baru, jalankan perintah:
  ```
  php artisan serve
  ```

- Testing API menggunakan Postman
