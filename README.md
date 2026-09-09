# Card & Booking Management API

RESTful API zbudowane w oparciu o **Laravel 11** i **MySQL 8.0**, uruchamiane w odizolowanym środowisku **Docker Compose**.

Projekt służy do zarządzania kartami podarunkowymi oraz autoryzacją użytkowników z wykorzystaniem **Laravel Sanctum**.

---

## 🛠️ Stos technologiczny

| Technologia      | Wersja / rozwiązanie         |
| ---------------- | ---------------------------- |
| PHP              | 8.3                          |
| Laravel          | 11                           |
| MySQL            | 8.0                          |
| Authentication   | Laravel Sanctum              |
| Testing          | PHPUnit / Laravel Test Suite |
| Database Testing | `RefreshDatabase`            |
| Containerization | Docker / Docker Compose      |

---

## 🚀 Wymagania systemowe

Przed uruchomieniem projektu upewnij się, że masz zainstalowane:

* [Docker Desktop](https://www.docker.com/)
* Docker Compose
* Git

---

## ⚙️ Konfiguracja środowiska

Projekt wykorzystuje plik `.env` do konfiguracji środowiska.

Skopiuj plik `.env.example`:

```bash
cp .env.example .env
```

### Przykładowa konfiguracja `.env`

> **Uwaga:** `DB_HOST` musi odpowiadać nazwie kontenera MySQL zdefiniowanej w `docker-compose.yml`.

```env
APP_NAME=GifCardApp
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8900

APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US

APP_MAINTENANCE_DRIVER=file

BCRYPT_ROUNDS=12

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=card-db
DB_PORT=3306
DB_DATABASE=cards
DB_USERNAME=laravel
DB_PASSWORD=secret

SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database

CACHE_STORE=database

VITE_APP_NAME="${APP_NAME}"
```

---

# 📦 Instalacja i uruchomienie

## 1. Klonowanie repozytorium

```bash
git clone https://github.com/MarekMikusek/booking-api.git
cd booking-api
```

## 2. Konfiguracja pliku `.env`

```bash
cp .env.example .env
```

## 3. Uruchomienie kontenerów Docker

Uruchom środowisko Docker Compose:

```bash
docker compose up -d --build
```

Skrypt inicjalizacyjny projektu przygotowuje dwie bazy danych:

* `cards` – baza deweloperska,
* `cards_test` – baza wykorzystywana podczas testów automatycznych.

## 4. Instalacja zależności i generowanie klucza aplikacji

Zainstaluj zależności Composera:

```bash
docker compose exec card-app composer install
```

Następnie wygeneruj klucz aplikacji:

```bash
docker compose exec card-app php artisan key:generate
```

## 5. Migracje i dane testowe

Uruchom migracje oraz seeder:

```bash
docker compose exec card-app php artisan migrate --seed
```

Po wykonaniu powyższych kroków aplikacja będzie dostępna pod adresem:

**http://localhost:8900**

---

# 🧪 Testy

Testy wykonywane są na dedykowanej bazie danych `cards_test`.

Do izolowania danych testowych wykorzystywany jest Laravel `RefreshDatabase`.

### Uruchomienie wszystkich testów

```bash
docker compose exec card-app php artisan test
```

### Uruchomienie konkretnego testu

```bash
docker compose exec card-app php artisan test --filter CardTest
```

---

# 📚 Dokumentacja API

## 🔐 Autoryzacja

Wszystkie zapytania do API powinny zawierać następujące nagłówki:

```http
Accept: application/json
Content-Type: application/json
```

Endpointy wymagające uwierzytelnienia muszą dodatkowo zawierać token Laravel Sanctum:

```http
Authorization: Bearer <TWÓJ_TOKEN_BEARER>
```

---

# 🔑 Authentication API

Bazowa ścieżka:

```text
/api
```

## 1. Logowanie użytkownika

**POST** `/api/login`

**Auth required:** ❌ Nie

### Request

```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

### Response — `200 OK`

```json
{
  "access_token": "1|abc123xyz...",
  "token_type": "Bearer",
  "user": {
    "id": 1,
    "name": "Jan Kowalski",
    "email": "user@example.com"
  }
}
```

---

## 2. Pobranie profilu użytkownika

**GET** `/api/me`

**Auth required:** ✅ Tak

### Response — `200 OK`

```json
{
  "id": 1,
  "name": "Jan Kowalski",
  "email": "user@example.com"
}
```

---

## 3. Wylogowanie

**POST** `/api/logout`

**Auth required:** ✅ Tak

### Response — `200 OK`

```json
{
  "message": "Successfully logged out"
}
```

---

# 💳 Cards API

Bazowa ścieżka:

```text
/api/cards
```

Wszystkie endpointy dotyczące kart wymagają uwierzytelnienia.

---

## 1. Lista kart

**GET** `/api/cards?page=1`

**Auth required:** ✅ Tak

Endpoint obsługuje paginację.

### Response — `200 OK`

```json
{
  "data": [
    {
      "id": 1,
      "card_number": "12345678901234567890",
      "pin": "1234",
      "activation_date": "2026-01-01 10:00:00",
      "expiration_date": "2027-01-01",
      "balance": 150.00
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 2,
    "total": 15
  }
}
```

---

## 2. Utworzenie karty

**POST** `/api/cards`

**Auth required:** ✅ Tak

### Request

```json
{
  "card_number": "12345678901234567890",
  "pin": "1234",
  "activation_date": "2026-09-09 10:00:00",
  "expiration_date": "2027-09-09",
  "balance": 150.00
}
```

### Response — `201 Created`

```json
{
  "data": {
    "id": 1,
    "card_number": "12345678901234567890",
    "pin": "1234",
    "activation_date": "2026-09-09 10:00:00",
    "expiration_date": "2027-09-09",
    "balance": 150.00
  }
}
```

---

## 3. Pobranie szczegółów karty

**GET** `/api/cards/{id}`

**Auth required:** ✅ Tak

### Response — `200 OK`

```json
{
  "data": {
    "id": 1,
    "card_number": "12345678901234567890",
    "pin": "1234",
    "activation_date": "2026-09-09 10:00:00",
    "expiration_date": "2027-09-09",
    "balance": 150.00
  }
}
```

---

## 4. Aktualizacja karty

**PUT** `/api/cards/{id}`

**Auth required:** ✅ Tak

### Request

```json
{
  "card_number": "12345678901234567890",
  "pin": "9999",
  "activation_date": "2026-09-09 10:00:00",
  "expiration_date": "2027-09-09",
  "balance": 250.00
}
```

### Response — `200 OK`

```json
{
  "data": {
    "id": 1,
    "card_number": "12345678901234567890",
    "pin": "9999",
    "balance": 250.00
  }
}
```

---

## 5. Usunięcie karty

**DELETE** `/api/cards/{id}`

**Auth required:** ✅ Tak

### Response

```text
204 No Content
```

---

# 🛠️ Przydatne polecenia CLI

## Świeże migracje wraz z przykładowymi danymi

Usuwa istniejące tabele, wykonuje migracje od początku i uruchamia seeder:

```bash
docker compose exec card-app php artisan migrate:fresh --seed
```

## Pełny reset środowiska Docker

Usuwa kontenery oraz wolumeny:

```bash
docker compose down -v
```

Następnie uruchom środowisko ponownie:

```bash
docker compose up -d
```

## Czyszczenie cache aplikacji

```bash
docker compose exec card-app php artisan optimize:clear
```

---

# 📁 Struktura projektu

Typowa struktura aplikacji Laravel:

```text
booking-api/
├── app/
│   ├── Http/
│   ├── Models/
│   └── ...
├── bootstrap/
├── config/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── routes/
│   └── api.php
├── tests/
│   ├── Feature/
│   └── Unit/
├── docker-compose.yml
├── .env.example
├── composer.json
└── README.md
```

---

# 📄 Licencja

Projekt jest udostępniony na licencji **MIT**.

Szczegóły znajdują się w pliku [`LICENSE`](LICENSE).
