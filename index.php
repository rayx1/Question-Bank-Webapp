<?php
include 'config.php';

$message = "";
$results = null;

// Retrieve search parameters (GET request)
$subject = isset($_GET['subject']) ? trim($_GET['subject']) : "";
$year = isset($_GET['year']) ? trim($_GET['year']) : "";
$minProbability = isset($_GET['minProbability']) ? (float)$_GET['minProbability'] : 0;

if ($_SERVER["REQUEST_METHOD"] == "GET" && empty($subject)) {
    $message = "Subject is mandatory for search.";
} elseif ($_SERVER["REQUEST_METHOD"] == "GET" && !empty($subject)) {
    $subject_safe = $conn->real_escape_string($subject);
    $where = "WHERE subject = '$subject_safe'";
    
    if (!empty($year)) {
        $year_safe = $conn->real_escape_string($year);
        $where .= " AND year = '$year_safe'";
    }
    
    if ($minProbability > 0) {
        $where .= " AND probability >= " . (float)$minProbability;
    }
    
    $sql = "SELECT * FROM questions $where ORDER BY probability DESC";
    $results = $conn->query($sql);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Question Bank - Search</title>
  <!-- Bootstrap CSS -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>
  <div class="container mt-4">
    <h1 class="text-center">Question Bank Search</h1>
    
    <form class="form-inline justify-content-center mb-4" method="GET" action="index.php">
      <div class="form-group mx-2">
        <label for="subject" class="mr-2">Subject (Required):</label>
        <input type="text" class="form-control" id="subject" name="subject" value="<?php echo htmlspecialchars($subject); ?>" required>
      </div>
      <div class="form-group mx-2">
        <label for="year" class="mr-2">Year:</label>
        <input type="text" class="form-control" id="year" name="year" value="<?php echo htmlspecialchars($year); ?>">
      </div>
      <div class="form-group mx-2">
        <label for="minProbability" class="mr-2">Min. Importance (%):</label>
        <input type="number" class="form-control" id="minProbability" name="minProbability" step="0.1" min="0" max="100" value="<?php echo htmlspecialchars($minProbability); ?>">
      </div>
      <button type="submit" class="btn btn-primary mx-2">Search</button>
    </form>
    
    <?php if (!empty($message)) : ?>
      <div class="alert alert-warning text-center"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    
    <?php if ($results && $results->num_rows > 0) : ?>
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead class="thead-light">
            <tr>
              <th>ID</th>
              <th>Subject</th>
              <th>Year</th>
              <th>Question</th>
              <th>Answer</th>
              <th>Probability (%)</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $results->fetch_assoc()) : ?>
              <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['subject']); ?></td>
                <td><?php echo htmlspecialchars($row['year']); ?></td>
                <td><?php echo htmlspecialchars($row['question']); ?></td>
                <td><?php echo htmlspecialchars($row['answer']); ?></td>
                <td><?php echo htmlspecialchars($row['probability']); ?>%</td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php elseif ($_SERVER["REQUEST_METHOD"] == "GET" && empty($message)) : ?>
      <div class="alert alert-info text-center">No results found.</div>
    <?php endif; ?>
  </div>

  <!-- Bootstrap JS and dependencies -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
