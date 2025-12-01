# Zaawansowane funkcje bazy danych i usprawnienia aplikacji

> Lokalizacje plików z definicjami:
> - Widoki: `database/create_database.sql` (sekcja widoków — zakomentowane by importował się na shared hostingu)
> - Wyzwalacze i funkcje SQL: `database/create_database_triggers_and_routines.sql`

---

## 1) Widoki (SQL VIEW)

Nazwy i cele:
- `product_stats` (opis w `database/create_database.sql`)
  - Co liczy: dla każdego produktu agreguje liczbę zamówień (`total_orders`), łączną sprzedaną ilość (`total_sold`) oraz przychód (`total_revenue`).
  - Zastosowanie: raporty sprzedażowe, panel admina (statystyki produktów). Widok ułatwia zapytania raportowe, bo logika agregacji jest zapisana po stronie bazy.

- `order_stats` (opis w `database/create_database.sql`)
  - Co liczy: agreguje dzienne statystyki zamówień (liczba zamówień, łączny przychód, średnia wartość zamówienia).
  - Zastosowanie: dashboard administracyjny, wykresy i raporty dzienne.

Uwagi praktyczne:
- Na niektórych hostingach (InfinityFree, itp.) tworzenie VIEW może być zablokowane. W repo widoki są skomentowane w `create_database.sql` — zamiast nich aplikacja może wykonywać ekwiwalentne zapytania agregujące bezpośrednio w PHP (przykłady zapytań w dokumentacji).
- Jeśli hosting pozwala, widoki można odkomentować i utworzyć w bazie (phpMyAdmin / CLI).

Gdzie wywołać je w kodzie (sugestia):
- W kontrolerze admina (np. `App\Controllers\Admin\OrderController`) można wykonać `SELECT * FROM product_stats WHERE id = ?` lub `SELECT * FROM order_stats WHERE order_date = ?` i przekazać wynik do widoku (`views/admin/*`).

---

## 2) Wyzwalacze (TRIGGERS)
Plik: `database/create_database_triggers_and_routines.sql`

Znalezione wyzwalacze:

- `update_stock_after_order` (AFTER INSERT ON `order_items`)
  - Cel: po dodaniu pozycji zamówienia (w tabeli `order_items`) automatycznie zmniejszyć `products.stock` o `NEW.quantity`.
  - Gdzie ma znaczenie: podczas procesu realizacji zamówienia — gdy aplikacja tworzy wpisy w `order_items` (najczęściej w `OrderController::store()`), zmniejszenie stanu magazynowego następuje automatycznie w bazie.
  - Zaleta: Atomiczność — nie trzeba ręcznie aktualizować stanu magazynowego w aplikacji; zmniejszenie stanu jest zagwarantowane po poprawnym wstawieniu pozycji.

- `check_stock_before_cart` (BEFORE INSERT ON `cart`)
  - Cel: przed dodaniem pozycji do tabeli `cart` sprawdzić, czy jest wystarczająca ilość produktu (SELECT stock). Jeśli brak — zgenerować SIGNAL SQLSTATE z błędem (zapobiega dodaniu do koszyka).
  - Gdzie ma znaczenie: mechanizm obronny, chroniący bazę i logikę aplikacji przed tworzeniem pozycji, które przekraczają zapas. Można go traktować jako ostatnią linię obrony (aplikacja także powinna walidować ilość przed wysłaniem INSERT).

Uwagi i ryzyka:
- Wyzwalacze wykonują się po stronie serwera bazy; debugowanie może być trudniejsze niż w kodzie aplikacji.
- Nie wszystkie hostingi pozwalają tworzyć wyzwalaczy — wtedy logikę należy przenieść do warstwy aplikacji (PHP) z użyciem transakcji i kontroli zasobów.

---

## 3) Funkcje SQL
Plik: `database/create_database_triggers_and_routines.sql`

- `calculate_discount(old_price, new_price)`
  - Cel: obliczyć procent obniżki między `old_price` a `new_price` i zwrócić zaokrągloną wartość.
  - Zastosowanie: można używać w zapytaniach SELECT (np. do szybkiego obliczania i raportowania promocji) lub tworzyć kolumny pomocnicze w widokach.
  - Zaletą jest wykonanie obliczeń po stronie bazy co zmniejsza konieczność powtórnych obliczeń w PHP.

---

## 4) Gdzie te mechanizmy są / mogą być użyte w aplikacji PHP

