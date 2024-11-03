<?php
require 'db/db.php';

$db = getDBConnection(); // Use the database connection function

if ($db === null) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];

    if ($action === 'get_student') {
        $academic_year = $_POST['academic_year'];
        $form = $_POST['form'];
        $term = $_POST['term'];
        $regNo = $_POST['regNo'];

        $student_table = getStudentTable($form);

        $stmt = $db->prepare("SELECT fullName FROM $student_table WHERE regNo = :regNo AND academicYr = :academic_year AND term = :term");
        $stmt->bindValue(':regNo', $regNo);
        $stmt->bindValue(':academic_year', $academic_year);
        $stmt->bindValue(':term', $term);
        $result = $stmt->execute();

        if ($row = $result->fetchArray()) {
            echo json_encode(['status' => 'success', 'fullName' => $row['fullName']]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Student not found']);
        }

    } elseif ($action === 'submit_score') {
        $academic_year = $_POST['academic_year'];
        $form = $_POST['form'];
        $term = $_POST['term'];
        $regNo = $_POST['regNo'];
        $subject = $_POST['subject'];
        $score = $_POST['score'];

        $student_table = getStudentTable($form);
        $cascore_table = getCascoreTable($form);

        // Check if student exists in cascore table
        $stmt = $db->prepare("SELECT id FROM $cascore_table WHERE student_id = (SELECT rowid FROM $student_table WHERE regNo = :regNo) AND academic_year = :academic_year AND term = :term");
        $stmt->bindValue(':regNo', $regNo);
        $stmt->bindValue(':academic_year', $academic_year);
        $stmt->bindValue(':term', $term);
        $result = $stmt->execute();

        if ($row = $result->fetchArray()) {
            // Update existing score
            $stmt = $db->prepare("UPDATE $cascore_table SET $subject = :score WHERE id = :id");
            $stmt->bindValue(':score', $score);
            $stmt->bindValue(':id', $row['id']);
            $stmt->execute();
            echo json_encode(['status' => 'success', 'message' => 'Score updated']);
        } else {
            // Insert new score
            $stmt = $db->prepare("INSERT INTO $cascore_table (student_id, academic_year, term, $subject) VALUES ((SELECT rowid FROM $student_table WHERE regNo = :regNo), :academic_year, :term, :score)");
            $stmt->bindValue(':regNo', $regNo);
            $stmt->bindValue(':academic_year', $academic_year);
            $stmt->bindValue(':term', $term);
            $stmt->bindValue(':score', $score);
            $stmt->execute();
            echo json_encode(['status' => 'success', 'message' => 'Score inserted']);
        }
    }
}

function getStudentTable($form) {
    switch ($form) {
        case 'Form 1':
            return 'f1Student';
        case 'Form 2':
            return 'f2Student';
        case 'Form 3':
            return 'f3Student';
        case 'Form 4':
            return 'f4Student';
        default:
            throw new Exception('Invalid form selected');
    }
}

function getCascoreTable($form) {
    switch ($form) {
        case 'Form 1':
            return 'cascore1';
        case 'Form 2':
            return 'cascore2';
        case 'Form 3':
            return 'cascore3';
        case 'Form 4':
            return 'cascore4';
        default:
            throw new Exception('Invalid form selected');
    }
}
?>
