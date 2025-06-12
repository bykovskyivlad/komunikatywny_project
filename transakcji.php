<?php
require_once 'db.php';
session_start();


if (!isset($_SESSION['username'])) {
  header("Location: main.php");
  exit;
}

$communicate = '';


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['transfer'])) {
  $recipientAccount = $_POST['recipient'];
  $amount = (float) $_POST['amount'];
  $desc = $_POST['description'] ?? '';
  $recipientName = $_POST['recipient_name'];
  $recipientSurname = $_POST['recipient_surname'];

  if ($amount <= 0) {
    $communicate = "Kwota przelewu musi być większa niż 0.";
  } else {
    $stmt = $pdo->prepare("SELECT konto_id, saldo FROM konta WHERE numer_konta = :numer_konta");
    $stmt->execute(['numer_konta' => $recipientAccount]);
    $recipient = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$recipient) {
      $communicate = "Nie znaleziono odbiorcy o podanym numerze konta.";
    } elseif ($amount > $_SESSION['saldo']) {
      $communicate = "Brak wystarczających środków na koncie.";
    } else {
      
      $pdo->beginTransaction();

      $stmt = $pdo->prepare("UPDATE konta SET saldo = saldo - :kwota WHERE konto_id = :konto_id");
      $stmt->execute(['kwota' => $amount, 'konto_id' => $_SESSION['konto_id']]);

      $stmt = $pdo->prepare("UPDATE konta SET saldo = saldo + :kwota WHERE konto_id = :konto_id");
      $stmt->execute(['kwota' => $amount, 'konto_id' => $recipient['konto_id']]);

      $stmt = $pdo->prepare("
                INSERT INTO transakcji (
                    nadawca_id, odbiorca_id, numer_konta_nadawcy, numer_konta_odbiorcy,
                    kwota, typ, data_transakcji, opis
                ) VALUES (
                    :nadawca_id, :odbiorca_id, :numer_nadawcy, :numer_odbiorcy,
                    :kwota, 'przelew', NOW(), :opis
                )
            ");
      $stmt->execute([
        'nadawca_id' => $_SESSION['konto_id'],
        'odbiorca_id' => $recipient['konto_id'],
        'numer_nadawcy' => $_SESSION['konto_id'],
        'numer_odbiorcy' => $recipientAccount,
        'kwota' => $amount,
        'opis' => $desc
      ]);

      $pdo->commit();
      $_SESSION['saldo'] -= $amount;
      $communicate = "Przelew na {$amount} PLN wykonany pomyślnie.";
    }
  }
}
?>
<!DOCTYPE html>
<html lang="pl">

<head>
  <meta charset="UTF-8">
  <title>Przelew | Bank Online</title>
  <link rel="stylesheet" href="style.css?v=3">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
  <a href="main.php" class="back-arrow">
    <i class='bx bx-arrow-back'></i>
  </a>
  <div class="centered-wrapper">
    <div class="container">
      <h2 class="text-center">Wykonaj przelew</h2>

      <?php if (!empty($communicate)): ?>
        <div class="alert alert-info mt-3">
          <?= htmlspecialchars($communicate) ?>
        </div>
      <?php endif; ?>

      <form method="post" class="text-center">
        <div class="mb-3 text-start w-100" style="max-width: 400px; margin: auto;">
          <label for="recipient_name" class="form-label">Imię odbiorcy:</label>
          <input type="text" name="recipient_name" id="recipient_name" required>
        </div>

        <div class="mb-3 text-start w-100" style="max-width: 400px; margin: auto;">
          <label for="recipient_surname" class="form-label">Nazwisko odbiorcy:</label>
          <input type="text" name="recipient_surname" id="recipient_surname" required>
        </div>

        <div class="mb-3 text-start w-100" style="max-width: 400px; margin: auto;">
          <label for="recipient" class="form-label">Numer konta odbiorcy:</label>
          <input type="text" name="recipient" id="recipient" maxlength="26" pattern="\d{26}" required>
        </div>

        <div class="mb-3 text-start w-100" style="max-width: 400px; margin: auto;">
          <label for="amount" class="form-label">Kwota (PLN):</label>
          <input type="number" name="amount" id="amount" step="0.01" min="0.01" required>
        </div>

        <div class="mb-3 text-start w-100" style="max-width: 400px; margin: auto;">
          <label for="description" class="form-label">Opis (opcjonalnie):</label>
          <textarea name="description" id="description" rows="3" class="form-control"></textarea>
        </div>

        <div class="text-center">
          <button type="submit" name="transfer" class="btn-success">Wykonaj przelew</button>
        </div>
      </form>
    </div>
  </div>
</body>

</html>