- Tworzenie zamówienia (OrderController / Models/Order)
  - Kiedy aplikacja tworzy rekordy w `orders` i `order_items`, wyzwalacz `update_stock_after_order` automatycznie zmniejsza `products.stock`.
  - Jeśli host pozwala na wyzwalacze, nie trzeba dodatkowo zmieniać stanu magazynowego w PHP — co upraszcza kod i redukuje ryzyko niespójności.

- Dodawanie do koszyka (CartController)
  - Przed dodaniem do koszyka aplikacja powinna zweryfikować dostępny stock, ale wyzwalacz `check_stock_before_cart` zapewnia dodatkową weryfikację i odrzuca niepoprawne inserty.

- Panel administracyjny / raporty
  - Widoki `product_stats` i `order_stats` (jeśli aktywne) umożliwiają szybkie zapytania do bazy i pokazanie wyników w dashboardzie admina (np. `views/admin/dashboard.php`).
  - Alternatywnie te same agregacje można wykonywać w PHP (przydatne, jeśli host nie pozwala na VIEW).

---

## 5) Inne funkcje usprawniające działanie sklepu (aplikacja)

Poniżej opis kluczowych mechanizmów programowych i helperów z pliku `config.php` i innych miejsc:

### A. `DB` (wrapper PDO)
- Lokalizacja: `config.php` (klasa `DB`).
- Co robi: tworzy jedno połączenie PDO, udostępnia metody `select`, `selectOne`, `insert`, `update`, `delete`.
- Zaleta: centralizacja połączenia i konfiguracji (timeouty, tryb błędów), łatwe logowanie i obróbka wyjątków.

### B. `send_mail()` + `smtp_send()`
- Lokalizacja: `config.php`.
- Co robi:
  - `send_mail()` buduje wiadomość, loguje ją do `storage/emails.log`, i próbuje wysłać ją przez Mailtrap (curl+exec) lub poprzez `smtp_send()` (socket) — zależnie od dostępności exec().
  - `smtp_send()` łączy się bezpośrednio przez gniazdo TCP z serwerem SMTP i wykonuje AUTH/STARTTLS i wysyła dane.
- Zaleta: dwutorowy mechanizm (curl vs sockets) poprawia kompatybilność z różnymi hostingami; logowanie wiadomości pomaga w debugowaniu.

### C. Helpers (funkcje globalne)
- `e()` — zabezpiecza przed XSS (escape). Zmodyfikowana, by nie przekazywać `null` do `htmlspecialchars()`.
- `url()`, `asset()` — generowanie poprawnych linków względem katalogu `public` (ważne przy deployu w nested docroot).
- `flash()` — jednorazowe komunikaty w sesji (sukces/errore dla operacji CRUD).
- `csrf_token()` i `csrf_verify()` — prosta ochrona przed CSRF w formularzach.

### D. Logowanie i fallbacki
- `storage/emails.log` — log wysyłanych e‑maili (treść, nagłówki, status wysyłki) ułatwia debug.
- `storage/db_connect.log` — log prób łączenia z bazą (próby z różnymi hostami) — pomocne przy deployu na hostingach z różnymi hostami DB.

### E. Caching / optymalizacje prostym podejściem
- System nie ma rozbudowanego cache, ale wykorzystuje indeksy SQL (np. `idx_*`) na tabelach: `products`, `orders`, `users`, co przyspiesza wyszukiwania i filtrowanie.
- Indeksy tworzone są w `create_database.sql` (np. `INDEX idx_slug`, `INDEX idx_category`, `INDEX idx_price`).

---


## 7) Gdzie szukać plików/definicji

- Widoki, funkcje i wyzwalacze SQL:
  - `database/create_database.sql` (widoki — zakomentowane w repo dla zgodności z hostingiem)
  - `database/create_database_triggers_and_routines.sql` (wyzwalacze i funkcje)
- Aplikacja PHP:
  - `config.php` (DB wrapper, send_mail, helpery)
  - `app/Controllers` (logika biznesowa)
  - `app/Models` (mapowanie danych)
  - `views/` (szablony)

---

### Krótkie podsumowanie

- Widoki: wygodne do raportów, ale opcjonalne (hosting). 
- Wyzwalacze: automatyzują stock-check i aktualizację stanów — przydatne i zalecane, ale nie zawsze dostępne na shared hostingu.
- Funkcje SQL: pomocne do obliczeń (np. procent rabatu).
- Helpers i logowanie w PHP: istotne dla debugowania i niezawodności (fallback dla e‑maili, logi, CSRF, escape).

---
