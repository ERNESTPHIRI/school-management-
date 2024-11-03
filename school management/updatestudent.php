<?php
// Include the database connection
include('db/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect POST data
    $regNo = $_POST['regNo'];
    $fullName = $_POST['fullName'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $guardian = $_POST['guardian'];
    $address1 = $_POST['address1'];
    $address2 = $_POST['address2'];
    $form = $_POST['form'];
    $term = $_POST['term'];
    $academicYr = $_POST['academicYr'];

    // Get the database connection
    $conn = getDBConnection();

    if ($conn) {
        // Prepare the update query
        $stmt = $conn->prepare("UPDATE students 
                                SET fullName = ?, dob = ?, gender = ?, phone = ?, guardian = ?, address1 = ?, address2 = ?, form = ?, term = ?, academicYr = ? 
                                WHERE regNo = ?");
        $stmt->bind_param('sssssssssss', $fullName, $dob, $gender, $phone, $guardian, $address1, $address2, $form, $term, $academicYr, $regNo);

        // Execute the query
        if ($stmt->execute()) {
            echo json_encode(['success' => 'Student updated successfully!']);
        } else {
            echo json_encode(['error' => 'Update failed: ' . $stmt->error]);
        }

        // Close the statement and connection
        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(['error' => 'Database connection failed']);
    }
}
?>
