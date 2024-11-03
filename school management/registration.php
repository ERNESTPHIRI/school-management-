<?php
// Include the database connection file
include('db/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $regNo = trim($_POST['regNo']);
    $fullName = trim($_POST['fullName']);
    $dob = $_POST['dob']; // Assuming the format is 'YYYY-MM-DD'
    $gender = $_POST['gender'];
    $phone = $_POST['phone'];
    $countryCode = $_POST['countryCode'];
    $form = $_POST['form'];
    $term = $_POST['term'];
    $academicYr = $_POST['academicYr'];
    $guardian = trim($_POST['guardian']);
    $address1 = trim($_POST['address1']);
    $address2 = trim($_POST['address2']);

    // Validate date of birth
    $currentDate = new DateTime(); // Get the current date
    $dobDate = new DateTime($dob); // Convert the input DOB to a DateTime object
    $age = $currentDate->diff($dobDate)->y; // Calculate the age in years

    // Ensure the student's age is properly validated
    if ($dobDate > $currentDate) {
        echo "<script>alert('No student from the future is allowed.'); window.history.back();</script>";
        exit;
    }

    if ($age < 10) {
        echo "<script>
                if (confirm('Are you sure to register a student less than 10 years?')) {
                    // Proceed with registration
                } else {
                    window.history.back();
                }
              </script>";
        // No exit here so the user can proceed if they confirm
    }

    // Validate phone number
    if (!preg_match('/^\d{9}$/', $phone)) {
        echo "<script>alert('Phone number is incomplete.'); window.history.back();</script>";
        exit;
    }

    // Determine the correct table based on the selected form
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
            echo "<script>alert('Invalid form selected.'); window.history.back();</script>";
            exit;
    }

    // Get the database connection
    $conn = getDBConnection();

    // Check if connection was successful
    if ($conn) {
        // Prepare the statement
        $stmt = $conn->prepare("INSERT INTO $tableName (regNo, fullName, dob, gender, phone, countryCode, form, term, academicYr, guardian, address1, address2) VALUES (:regNo, :fullName, :dob, :gender, :phone, :countryCode, :form, :term, :academicYr, :guardian, :address1, :address2)");

        // Bind the parameters using bindValue()
        $stmt->bindValue(':regNo', $regNo, SQLITE3_TEXT);
        $stmt->bindValue(':fullName', $fullName, SQLITE3_TEXT);
        $stmt->bindValue(':dob', $dob, SQLITE3_TEXT);
        $stmt->bindValue(':gender', $gender, SQLITE3_TEXT);
        $stmt->bindValue(':phone', $phone, SQLITE3_TEXT);
        $stmt->bindValue(':countryCode', $countryCode, SQLITE3_TEXT);
        $stmt->bindValue(':form', $form, SQLITE3_TEXT);
        $stmt->bindValue(':term', $term, SQLITE3_TEXT);
        $stmt->bindValue(':academicYr', $academicYr, SQLITE3_TEXT);
        $stmt->bindValue(':guardian', $guardian, SQLITE3_TEXT);
        $stmt->bindValue(':address1', $address1, SQLITE3_TEXT);
        $stmt->bindValue(':address2', $address2, SQLITE3_TEXT);

        // Execute the statement
        if ($stmt->execute()) {
            echo "<script>alert('Registration successful!'); window.location.href='registration.html';</script>";
        } else {
            echo "<script>alert('Registration failed. Please try again.'); window.history.back();</script>";
        }

        // Close the statement and connection
        $stmt->close();
        $conn->close();
    } else {
        echo "<script>alert('Database connection failed.'); window.history.back();</script>";
    }

    $db->close(); 
}
?>
