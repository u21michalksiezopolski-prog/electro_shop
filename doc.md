# Dokumentacja Projektu - Electro Shop

## 1. Wprowadzenie

**Electro Shop** to aplikacja e-commerce napisana w PHP. Projekt został stworzony w celu demonstracji podstawowych mechanizmów sklepu internetowego, zrealizowanych przy użyciu prostego wzorca MVC (Model-View-Controller) i własnego systemu routingu.

### Główne Technologie
- **Backend:** PHP (wersja 8.0+)
- **Baza Danych:** MySQL / MariaDB
- **Frontend:** HTML, CSS, JavaScript (bez frameworków)

---

## 2. Instalacja i Konfiguracja

### A. Instalacja Lokalna (np. XAMPP na macOS)

1.  **Pobierz projekt:** Umieść pliki projektu w katalogu serwera WWW, np. `/Applications/XAMPP/xamppfiles/htdocs/elec`.
2.  **Utwórz bazę danych:**
    *   Uruchom serwer MySQL w XAMPP.
    *   Przejdź do `phpMyAdmin` (`http://localhost/phpmyadmin`).
    *   Utwórz nową bazę danych o nazwie `electro_shop` z kodowaniem `utf8mb4_unicode_ci`.
3.  **Importuj strukturę i dane:**
    *   Wybierz nowo utworzoną bazę.
    *   Przejdź do zakładki "Import".
    *   Wybierz plik `database/create_database.sql` z katalogu projektu i uruchom import.
4.  **Konfiguracja połączenia:**
    *   Domyślnie `config.php` jest skonfigurowany do pracy z XAMPP (użytkownik `root`, bez hasła). Jeśli masz inne ustawienia, utwórz plik `config.local.php` i nadpisz w nim wartości (patrz sekcja Konfiguracja).
5.  **Ustaw uprawnienia:**
    *   Katalog `storage/` musi mieć uprawnienia do zapisu dla serwera WWW. Na macOS może być konieczne uruchomienie:
        ```bash
        sudo chmod -R 775 storage/
        sudo chown -R daemon:admin storage/
        ```
6.  **Uruchomienie:**
    *   Otwórz w przeglądarce adres `http://localhost/elec/`. Dzięki plikowi `index.php` w głównym katalogu, aplikacja powinna działać od razu.

### B. Wdrożenie na Hostingu (np. InfinityFree)

1.  **Upload plików:** Wgraj wszystkie pliki projektu (przez FTP lub menedżer plików) do katalogu `htdocs/` na serwerze.
2.  **Baza danych:**
    *   W panelu InfinityFree utwórz nową bazę danych MySQL.
    *   Zanotuj **nazwę bazy**, **użytkownika**, **hasło** i **host bazy danych**.
    *   Przejdź do `phpMyAdmin` i zaimportuj plik `database/create_database.sql`.
3.  **Konfiguracja:**
    *   W głównym katalogu projektu (`htdocs/`) utwórz plik `config.local.php` i wklej do niego dane dostępowe do bazy oraz URL aplikacji:
        ```php
        <?php
        // config.local.php
        define('APP_URL', 'http://twoja-domena.epizy.com');
        define('APP_DEBUG', false); // Na produkcji zawsze false!

        define('DB_HOST', 'sqlXXX.epizy.com');
        define('DB_NAME', 'if0_XXX_electroshop');
        define('DB_USER', 'if0_XXX');
        define('DB_PASS', 'TwojeHasloDB');
        define('DB_PORT', 3306);
        ?>
        ```
    *   **Ważne:** Użyj dokładnych danych z panelu InfinityFree.
4.  **Uprawnienia:** Ustaw uprawnienia dla katalogu `storage/` na `755` lub `775` za pomocą menedżera plików na hostingu.
5.  **Testowanie:** Otwórz adres swojej strony. Aplikacja powinna działać.

---

## 3. Architektura Aplikacji

Projekt naśladuje wzorzec MVC, oddzielając logikę biznesową, dane i prezentację.

### A. Struktura Katalogów

- **/app**: Zawiera logikę aplikacji.
  - **/Controllers**: Klasy obsługujące żądania HTTP (np. `ProductController`, `Admin/OrderController`).
  - **/Models**: Klasy reprezentujące tabele w bazie danych (np. `Product`, `User`).
- **/database**: Skrypty SQL do tworzenia i zasilania bazy danych.
- **/public**: Publiczny katalog główny (Document Root).
  - `index.php`: **Front Controller** - pojedynczy punkt wejścia do aplikacji.
  - `/css`, `/js`: Zasoby statyczne.
