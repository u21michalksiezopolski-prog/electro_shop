# Electro Shop

Aplikacja webowa sklepu elektronicznego napisana w PHP.

##  Opis
Electro Shop to prosty sklep internetowy umożliwiający przeglądanie produktów, dodawanie ich do koszyka, rejestrację użytkowników, logowanie, składanie zamówień oraz korzystanie z panelu administracyjnego do zarządzania ofertą.

##  Funkcjonalności
- Przeglądanie produktów
- Dodawanie do koszyka
- Rejestracja i logowanie
- Resetowanie hasła
- Składanie zamówień
- Profil użytkownika
- Lista ulubionych produktów
- Panel administracyjny (produkty, zamówienia itp.)
- Konta testowe (admin, klient, pracownik)

## Wymagania
- PHP 7.4+
- MySQL 5.7 / MariaDB
- Serwer WWW (Apache, Nginx lub XAMPP/LAMP/WAMP)
- Composer (jeśli projekt go używa)

| Rola      | Email                                                       | Hasło |
| --------- | ----------------------------------------------------------- | ----- |
| Admin     | [admin@electroshop.pl](mailto:admin@electroshop.pl)         | 123   |
| Pracownik | [pracownik@electroshop.pl](mailto:pracownik@electroshop.pl) | 123   |
| Klient    | [customer@electroshop.pl](mailto:customer@electroshop.pl)   | 123   |

| Folder / Plik                     | Opis                                                 |
| --------------------------------- | ---------------------------------------------------- |
| `app/`                            | Logika aplikacji (kontrolery, modele, obsługa akcji) |
| `database/`                       | Pliki SQL, konfiguracje bazy                         |
| `public/`                         | Pliki publiczne, entry-point, index.php              |
| `storage/`                        | Uploady, cache, tymczasowe                           |
| `views/`                          | Szablony HTML/PHP                                    |
| `config.php` / `config.local.php` | Konfiguracja aplikacji                               |
| `routes.php`                      | Routing URL → kontrolery                             |
Jak korzystać

Po odpaleniu projektu:

Zaloguj się jako admin, by zarządzać produktami i zamówieniami.

Przeglądaj produkty jako klient.

Dodawaj produkty do koszyka.

Składaj zamówienia.

Zarządzaj profilem lub dodawaj ulubione produkty.

Co można rozwinąć

Sortowanie produktów

Paginacja list

System recenzji produktów

Powiadomienia e-mail

System ról z uprawnieniami

Filtrowanie produktów



