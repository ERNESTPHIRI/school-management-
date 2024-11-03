<?php
session_start(); // Start the session
$error = '';

// Include the database connection file
include 'db/db.php'; // Adjust path as necessary

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get a database connection
    $db = getDBConnection();

    // Get form data
    $loginName = $_POST['LoginName'];
    $password = $_POST['Password'];

    // Prepare and execute the SQL query to get the user
    $stmt = $db->prepare('SELECT Password, Rank FROM users WHERE LoginName = :LoginName');
    $stmt->bindValue(':LoginName', $loginName, SQLITE3_TEXT);
    $result = $stmt->execute();

    if ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        // Verify the password
        if (password_verify($password, $row['Password'])) {
            // Password is correct; set session variables
            $_SESSION['loginName'] = $loginName;
            $_SESSION['rank'] = $row['Rank'];

            // Check the user's rank
            if ($_SESSION['rank'] === 'Admin') {
                // Redirect to admin dashboard or appropriate page for Admin
                header('Location: signup.html'); 
                exit();
            } elseif ($_SESSION['rank'] === 'Teacher') {
                // Redirect to signup.html if the user is a Teacher
                header('Location: teacherdashboard.html'); 
                exit();
            } elseif ($_SESSION['rank'] === 'Form Teacher') {
                // Redirect to form teacher page if the user is a Form Teacher
                header('Location: formteacher.html'); 
                exit();
            } elseif ($_SESSION['rank'] === 'Student') {
                // Redirect to student dashboard or appropriate page for Student
                header('Location: student_dashboard.php'); 
                exit();
            } else {
                // Redirect to a default page if the rank does not match any known role
                header('Location: unknown_role.html'); 
                exit();
            }
            
        } else {
            $error = "Invalid Login Name or Password.";
        }
    } else {
        $error = "Invalid Login Name or Password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/login.css">
    <title>User Login</title>
</head>
<body>
    <div class="container">
        <h2>User Login</h2>
        <form action="login.php" method="POST">
            <label for="LoginName">Login Name:</label>
            <input type="text" id="LoginName" name="LoginName" required><br>

            <label for="Password">Password:</label>
            <input type="password" id="Password" name="Password" required><br>

            <button type="submit">Login</button>
        </form>
        <?php if (!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