- **/storage**: Katalog na pliki generowane przez aplikację (logi, cache, itp.). Musi być zapisywalny.
- **/views**: Szablony HTML (pliki `.php` z mieszanką HTML i PHP).
- **config.php**: Główny plik konfiguracyjny, autoloader, funkcje pomocnicze.
- **routes.php**: Definicje tras (mapowanie URL na akcje kontrolerów).
- **index.php** (w głównym katalogu): Prosty loader, który przekierowuje żądania do `public/index.php`, ułatwiając konfigurację na serwerach bez dostępu do ustawień Document Root.

### B. Cykl Życia

1.  Żądanie trafia do `public/index.php`.
2.  `public/index.php` ładuje `config.php` (konfiguracja i helpery).
3.  Następnie ładuje `routes.php`, aby pobrać tablicę z trasami.
4.  Skrypt porównuje bieżący URL i metodę HTTP z definicjami w `routes.php`.
5.  Po znalezieniu pasującej trasy, wywoływana jest odpowiednia funkcja anonimowa, która z kolei uruchamia statyczną metodę w odpowiednim **Kontrolerze** (np. `ProductController::show($slug)`).
6.  **Kontroler** przetwarza żądanie, komunikuje się z **Modelem** w celu pobrania lub zapisu danych.
7.  Na koniec Kontroler ładuje odpowiedni **Widok** (np. `views/product.php`), przekazując do niego dane.
8.  Widok jest renderowany wewnątrz głównego szablonu `views/layout.php`.

### C. Kluczowe Komponenty

- **Routing (`routes.php`):** Prosty system oparty na tablicy asocjacyjnej. Klucze to `METODA:URL`, np. `GET:/product/{slug}`.
- **Baza Danych (`DB` class w `config.php`):** Statyczna klasa pomocnicza (wrapper) dla PDO, udostępniająca metody `select`, `insert`, `update`, `delete`.
- **Modele (`app/Models`):** Proste klasy, które często mapują się na wiersze z bazy danych. Bazowy `Model.php` dostarcza podstawowe metody `find`, `all`, `save`.
- **Widoki (`views`):** Zwykłe pliki PHP, które używają funkcji pomocniczych (np. `e()`, `url()`) do bezpiecznego renderowania danych.
- **Autentykacja:** System oparty na sesji PHP. Funkcje `auth()`, `auth_check()` i `redirect()` zarządzają dostępem.

---

## 4. Główne Funkcje

### Role Użytkowników
- **Customer (Klient):** Domyślna rola. Może przeglądać produkty, składać zamówienia, zarządzać swoim profilem.
- **Employee (Pracownik):** Może zarządzać zamówieniami (zmieniać statusy).
- **Admin (Administrator):** Pełny dostęp do panelu admina: zarządzanie produktami, kategoriami, użytkownikami (zmiana ról) i zamówieniami.

### Panel Admina (`/admin/dashboard`)
Dostępny tylko dla ról `admin` i `employee`. Umożliwia:
- Zarządzanie produktami (CRUD).
- Przeglądanie zamówień i zmiana ich statusu.
- Zarządzanie użytkownikami i ich rolami.

### Wysyłka E-maili (`send_mail()` w `config.php`)
- Funkcja `send_mail()` obsługuje wysyłkę e-maili.
- **Na hostingu współdzielonym (np. InfinityFree)**, gdzie `exec()` jest zablokowane, funkcja automatycznie przełącza się na wysyłkę przez gniazdo SMTP (`smtp_send`).
- Do testowania lokalnego można użyć **Mailtrap**. Wystarczy ustawić stałe `MAILTRAP_*` w `config.php` lub `config.local.php`.

### Funkcje Pomocnicze (Helpers)
Plik `config.php` definiuje globalne funkcje ułatwiające pracę:
- `url($path)`: Generuje poprawny URL względem katalogu `public`.
- `asset($path)`: Generuje URL do zasobów statycznych (CSS, JS).
- `e($value)`: Zabezpiecza dane przed XSS (wrapper na `htmlspecialchars`).
- `redirect($url)`: Przekierowuje użytkownika.
- `flash($key, $value)`: Obsługuje jednorazowe komunikaty w sesji.
- `auth()`, `auth_check()`: Zwraca zalogowanego użytkownika lub sprawdza, czy jest zalogowany.

---

## 5. Struktura Bazy Danych

Główne tabele:
- `users`: Użytkownicy i ich dane.
- `products`: Katalog produktów.
- `categories`: Kategorie produktów.
- `orders`: Główne informacje o zamówieniach.
- `order_items`: Pozycje w ramach zamówienia.
- `cart`: Przechowuje zawartość koszyka (dla zalogowanych i gości).
- `password_reset_tokens`: Tokeny do resetowania hasła.

Pełna struktura znajduje się w pliku `database/create_database.sql`.

