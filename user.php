<?php
class User {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function authenticate($login, $password) {
        $stmt = $this->pdo->prepare("
            SELECT klient.klient_id, klient.imie, klient.nazwisko,
                   konta.konto_id, konta.numer_konta, konta.saldo, klient.haslo
            FROM klient
            JOIN konta ON konta.klient_id = klient.klient_id
            WHERE klient.login = :login
        ");
        $stmt->execute(['login' => $login]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        return ($user && $user['haslo'] === $password) ? $user : false;
    }
}
