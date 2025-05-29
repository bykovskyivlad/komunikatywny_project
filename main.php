<?php
require_once 'db.php';
session_start();
$communicate = $_SESSION['communicate'] ?? '';
unset($_SESSION['communicate']);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
  $login = $_POST['username'];
  $password = $_POST['password'];

  $stmt = $pdo->prepare("
    SELECT klient.klient_id, klient.imie, klient.nazwisko,
           konta.konto_id, konta.saldo, log.haslo
    FROM log
    JOIN klient ON log.klient_id = klient.klient_id
    JOIN konta ON konta.klient_id = klient.klient_id
    WHERE log.login = :login
");

  $stmt->execute(['login' => $login]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$user) {
    $communicate = "Użytkownik o podanej nazwie nie istnieje.";
  } elseif ($user['haslo'] !== $password) {
    $communicate = "Błędne hasło.";
  } else {
    $_SESSION['username'] = $login;
    $_SESSION['imie'] = $user['imie'];
    $_SESSION['nazwisko'] = $user['nazwisko'];
    $_SESSION['klient_id'] = $user['klient_id'];
    $_SESSION['konto_id'] = $user['konto_id'];
    $_SESSION['saldo'] = $user['saldo'];
    header("Location: main.php");
    exit;
  }
}

if (isset($_GET['logout'])) {
  session_unset();
  session_destroy();
  header("Location: main.php?wylogowano=1");
  exit;
}
/*if (isset($_GET['wylogowano'])) {
  $communicate = "Zostałeś pomyślnie wylogowany.";
}*/



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

<body>
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

      <h1>Imperium Bank</h1>

      <?php if (!empty($communicate)): ?>
        <div class="alert alert-info mt-3">
          <?= htmlspecialchars($communicate) ?>
        </div>
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
          </div>
        </form>

      <?php else: ?>

        <h2 class="text-center">
          Witaj,
          <?= isset($_SESSION['imie'], $_SESSION['nazwisko'])
            ? htmlspecialchars($_SESSION['imie'] . ' ' . $_SESSION['nazwisko'])
            : htmlspecialchars($_SESSION['username'] ?? '') ?>
        </h2>
        <p class="text-center">Twoje saldo: <strong><?= $_SESSION['saldo']; ?> PLN</strong></p>
        <div class="text-center">
        <a href="transakcji.php" class="btn-success">Wykonaj przelew</a>
        </div>
        
        <?php /*include 'forma_po_logowaniu.php';*/ ?>


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