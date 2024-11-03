<?php
// Include the database connection
include('db/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $form = $_POST['form'];
    $term = $_POST['term'];
    $academicYr = $_POST['academicYr'];

    // Determine the table name based on the form
    $tableName = '';
    switch ($form) {
        case 'Form 1':
            $tableName = 'f1Student';
            break;
        case 'Form 2':
            $tableName = 'f2Student';
            break;
        case 'Form 3':
            $tableName = 'f3Student';
            break;
        case 'Form 4':
            $tableName = 'f4Student';
            break;
        default:
            echo json_encode(['error' => 'Invalid form selected']);
            exit;
    }

    // Get the database connection
    $conn = getDBConnection();

    if ($conn) {
        // Prepare the query
        $stmt = $conn->prepare("SELECT regNo, fullName, dob, gender, phone, guardian, address1, address2, form, term, academicYr 
                                FROM {$tableName} 
                                WHERE form = :form AND term = :term AND academicYr = :academicYr");
        $stmt->bindValue(':form', $form, SQLITE3_TEXT);
        $stmt->bindValue(':term', $term, SQLITE3_TEXT);
        $stmt->bindValue(':academicYr', $academicYr, SQLITE3_TEXT);

        // Execute the query
        $result = $stmt->execute();
        if ($result === false) {
            echo json_encode(['error' => 'Query failed: ' . $conn->lastErrorMsg()]);
            exit;
        }

        // Fetch the results and format them into an array
        $students = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $students[] = $row;
        }

        // Check if students are found
        if (count($students) > 0) {
            // Return the result as JSON
            echo json_encode($students);
        } else {
            echo json_encode(['error' => 'No students found for the selected criteria']);
        }

        // Close the statement and connection
        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(['error' => 'Database connection failed']);
    }
}
?>
