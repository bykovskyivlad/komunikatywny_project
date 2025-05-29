<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['transfer'])) {
    $recipientAccount = $_POST['recipient'];
    $amount = (float) $_POST['amount'];

    if ($amount <= 0) {
        $communicate = "Kwota przelewu musi być większa niż 0.";
    } else {
        // Pobierz konto odbiorcy
        $stmt = $pdo->prepare("SELECT konto_id, saldo FROM konta WHERE numer_konta = :numer_konta");
        $stmt->execute(['numer_konta' => $recipientAccount]);
        $recipient = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$recipient) {
            $communicate = "Nie znaleziono odbiorcy o podanym numerze konta.";
        } elseif ($amount > $_SESSION['saldo']) {
            $communicate = "Brak wystarczających środków na koncie.";
        } else {
            // Odejmij środki z konta nadawcy
            $stmt = $pdo->prepare("UPDATE konta SET saldo = saldo - :kwota WHERE konto_id = :konto_id");
            $stmt->execute([
                'kwota' => $amount,
                'konto_id' => $_SESSION['konto_id']
            ]);

            // Dodaj środki do konta odbiorcy
            $stmt = $pdo->prepare("UPDATE konta SET saldo = saldo + :kwota WHERE konto_id = :konto_id");
            $stmt->execute([
                'kwota' => $amount,
                'konto_id' => $recipient['konto_id']
            ]);

            // Zapisz transakcję w historii
            $stmt = $pdo->prepare("
                INSERT INTO historia_transakcji (
                    nadawca_id, odbiorca_id, numer_konta_nadawcy, numer_konta_odbiorcy,
                    kwota, typ, data_transakcji, opis
                ) VALUES (
                    :nadawca_id, :odbiorca_id, :numer_nadawcy, :numer_odbiorcy,
                    :kwota, 'przelew', NOW(), 'Przelew online'
                )
            ");
            $stmt->execute([
                'nadawca_id' => $_SESSION['konto_id'],
                'odbiorca_id' => $recipient['konto_id'],
                'numer_nadawcy' => $_SESSION['konto_id'],
                'numer_odbiorcy' => $recipientAccount,
                'kwota' => $amount
            ]);

            $_SESSION['saldo'] -= $amount;
            $communicate = "Przelew na {$amount} PLN wykonany pomyślnie.";
        }
    }
}
?>

<h2 class="text-center">Wykonaj przelew</h2>
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
