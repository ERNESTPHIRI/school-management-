<?php
$error = '';
$success = '';

include 'db/db.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = getDBConnection();

    $loginName = $_POST['LoginName'];
    $password = $_POST['Password'];
    $rank = $_POST['Rank'];

    if (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        $password = password_hash($password, PASSWORD_DEFAULT);

        $db->exec('BEGIN TRANSACTION');
        
        $stmt = $db->prepare('INSERT INTO users (LoginName, Password, Rank) VALUES (:LoginName, :Password, :Rank)');
        $stmt->bindValue(':LoginName', $loginName, SQLITE3_TEXT);
        $stmt->bindValue(':Password', $password, SQLITE3_TEXT);
        $stmt->bindValue(':Rank', $rank, SQLITE3_TEXT);

        if ($stmt->execute()) {
            $db->exec('COMMIT');
            $success = "User registered successfully!";
        } else {
            $db->exec('ROLLBACK');
            $error = "Error: Could not register the user.";
        }
    }

    $db->close(); 
}
?>
