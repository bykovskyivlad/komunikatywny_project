<?php
session_start();
$communicate = $_SESSION['communicate'] ?? '';
unset($_SESSION['communicate']);

if(!isset($_SESSION['users'])){
$_SESSION['users'] = $_SESSION['users'] ?? [
    'user1' => ['password' => 'password123', 'balance' => 1000],
    'user2' => ['password' => 'password456', 'balance' => 500],
    'user3' => ['password' => 'anton123', 'balance' => 2000],
    'user4' => ['password' => 'password789', 'balance' => 4000]
];
}

$users = &$_SESSION['users']; 


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if (!isset($_SESSION['users'][$username])) {
        $communicate = "Błąd: użytkownik nie istnieje";
    } elseif ($_SESSION['users'][$username]['password'] !== $password) {
        $communicate ="Błąd: niepoprawne hasło.";
    } else {
        $_SESSION['username'] = $username;
        $_SESSION['balance'] = $_SESSION['users'][$username]['balance'];
        unset($_SESSION['logged_out']);
    }
}


if (isset($_GET['logout'])) {
    $_SESSION['logged_out'] = true;
    unset($_SESSION['username']); 
    unset($_SESSION['balance']);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['transfer'])) {
    if (!isset($_SESSION['username']) || isset($_SESSION['logged_out'])) {
        $communicate = "Musisz się zalogować!";
    } else {
        $amount = (float) $_POST['amount'];
        $recipient = $_POST['recipient'];
        $sender = $_SESSION['username'];
        
        if ($amount > 0 && $amount <= $users[$sender]['balance'] && isset($users[$recipient])) {
            $users[$sender]['balance'] -= $amount;
            $users[$recipient]['balance'] += $amount;
            $_SESSION['balance'] = $users[$sender]['balance'];
            $communicate = "Przelew na $amount PLN do $recipient wykonany!";
        } else {
            $communicate = "Błąd przelewu: sprawdź saldo lub dane odbiorcy.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <title>System bankowy</title>
  <script src="jquery-3.7.1.min.js"></script>
  <link rel="stylesheet" href="style.css?v=3">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body >
   <?php if (isset($_SESSION['username']) && !isset($_SESSION['logged_out'])): ?>
  <div class="menu-wrapper">
   <div class="menu-item">
      <button class="btn btn-outline-primary fw-bold">☰ Menu</button>
      <div class="submenu">
        <a href="main.php">Strona główna</a>
        <a href="forma_kontakt.php">Kontakt</a>
        <a href="?logout=1">Wyloguj</a>
      </div>
    </div>
  </div>
  <?php endif; ?>
  <div class="centered-wrapper">
    <div class="container">

    <h1>Bank Online</h1>

    <?php if (!empty($communicate)): ?>
  <div class="alert alert-info mt-3">
    <?= htmlspecialchars($communicate) ?>
  </div>
  <?php endif; ?>

    <?php if (!isset($_SESSION['username']) || isset($_SESSION['logged_out'])): ?>

      <h2>Logowanie</h2>

      <form method="post">
  <div class="input-icon">
    <label for="username" class="form-label">Login:</label>
    <input type="text" name="username" id="username" required>
    <i class='bx bxs-user'></i>
  </div>

  <div class="input-icon">
    <label for="password" class="form-label">Hasło:</label>
    <input type="password" name="password" id="password" required>
    <i class='bx bxs-lock-alt'></i>
  </div>

  <div class="text-center mt-3">
    <button type="submit" name="login" class="btn-success">Zaloguj</button>
  </div>
</form>

    <?php else: ?>

      <h2 class="text-center">Witaj, <?= $_SESSION['username']; ?>!</h2>
      <p class="text-center">Twoje saldo: <strong><?= $_SESSION['users'][$_SESSION['username']]['balance']; ?> PLN</strong></p>

      <?php include 'forma_po_logowaniu.php'; ?>
      

    <?php endif; ?>
</body>
</html>
<script>
  $(document).ready(function () {
    $('.menu-item').hover(
      function () {
        $(this).find('.submenu').stop(true, true).slideDown(150);
      },
      function () {
        $(this).find('.submenu').stop(true, true).slideUp(150);
      }
    );
  });
</script>
