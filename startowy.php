<?php
session_start();


if(!isset($_SESSION['users'])){
$_SESSION['users'] = $_SESSION['users'] ?? [
    'user1' => ['password' => 'password123', 'balance' => 1000],
    'user2' => ['password' => 'password456', 'balance' => 500],
    'user3' => ['password' => 'anton123', 'balance' => 4000],
    'user4' => ['password' => 'password789', 'balance' => 4000]
];
}

$users = &$_SESSION['users']; // Odwołanie do przechowywanej sesyjnie bazy użytkowników


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (!isset($_SESSION['users'][$username])) {
        $communikat = "<p style='color:red;'>Błąd: użytkownik nie istnieje.</p>";
    } elseif ($_SESSION['users'][$username]['password'] !== $password) {
        echo "<p style='color:red;'>Błąd: niepoprawne hasło.</p>";
    } else {
        $_SESSION['username'] = $username;
        $_SESSION['balance'] = $_SESSION['users'][$username]['balance'];
        unset($_SESSION['logged_out']);
    }
}

// Wylogowanie użytkownika
if (isset($_GET['logout'])) {
    $_SESSION['logged_out'] = true; // Oznaczamy użytkownika jako wylogowanego
    unset($_SESSION['username']); // Usuwamy dane użytkownika, aby umożliwić logowanie innego
    unset($_SESSION['balance']);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Wykonywanie przelewu
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['transfer'])) {
    if (!isset($_SESSION['username']) || isset($_SESSION['logged_out'])) {
        echo "<p style='color:red;'>Musisz się zalogować!</p>";
    } else {
        $amount = (float) $_POST['amount'];
        $recipient = $_POST['recipient'];
        $sender = $_SESSION['username'];
        
        if ($amount > 0 && $amount <= $users[$sender]['balance'] && isset($users[$recipient])) {
            $users[$sender]['balance'] -= $amount;
            $users[$recipient]['balance'] += $amount;
            $_SESSION['balance'] = $users[$sender]['balance']; // Aktualizacja salda zalogowanego użytkownika
            echo "<p style='color:green;'>Przelew na $amount PLN do $recipient wykonany!</p>";
        } else {
            echo "<p style='color:red;'>Błąd przelewu: sprawdź saldo lub dane odbiorcy.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>System bankowy</title>
</head>
<body>
    <h1>Bank Online</h1>
    <!communikat>
    <?php if (!isset($_SESSION['username']) || isset($_SESSION['logged_out'])): ?>
        <h2>Logowanie</h2>
        <form method="post">
            <label for="username">Login:</label>
            <input type="text" name="username" required>
            <label for="password">Hasło:</label>
            <input type="password" name="password" required>
            <button type="submit" name="login">Zaloguj</button>
        </form>
    <?php else: ?>
        <h2>Witaj, <?php echo $_SESSION['username']; ?>!</h2>
        <p>Twoje saldo: <?php echo $_SESSION['balance']; ?> PLN</p>
        
        <h2>Wykonaj przelew</h2>
        <form method="post">
            <label for="recipient">Odbiorca:</label>
            <input type="text" name="recipient" required>
            <label for="amount">Kwota:</label>
            <input type="number" name="amount" step="0.01" required>
            <button type="submit" name="transfer">Wykonaj przelew</button>
        </form>
        
        <a href="?logout">Wyloguj</a>
    <?php endif; ?>
</body>
</html