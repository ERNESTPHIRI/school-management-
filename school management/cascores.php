<?php 
require_once 'db/db.php'; // Include the database connection

$db = getDBConnection(); // Get the database connection

if (!$db) {
    die("Database connection failed.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $regNo = $_POST['regNo'];
    $academicYr = $_POST['academicYr'];
    $term = $_POST['term'];
    $form = $_POST['form'];
    $subject = $_POST['subject'];
    $score = $_POST['score'];

    // Determine the appropriate cascores table based on form
    $cascoresTable = 'cascores' . substr($form, 1); // cascores1, cascores2, etc.

    // Prepare the SQL query to insert the score
    $stmt = $db->prepare("INSERT INTO {$cascoresTable} (student_id, academic_year, term, score_{$subject}) VALUES ((SELECT id FROM {$form}Student WHERE regNo = :regNo), :academicYr, :term, :score)");
    $stmt->bindValue(':regNo', $regNo, SQLITE3_TEXT);
    $stmt->bindValue(':academicYr', $academicYr, SQLITE3_TEXT);
    $stmt->bindValue(':term', $term, SQLITE3_TEXT);
    $stmt->bindValue(':score', $score, SQLITE3_FLOAT);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Score submitted successfully.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to submit the score.']);
    }
}
?>
