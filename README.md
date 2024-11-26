# Gym Tracker 🏋️‍♂️

Gym Tracker to prosta usługa webowa RESTful, która umożliwia użytkownikom zarządzanie danymi treningowymi. Użytkownicy mogą rejestrować się, logować, dodawać, pobierać i usuwać wyniki treningowe.

## Funkcje 🖱️

- **Autoryzacja użytkowników**: Funkcjonalności rejestracji, logowania i wylogowywania.
- **Zarządzanie treningami**: Dodawanie nowych treningów, edycja, pobieranie historii treningów oraz usuwanie treningów.
- **Autoryzacja oparta na sesji**: Sesje użytkowników są zarządzane, aby uwierzytelnić żądania i zapewnić bezpieczny dostęp do danych.
- **Bezpieczne zarządzanie konfiguracją**: Zmienna konfiguracja (np. dane logowania do bazy danych) jest teraz przechowywana w pliku `.env` z wykorzystaniem biblioteki [PHP dotenv](https://github.com/vlucas/phpdotenv), co zapewnia lepszą ochronę danych wrażliwych.
- **Obsługa Composer**: Zastosowanie narzędzia Composer do zarządzania zależnościami PHP, w tym instalacja i zarządzanie bibliotekami takimi jak PHP dotenv.
## Wymagania 📋

- PHP >= 7.4
- MySQL lub MariaDB
- Apache lub Nginx (lub dowolny serwer WWW obsługujący PHP)
- Composer (do zarządzania zależnościami)

## Instalacja 🛠️

1. **Zainstaluj Composer**:
   - Jeśli nie masz zainstalowanego Composera, możesz to zrobić, wykonując instrukcje dostępne na [oficjalnej stronie Composera](https://getcomposer.org/download/).

2. **Zainstaluj zależności**:
   - Po sklonowaniu repozytorium, przejdź do katalogu projektu i uruchom polecenie:

     ```bash
     composer install
     ```

   - To polecenie zainstaluje wszystkie wymagane biblioteki, w tym `phpdotenv`.

3. **Skonfiguruj plik `.env`**:
   - Utwórz plik `.env` w głównym katalogu projektu (jeśli go jeszcze nie ma) i wprowadź dane logowania do bazy danych:

     ```env
     DB_HOST=localhost
     DB_NAME=gym_tracker
     DB_USER=user
     DB_PASSWORD=haslo
     ```
4. **Uruchom projekt**:
   - Skonfiguruj swój serwer WWW (np. Apache lub Nginx) i wskazać na folder projektu, aby uruchomić aplikację.

### Podsumowanie zmian 📋

1. **Dodanie PHP dotenv** do zarządzania zmiennymi środowiskowymi (np. hasła do bazy danych).
2. **Integracja z Composer** do zarządzania zależnościami i autoloadingiem.
3. **Aktualizacja pliku `.gitignore`** w celu zignorowania pliku `.env`.
4. **Bezpieczne przechowywanie danych wrażliwych**.

---
