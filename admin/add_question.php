<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include '../config.php';
include '../functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve input fields
    $subject  = $conn->real_escape_string($_POST['subject']);
    $year     = $conn->real_escape_string($_POST['year']);
    $question = $_POST['question'];  // Raw text for keyword extraction
    $answer   = $conn->real_escape_string($_POST['answer']);
    
    // File upload handling (if a file is provided)
    $file_path = "";
    if (isset($_FILES['upload']) && $_FILES['upload']['error'] === 0) {
         $allowedExt = array('pdf', 'jpg', 'jpeg', 'png');
         $fileName   = $_FILES['upload']['name'];
         $fileTmpName= $_FILES['upload']['tmp_name'];
         $fileExt    = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
         
         if (in_array($fileExt, $allowedExt)) {
             $upload_dir = "../uploads/";
             if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
             }
             $newFileName = uniqid() . "." . $fileExt;
             if (move_uploaded_file($fileTmpName, $upload_dir . $newFileName)) {
                  $file_path = $newFileName;
             } else {
                  $error = "Failed to upload the file.";
             }
         } else { 
               $error = "Invalid file type. Allowed types: PDF, JPG, JPEG, PNG.";
         }
    }
    
    // Auto-calculate probability using the advanced function from functions.php.
    $calculatedProbability = calculateAutoProbabilityRecency($question, $subject, $year, $conn);
    
    // Escape question text for safe SQL insertion.
    $questionEscaped = $conn->real_escape_string($question);
    $sql = "INSERT INTO questions (subject, year, question, answer, file_path, probability)
            VALUES ('$subject', '$year', '$questionEscaped', '$answer', '$file_path', '$calculatedProbability')";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add New Question</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS CDN -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
</head>
<body>
  <div class="container mt-4">
    <h2 class="text-center">Add New Question</h2>
    <?php if (isset($error)) : ?>
      <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form method="POST" action="add_question.php" enctype="multipart/form-data">
      <div class="form-group">
        <label>Subject:</label>
        <input type="text" class="form-control" name="subject" required>
      </div>
      <div class="form-group">
        <label>Year:</label>
        <input type="text" class="form-control" name="year" required>
      </div>
      <div class="form-group">
        <label>Question:</label>
        <textarea class="form-control" name="question" rows="5" required></textarea>
      </div>
      <div class="form-group">
        <label>Answer:</label>
        <textarea class="form-control" name="answer" rows="5" required></textarea>
      </div>
      <div class="form-group">
        <label>Upload PDF/Image (optional):</label>
        <input type="file" class="form-control-file" name="upload">
      </div>
      <button type="submit" class="btn btn-primary">Add Question</button>
      <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </form>
  </div>
  <!-- Bootstrap JS and dependencies -->
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
