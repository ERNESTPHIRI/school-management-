<?php
// Save uploaded images to the "uploads" directory
$uploads_dir = 'uploads';
$fields = ['schoolLogo', 'teacherSignature', 'headTeacherSignature', 'schoolStamp'];
$imagePaths = [];

foreach ($fields as $field) {
    if (isset($_FILES[$field]) && $_FILES[$field]['error'] == UPLOAD_ERR_OK) {
        $tmp_name = $_FILES[$field]['tmp_name'];
        $name = basename($_FILES[$field]['name']);
        $target_file = "$uploads_dir/$name";
        move_uploaded_file($tmp_name, $target_file);
        $imagePaths[$field] = $target_file;
    }
}

// Redirect to studentreport.php with student details and image paths
$query = http_build_query(array_merge($_POST, $imagePaths));
header("Location: studentreport.php?$query");
exit;
?>
