<?php
include('db/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $regNo = $_POST['regNo'];
    $fullName = $_POST['fullName'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $guardian = $_POST['guardian'];
    $address1 = $_POST['address1'];
    $address2 = $_POST['address2'];

    $conn = getDBConnection();

    if ($conn) {
        $stmt = $conn->prepare("UPDATE students SET fullName = :fullName, dob = :dob, gender = :gender, phone = :phone, guardian = :guardian, address1 = :address1, address2 = :address2 WHERE regNo = :regNo");
        $stmt->bindValue(':regNo', $regNo, SQLITE3_TEXT);
        $stmt->bindValue(':fullName', $fullName, SQLITE3_TEXT);
        $stmt->bindValue(':dob', $dob, SQLITE3_TEXT);
        $stmt->bindValue(':gender', $gender, SQLITE3_TEXT);
        $stmt->bindValue(':phone', $phone, SQLITE3_TEXT);
        $stmt->bindValue(':guardian', $guardian, SQLITE3_TEXT);
        $stmt->bindValue(':address1', $address1, SQLITE3_TEXT);
        $stmt->bindValue(':address2', $address2, SQLITE3_TEXT);

        $result = $stmt->execute();

        if ($result) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Failed to update student details']);
        }

        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(['error' => 'Database connection failed']);
    }

    $db->close(); 
}
?>
