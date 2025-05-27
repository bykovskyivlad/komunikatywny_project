<?php
session_start();


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact'])) {
    $imie = $_POST['firstName'] ?? '';
    $nazwisko = $_POST['lastName'] ?? '';
    $email = $_POST['email'] ?? '';
    $temat = $_POST['subject'] ?? '';
    $innyTemat = $_POST['otherSubject'] ?? '';
    $wiadomosc = $_POST['message'] ?? '';

    $tematFinalny = ($temat === 'Inne' && !empty($innyTemat)) ? $innyTemat : $temat;

    $_SESSION['communikat'] = "Dziękujemy za wiadomość, $imie!";
    header("Location: main.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8">
  <title>Formularz kontaktowy</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">

  <div  class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <form class="bg-white p-4 shadow rounded" method="post">
          <h2 class="mb-4 text-center">Skontaktuj się z nami</h2>

          <div class="row mb-3">
            <div class="col">
              <label for="firstName" class="form-label">Imię</label>
              <input type="text" class="form-control" name="firstName" id="firstName" required>
            </div>
            <div class="col">
              <label for="lastName" class="form-label">Nazwisko</label>
              <input type="text" class="form-control" name="lastName" id="lastName" required>
            </div>
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">Adres e-mail</label>
            <input type="email" class="form-control" name="email" id="email" required>
          </div>

          <div class="mb-3">
            <label for="subject" class="form-label">Temat</label>
            <select class="form-select" name="subject" id="subject" required onchange="toggleOtherSubject(this)">
              <option value="" disabled selected>Wybierz temat...</option>
              <option value="Błąd">Błąd</option>
              <option value="Problemy z logowaniem">Problemy z logowaniem</option>
              <option value="Cyberatak">Cyberatak</option>
              <option value="Współpraca">Współpraca</option>
              <option value="Inne">Inne</option>
            </select>
          </div>

          <div class="mb-3 d-none" id="otherSubjectContainer">
            <label for="otherSubject" class="form-label">Inny temat</label>
            <input type="text" class="form-control" name="otherSubject" id="otherSubject">
          </div>

          <div class="mb-3">
            <label for="message" class="form-label">Wiadomość</label>
            <textarea name="message" class="form-control" id="message" rows="5" required></textarea>
          </div>

          <div class="d-flex flex-column align-items-center">
            <button type="submit" class="btn btn-success w-75 mb-2" name="contact">Wyślij wiadomość</button>
            <a href="main.php" class="btn btn-primary w-50">Powrót</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  <script>
    function toggleOtherSubject(select) {
      const container = document.getElementById('otherSubjectContainer');
      if (select.value === 'Inne') {
        container.classList.remove('d-none');
        container.querySelector('input').setAttribute('required', 'required');
      } else {
        container.classList.add('d-none');
        container.querySelector('input').removeAttribute('required');
      }
    }
  </script>

</body>
</html>