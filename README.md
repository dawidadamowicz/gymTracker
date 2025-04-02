# Gym Tracker 🏋️‍♂️

Gym Tracker to prosta usługa webowa RESTful, która umożliwia użytkownikom zarządzanie danymi treningowymi. Użytkownicy mogą rejestrować się, logować, dodawać, edytować i usuwać wyniki treningowe.

## Funkcje 🖱️

- **Autoryzacja użytkowników**: Funkcjonalności rejestracji, logowania i wylogowywania.
- **Implementacja CRUD oraz programowania obiektowego**
- **Zarządzanie treningami**: Dodawanie nowych treningów, edycja, pobieranie historii treningów oraz usuwanie treningów.
- **Autoryzacja oparta na sesji**: Sesje użytkowników są zarządzane, aby uwierzytelnić żądania i zapewnić bezpieczny dostęp do danych.
- **Bezpieczne zarządzanie konfiguracją**: Zmienna konfiguracja (np. dane logowania do bazy danych) jest teraz przechowywana w pliku `.env` z wykorzystaniem biblioteki [PHP dotenv](https://github.com/vlucas/phpdotenv), co zapewnia lepszą ochronę danych wrażliwych.
- **Obsługa Composer**: Zastosowanie narzędzia Composer do zarządzania zależnościami PHP, w tym instalacja i zarządzanie bibliotekami takimi jak PHP dotenv.
## Wymagania 📋

- PHP >= 7.4
- MySQL lub MariaDB
- Apache lub Nginx (lub dowolny serwer WWW obsługujący PHP)
- Composer (do zarządzania zależnościami)
---
