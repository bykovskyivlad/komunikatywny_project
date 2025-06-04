<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("SELECT rejestracja(
            :imie, :nazwisko, :login, :haslo,
            :data_urodzenia, :numer_telefonu,
            :pesel, :kraj_iso, :numer_paszportu
        )");

        $stmt->execute([
            'imie' => $_POST['imie'],
            'nazwisko' => $_POST['nazwisko'],
            'login' => $_POST['login'],
            'haslo' => $_POST['haslo'],
            'data_urodzenia' => $_POST['data_urodzenia'],
            'numer_telefonu' => $_POST['telefon'],
            'pesel' => $_POST['pesel'] ?: null,
            'kraj_iso' => $_POST['kraj_iso'] ?: 'PL',
            'numer_paszportu' => $_POST['paszport'] ?: null
        ]);

        echo "✅ Rejestracja zakończona sukcesem!";
    } catch (PDOException $e) {
        echo "❌ Błąd: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="pl">

<head>
    <link rel="stylesheet" href="style_r.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>
    <a href="main.php" class="back-arrow">
  <i class='bx bx-arrow-back'></i> 
</a>
    <form method="post" onsubmit="return validateForm()">
        <input name="imie" placeholder="Imię" required>
        <input name="nazwisko" placeholder="Nazwisko" required>
        <input name="login" placeholder="Login" required>
        <input name="haslo" placeholder="Hasło" required>
        <input name="data_urodzenia" id="data_urodzenia" type="date" required>
        <input name="telefon" placeholder="Telefon" required>

        <!-- PESEL: tylko 11 cyfr -->
        <input name="pesel" id="pesel" placeholder="PESEL" pattern="\d{11}"
            title="PESEL musi zawierać dokładnie 11 cyfr">

        <input name="paszport" placeholder="Paszport">
        <input name="kraj_iso" placeholder="Kraj (np. PL)">
        <button type="submit">Zarejestruj</button>
    </form>

    <script>
        function validateForm() {
            // Sprawdzenie wieku
            const dataUrodzenia = document.getElementById('data_urodzenia').value;
            if (!isAtLeast18(dataUrodzenia)) {
                alert("Musisz mieć co najmniej 18 lat.");
                return false;
            }

            // Sprawdzenie PESEL (jeśli podano)
            const pesel = document.getElementById('pesel').value;
            if (pesel && !/^\d{11}$/.test(pesel)) {
                alert("PESEL musi składać się z dokładnie 11 cyfr.");
                return false;
            }

            return true;
        }

        function isAtLeast18(birthDateStr) {
            const today = new Date();
            const birthDate = new Date(birthDateStr);
            const age = today.getFullYear() - birthDate.getFullYear();
            const m = today.getMonth() - birthDate.getMonth();
            return (
                age > 18 || (age === 18 && (m > 0 || (m === 0 && today.getDate() >= birthDate.getDate())))
            );
        }
    </script>