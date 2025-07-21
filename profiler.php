<?php
// passphrase_profiler_en.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = trim($_POST['phrases'] ?? '');
    $phrases = array_filter(array_map('trim', explode("\n", $input)));

    function analyzePhrase($phrase) {
        $length = strlen($phrase);
        $hasUpper = preg_match('/[A-Z]/', $phrase);
        $hasLower = preg_match('/[a-z]/', $phrase);
        $hasDigit = preg_match('/\d/', $phrase);
        $hasSymbol = preg_match('/[\W_]/', $phrase);
        $entropy = round(log(pow(94, $length), 2), 2); // 94 printable ASCII characters
        $hasYear = preg_match('/(19|20)\d{2}/', $phrase);
        $keyboardPatterns = preg_match('/qwert|asdf|1234|abcd/i', $phrase);

        return [
            'phrase' => $phrase,
            'length' => $length,
            'has_uppercase' => $hasUpper,
            'has_lowercase' => $hasLower,
            'has_digit' => $hasDigit,
            'has_symbol' => $hasSymbol,
            'estimated_entropy_bits' => $entropy,
            'contains_year' => $hasYear,
            'keyboard_pattern_detected' => $keyboardPatterns
        ];
    }

    $results = array_map('analyzePhrase', $phrases);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Passphrase Profiling Tool</title>
  <style>
    body { font-family: sans-serif; background: #111; color: #eee; padding: 2rem; }
    textarea { width: 100%; height: 150px; background: #222; color: #0f0; font-family: monospace; }
    table { width: 100%; border-collapse: collapse; margin-top: 2rem; }
    th, td { border: 1px solid #333; padding: 8px; text-align: left; }
    th { background: #333; }
    .highlight { color: #f33; font-weight: bold; }
    .success { color: #0f0; }
  </style>
</head>
<body>

  <h1>🔐 Passphrase Profiling Tool</h1>
  <form method="POST">
    <label for="phrases">Enter multiple passphrases (one per line):</label><br>
    <textarea name="phrases" placeholder="e.g. Hello2024!&#10; Hello2023!"></textarea><br><br>
    <button type="submit">Analyze</button>
  </form>

<?php if (!empty($results)): ?>
  <h2>🧠 Analysis Results</h2>
  <table>
    <tr>
      <th>Passphrase</th>
      <th>Length</th>
      <th>A-Z</th>
      <th>a-z</th>
      <th>0-9</th>
      <th>Symbol</th>
      <th>Entropy (Bits)</th>
      <th>Year Detected</th>
      <th>Keyboard Pattern</th>
    </tr>
    <?php foreach ($results as $r): ?>
    <tr>
      <td><?= htmlspecialchars($r['phrase']) ?></td>
      <td><?= $r['length'] ?></td>
      <td><?= $r['has_uppercase'] ? '✔' : '✖' ?></td>
      <td><?= $r['has_lowercase'] ? '✔' : '✖' ?></td>
      <td><?= $r['has_digit'] ? '✔' : '✖' ?></td>
      <td><?= $r['has_symbol'] ? '✔' : '✖' ?></td>
      <td><?= $r['estimated_entropy_bits'] ?></td>
      <td class="<?= $r['contains_year'] ? 'highlight' : 'success' ?>"><?= $r['contains_year'] ? '✔' : '✖' ?></td>
      <td class="<?= $r['keyboard_pattern_detected'] ? 'highlight' : 'success' ?>"><?= $r['keyboard_pattern_detected'] ? '✔' : '✖' ?></td>
    </tr>
    <?php endforeach; ?>
  </table>

  <h3>📊 JSON Profile (Raw Data)</h3>
  <pre><?= json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?></pre>
<?php endif; ?>

</body>
</html>
