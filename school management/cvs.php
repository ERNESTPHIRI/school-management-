<?php
// Connect to the SQLite database
require_once('db/db.php');
$db = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $academicYr = $_POST['academicYr'];
    $form = $_POST['form'];
    $term = 'Term ' . $_POST['term'];  // Fix to match "Term 1", "Term 2", etc.
    $subject = $_POST['subject'];

    // Determine which cascore table to use based on the selected form
    switch ($form) {
        case 'Form 1':
            $table = 'cascore1';
            $studentTable = 'f1Student';
            break;
        case 'Form 2':
            $table = 'cascore2';
            $studentTable = 'f2Student';
            break;
        case 'Form 3':
            $table = 'cascore3';
            $studentTable = 'f3Student';
            break;
        case 'Form 4':
            $table = 'cascore4';
            $studentTable = 'f4Student';
            break;
        default:
            echo json_encode(['error' => 'Invalid form selected']);
            exit;
    }

    // Query to retrieve student scores from the corresponding cascore table
    $query = "
        SELECT f.regNo, f.fullName, c.$subject AS score
        FROM $table c
        JOIN $studentTable f ON f.rowid = c.student_id
        WHERE c.academic_year = :academicYr AND c.term = :term AND c.$subject IS NOT NULL
    ";

    $stmt = $db->prepare($query);
    $stmt->bindValue(':academicYr', $academicYr, SQLITE3_TEXT);
    $stmt->bindValue(':term', $term, SQLITE3_TEXT);

    $result = $stmt->execute();
    $data = [];

    if ($result) {
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $data[] = $row;
        }
    }

    if (count($data) > 0) {
        echo json_encode($data);
    } else {
        echo json_encode(['error' => 'No data found for the selected criteria']);
    }
}
?>
