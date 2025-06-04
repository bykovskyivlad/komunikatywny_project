<?php
require_once 'db.php';
require_once 'user.php';
require_once 'session.php';

SessionManager::start();
$communicate = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
  $user = new User($pdo);
  $userData = $user->authenticate($_POST['username'], $_POST['password']);

  if ($userData) {
    SessionManager::login($userData);
    header("Location: main.php");
    exit;
  } else {
    $communicate = "Błędny login lub hasło.";
  }
}

if (isset($_GET['logout'])) {
  SessionManager::logout();
}
?>


<!DOCTYPE html>
<html lang="pl">

<head>
  <meta charset="UTF-8">
  <title>System bankowy</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css?v=3">
  <script src="jquery-3.7.1.min.js"></script>
  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body>

  <?php if (isset($_SESSION['username'])): ?>
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
      <h1>Imperium Bank</h1>

      <?php if (!empty($communicate)): ?>
        <div class="alert alert-info mt-3"><?= htmlspecialchars($communicate) ?></div>
      <?php endif; ?>

      <?php if (!isset($_SESSION['username'])): ?>
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
            <a href="rejestracja.php" class="btn-outline-primary">Zarejestruj się</a>
          </div>
        </form>
      <?php else: ?>
        <h2 class="text-center">
          Witaj, <?= htmlspecialchars($_SESSION['imie'] . ' ' . $_SESSION['nazwisko']) ?>
        </h2>
        <p class="text-center account-number">Numer konta: <strong><?= $_SESSION['numer_konta'] ?></strong></p>
        <p class="text-center account-number">Twoje saldo: <strong><?= $_SESSION['saldo'] ?> PLN</strong></p>

        <div class="text-center mt-3">
          <form action="transakcji.php" method="get" style="display:inline;">
            <button type="submit" class="btn-success">Wykonaj przelew</button>
          </form>
        </div>
      <?php endif; ?>
    </div>
  </div>

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

</body>

</html>