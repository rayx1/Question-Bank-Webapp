<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}
include '../config.php';
include '../functions.php';

// Check if 'id' is provided in the URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("No question ID provided.");
}

$id = (int)$_GET['id'];

// Fetch the question information from the database
$sql = "SELECT * FROM questions WHERE id = $id LIMIT 1";
$result = $conn->query($sql);
if ($result->num_rows == 0) {
    die("Question not found.");
}
$questionRow = $result->fetch_assoc();

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve input fields
    $subject  = $conn->real_escape_string($_POST['subject']);
    $year     = $conn->real_escape_string($_POST['year']);
    $question = $_POST['question']; // Raw text for keyword extraction
    $answer   = $conn->real_escape_string($_POST['answer']);
    
    // File upload handling
    $file_path = $questionRow['file_path']; // Default: use existing file
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
                $error = "Failed to upload the new file.";
            }
        } else { 
            $error = "Invalid file type. Allowed types: PDF, JPG, JPEG, PNG.";
        }
    }
    
    // Re-calculate probability based on updated question details
    $calculatedProbability = calculateAutoProbabilityRecency($question, $subject, $year, $conn);
    
    $questionEscaped = $conn->real_escape_string($question);
    $sqlUpdate = "UPDATE questions SET 
                    subject = '$subject', 
                    year = '$year', 
                    question = '$questionEscaped', 
                    answer = '$answer', 
                    file_path = '$file_path', 
                    probability = '$calculatedProbability'
                  WHERE id = $id";
    
    if ($conn->query($sqlUpdate) === TRUE) {
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Error updating record: " . $conn->error;
    }
    
    // Optionally, re-fetch the updated row (if you want to display it again)
    $result = $conn->query("SELECT * FROM questions WHERE id = $id LIMIT 1");
    $questionRow = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Question</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS CDN -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
  <!-- jQuery UI CSS for Autocomplete -->
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
</head>
<body>
  <div class="container mt-4">
    <h2 class="text-center">Edit Question</h2>
    <?php if (isset($error)) : ?>
      <div class="alert alert-danger text-center"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    <form method="POST" action="edit_question.php?id=<?php echo $id; ?>" enctype="multipart/form-data">
      <div class="form-group">
        <label>Subject:</label>
        <input type="text" id="subject" class="form-control" name="subject" value="<?php echo htmlspecialchars($questionRow['subject']); ?>" required>
      </div>
      <div class="form-group">
        <label>Year:</label>
        <input type="text" class="form-control" name="year" value="<?php echo htmlspecialchars($questionRow['year']); ?>" required>
      </div>
      <div class="form-group">
        <label>Question:</label>
        <textarea class="form-control" name="question" rows="5" required><?php echo htmlspecialchars($questionRow['question']); ?></textarea>
      </div>
      <div class="form-group">
        <label>Answer:</label>
        <textarea class="form-control" name="answer" rows="5" required><?php echo htmlspecialchars($questionRow['answer']); ?></textarea>
      </div>
      <div class="form-group">
        <label>Current File:</label>
        <?php if ($questionRow['file_path']) : ?>
          <a href="../uploads/<?php echo htmlspecialchars($questionRow['file_path']); ?>" target="_blank">View File</a>
        <?php else : ?>
          N/A
        <?php endif; ?>
      </div>
      <div class="form-group">
        <label>Upload New PDF/Image (optional, to replace current):</label>
        <input type="file" class="form-control-file" name="upload">
      </div>
      <button type="submit" class="btn btn-primary">Update Question</button>
      <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </form>
  </div>
  <!-- jQuery, jQuery UI, and Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Initialize jQuery UI Autocomplete on the Subject field -->
  <script>
    $(document).ready(function(){
      $("#subject").autocomplete({
        source: "../subject_autocomplete.php",
        minLength: 1
      });
    });
  </script>
</body>
</html>
