<h2 class="text-center">Wykonaj przelew</h2>
<form method="post" class="text-center">
  <div class="mb-3 text-start w-100" style="max-width: 400px; margin: auto;">
    <label for="recipient" class="form-label">Odbiorca:</label>
    <input type="text" name="recipient" required>
  </div>

  <div class="mb-3 text-start w-100" style="max-width: 400px; margin: auto;">
    <label for="amount" class="form-label">Kwota:</label>
    <input type="number" name="amount" step="0.01" required>
  </div>

  <button type="submit" name="transfer" class="btn-success">Wykonaj przelew</button>
</form>
