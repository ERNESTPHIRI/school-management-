<?php
require_once 'db/db.php'; // Include your database connection script

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $className = $_POST['className'] ?? '';
    $academicYr = $_POST['academicYr'] ?? '';
    $term = $_POST['term'] ?? '';
    $formTeacher = $_POST['formTeacher'] ?? '';

    // Process uploaded images
    $FTSign = file_get_contents($_FILES['FTSign']['tmp_name']);

    // Insert data into formClass table
    $db = getDBConnection();
    if ($db) {
        $query = "INSERT INTO formClass (name, academicYr, term, FormTeacher, FTSign) 
                  VALUES (:name, :academicYr, :term, :FormTeacher, :FTSign)";
        
        $stmt = $db->prepare($query);
        $stmt->bindValue(':name', $className, SQLITE3_TEXT);
        $stmt->bindValue(':academicYr', $academicYr, SQLITE3_TEXT);
        $stmt->bindValue(':term', $term, SQLITE3_TEXT);
        $stmt->bindValue(':FormTeacher', $formTeacher, SQLITE3_TEXT);
        $stmt->bindValue(':FTSign', $FTSign, SQLITE3_BLOB);
        
        if ($stmt->execute()) {
            echo "<p>Class details added successfully!</p>";
        } else {
            echo "<p>Error adding class details.</p>";
        }
    } else {
        echo "<p>Database connection failed.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Details</title>
    <link rel="stylesheet" href="css/class_details.css">
</head>
<body>
    <div class="container">
        <h1>Enter Class Details</h1>
        <form action="class_details.php" method="POST" enctype="multipart/form-data">
            <label for="className">Class Name:</label>
            <select id="className" name="className" required>
                <option value="Form 1">Form 1</option>
                <option value="Form 2">Form 2</option>
                <option value="Form 3">Form 3</option>
                <option value="Form 4">Form 4</option>
            </select>

            <label for="academicYr">Academic Year:</label>
            <select id="academicYr" name="academicYr" required>
                <option value="2024-2025">2024-2025</option>
                <option value="2025-2026">2025-2026</option>
                <option value="2026-2027">2026-2027</option>
                <option value="2027-2028">2027-2028</option>
                <option value="2028-2029">2028-2029</option>
                <option value="2029-2030">2029-2030</option>
            </select>

            <label for="term">Term:</label>
            <select id="term" name="term" required>
                <option value="Term 1">Term 1</option>
                <option value="Term 2">Term 2</option>
                <option value="Term 3">Term 3</option>
            </select>

            <label for="formTeacher">Form Teacher's Name:</label>
            <input type="text" id="formTeacher" name="formTeacher" required>

            <label for="FTSign">Form Teacher's Signature:</label>
            <input type="file" id="FTSign" name="FTSign" accept="image/*" required>

            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>
