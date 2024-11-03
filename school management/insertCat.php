<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'db/db.php'; // Include the database connection

$db = getDBConnection(); // Get the database connection

if (!$db) {
    die("Database connection failed.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the posted data
    $academicYr = $_POST['academicYr'];
    $term = $_POST['term'];
    $form = $_POST['form']; // e.g., "Form 1", "Form 2", etc.
    $regNo = $_POST['regNo'];
    $subject = $_POST['subject'];
    $score = $_POST['score'];

    // Determine the correct casores table based on the form
    $casoresTable = "cascores" . substr($form, -1); // e.g., "casores1" for "Form 1"
    
    // Prepare the column map for the subjects
    $columnMap = [
        'Agriculture' => 'score_agriculture',
        'Additional Mathematics' => 'score_additional_math',
        'Biology' => 'score_biology',
        'Business Studies' => 'score_business_studies',
        'Computer Studies' => 'score_computer_studies',
        'Chichewa' => 'score_chichewa',
        'Chemistry' => 'score_chemistry',
        'English' => 'score_english',
        'Geography' => 'score_geography',
        'History' => 'score_history',
        'Life Skills' => 'score_life_skills',
        'Mathematics' => 'score_mathematics',
        'Social Studies' => 'score_social_studies',
        'Physics' => 'score_physics',
        'Home Economics' => 'score_home_economics'
    ];

    // Validate the subject
    if (!array_key_exists($subject, $columnMap)) {
        die("Invalid subject.");
    }

    // Check if the student already exists in the casores table
    $checkQuery = "SELECT * FROM $casoresTable WHERE student_id = :regNo AND academic_year = :academicYr AND term = :term";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindValue(':regNo', $regNo, SQLITE3_TEXT);
    $checkStmt->bindValue(':academicYr', $academicYr, SQLITE3_TEXT);
    $checkStmt->bindValue(':term', $term, SQLITE3_TEXT);

    $result = $checkStmt->execute();
    $existingRecord = $result->fetchArray(SQLITE3_ASSOC);

    if ($existingRecord) {
        // If the record exists, update the score for the given subject
        $updateQuery = "UPDATE $casoresTable 
                        SET {$columnMap[$subject]} = :score 
                        WHERE student_id = :regNo AND academic_year = :academicYr AND term = :term";
        
        $updateStmt = $db->prepare($updateQuery);
        $updateStmt->bindValue(':regNo', $regNo, SQLITE3_TEXT);
        $updateStmt->bindValue(':academicYr', $academicYr, SQLITE3_TEXT);
        $updateStmt->bindValue(':term', $term, SQLITE3_TEXT);
        $updateStmt->bindValue(':score', $score, SQLITE3_FLOAT);

        if ($updateStmt->execute()) {
            echo "<script>
                    alert('Score updated successfully.');
                    window.location.href = 'casores.html'; // Redirect to casores.html
                  </script>";
        } else {
            echo "Error updating score: " . $updateStmt->lastErrorMsg();
        }
    } else {
        // If the record does not exist, insert a new record
        $insertQuery = "INSERT INTO $cascoresTable (student_id, academic_year, term, {$columnMap[$subject]}) 
                        VALUES (:regNo, :academicYr, :term, :score)";

        $insertStmt = $db->prepare($insertQuery);
        $insertStmt->bindValue(':regNo', $regNo, SQLITE3_TEXT);
        $insertStmt->bindValue(':academicYr', $academicYr, SQLITE3_TEXT);
        $insertStmt->bindValue(':term', $term, SQLITE3_TEXT);
        $insertStmt->bindValue(':score', $score, SQLITE3_FLOAT);

        if ($insertStmt->execute()) {
            echo "<script>
                    alert('Score inserted successfully.');
                    window.location.href = 'cascores.html'; // Redirect to cascores.html
                  </script>";
        } else {
            echo "Error inserting score: " . $insertStmt->lastErrorMsg();
        }
    }
} else {
    echo "Invalid request method.";
}
?>
