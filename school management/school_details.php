<?php
require_once 'db/db.php'; // Include your database connection script

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $address = $_POST['address'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $email = $_POST['email'] ?? null;
    $headTeacher = $_POST['headTeacher'] ?? '';

    // Process uploaded images
    $logo = file_get_contents($_FILES['logo']['tmp_name']);
    $stamp = file_get_contents($_FILES['stamp']['tmp_name']);
    $HDSign = file_get_contents($_FILES['HDSign']['tmp_name']);

    // Insert data into Xull table
    $db = getDBConnection();
    if ($db) {
        $query = "INSERT INTO Xull (name, address, logo, phone, email, stamp, headTeacher, HDSign) 
                  VALUES (:name, :address, :logo, :phone, :email, :stamp, :headTeacher, :HDSign)";
        
        $stmt = $db->prepare($query);
        $stmt->bindValue(':name', $name, SQLITE3_TEXT);
        $stmt->bindValue(':address', $address, SQLITE3_TEXT);
        $stmt->bindValue(':logo', $logo, SQLITE3_BLOB);
        $stmt->bindValue(':phone', $phone, SQLITE3_TEXT);
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $stmt->bindValue(':stamp', $stamp, SQLITE3_BLOB);
        $stmt->bindValue(':headTeacher', $headTeacher, SQLITE3_TEXT);
        $stmt->bindValue(':HDSign', $HDSign, SQLITE3_BLOB);
        
        if ($stmt->execute()) {
            echo "<p>School details added successfully!</p>";
        } else {
            echo "<p>Error adding school details.</p>";
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
    <title>School Details</title>
    <link rel="stylesheet" href="css/school_details.css">
</head>
<body>
    <div class="container">
        <h1>Enter School Details</h1>
        <form action="school_details.php" method="POST" enctype="multipart/form-data">
            <label for="name">School Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="address">Address:</label>
            <textarea id="address" name="address" required></textarea>

            <label for="phone">Phone:</label>
            <input type="text" id="phone" name="phone" required>

            <label for="email">Email (optional):</label>
            <input type="email" id="email" name="email">

            <label for="headTeacher">Head Teacher's Name:</label>
            <input type="text" id="headTeacher" name="headTeacher" required>

            <label for="logo">School Logo:</label>
            <input type="file" id="logo" name="logo" accept="image/*" required>

            <label for="stamp">School Stamp:</label>
            <input type="file" id="stamp" name="stamp" accept="image/*" required>

            <label for="HDSign">Head Teacher's Signature:</label>
            <input type="file" id="HDSign" name="HDSign" accept="image/*" required>

            <button type="submit">Submit</button>
        </form>
    </div>
</body>
</html>
