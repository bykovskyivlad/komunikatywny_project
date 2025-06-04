<?php

class SessionManager {
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function login($userData) {
        $_SESSION['username'] = $userData['login'] ?? '';
        $_SESSION['imie'] = $userData['imie'];
        $_SESSION['nazwisko'] = $userData['nazwisko'];
        $_SESSION['klient_id'] = $userData['klient_id'];
        $_SESSION['konto_id'] = $userData['konto_id'];
        $_SESSION['saldo'] = $userData['saldo'];
        $_SESSION['numer_konta'] = $userData['numer_konta'];
    }

    public static function logout() {
        session_unset();
        session_destroy();
        header("Location: main.php?wylogowano=1");
        exit;
    }
}
