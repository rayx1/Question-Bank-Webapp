<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include '../config.php';

$sql = "SELECT * FROM questions ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS CDN -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>
  <div class="container mt-4">
    <h2 class="text-center">Admin Dashboard</h2>
    <p class="text-center">Welcome, <?php echo htmlspecialchars($_SESSION['admin']); ?></p>
    <div class="text-center mb-3">
      <a href="add_question.php" class="btn btn-success">Add New Question</a>
      <a href="logout.php" class="btn btn-danger">Logout</a>
    </div>
    <?php if ($result && $result->num_rows > 0) : ?>
      <div class="table-responsive">
        <table class="table table-bordered">
          <thead class="thead-light">
            <tr>
              <th>ID</th>
              <th>Subject</th>
              <th>Year</th>
              <th>Question</th>
              <th>Answer</th>
              <th>File</th>
              <th>Probability (%)</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()) : ?>
            <tr>
              <td><?php echo $row['id']; ?></td>
              <td><?php echo htmlspecialchars($row['subject']); ?></td>
              <td><?php echo htmlspecialchars($row['year']); ?></td>
              <td><?php echo htmlspecialchars($row['question']); ?></td>
              <td><?php echo htmlspecialchars($row['answer']); ?></td>
              <td>
                <?php if ($row['file_path']) : ?>
                  <a href="../uploads/<?php echo htmlspecialchars($row['file_path']); ?>" target="_blank">View File</a>
                <?php else : ?>
                  N/A
                <?php endif; ?>
              </td>
              <td><?php echo htmlspecialchars($row['probability']); ?>%</td>
              <td>
                <a href="edit_question.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-info">Edit</a>
                <a href="delete_question.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this question?');">Delete</a>
              </td>
            </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php else : ?>
      <div class="alert alert-info text-center">No questions found.</div>
    <?php endif; ?>
  </div>
  <!-- Bootstrap JS and dependencies -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
