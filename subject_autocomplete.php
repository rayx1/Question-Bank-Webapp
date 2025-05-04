<?php
// subject_autocomplete.php
include 'config.php';

$query = "SELECT DISTINCT subject FROM questions ORDER BY subject ASC";
$result = $conn->query($query);

$suggestions = array();
if($result && $result->num_rows > 0){
    while ($row = $result->fetch_assoc()) {
        $suggestions[] = $row['subject'];
    }
}
// Return JSON encoded list of subjects
echo json_encode($suggestions);
?>
