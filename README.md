# 🏦 Bank Komunikatywny – Projekt systemu bankowości online

Ten projekt to prosta aplikacja bankowa stworzona w PHP z wykorzystaniem PostgreSQL. Pozwala użytkownikowi zalogować się, wyświetlić saldo, wykonać przelew oraz przejrzeć historię transakcji.

## 📌 Funkcje

- Logowanie użytkownika (z weryfikacją hasła)
- Wyświetlanie imienia, nazwiska i salda użytkownika
- Wykonywanie przelewów między kontami
- Historia transakcji
- System komunikatów i błędów
- Stylizacja responsywna przy pomocy SCSS (kompilowanego do CSS)
- Oddzielna strona przelewu (transakcji)

## 💻 Technologie

- PHP 8+
- PostgreSQL
- HTML5 + CSS3 (SCSS)
- JavaScript (jQuery – do menu)
- XAMPP / Apache
- DB Designer: [dbdiagram.io](https://dbdiagram.io)

## 🗃️ Struktura katalogów
│
├── db.php # Połączenie z bazą danych
├── main.php # Strona główna po zalogowaniu
├── transakcji.php # Strona wykonania przelewu
├── forma_kontakt.php # Strona kontaktowa
├── style.scss # Główny plik SCSS
├── style.css # Wygenerowany CSS
├── images/ # Obrazy (tło, logo itp.)
├── sql/ # Skrypty SQL (np. baza_danych.sql)
└── ...
Autor
Vladyslav Bykovskyi
