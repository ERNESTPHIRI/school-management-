<?php
require_once 'db/db.php';

function getSchoolDetails() {
    $db = getDBConnection();
    if (!$db) return null;

    $query = "SELECT * FROM Xull WHERE id = 1"; // Assuming only one school record
    $result = $db->querySingle($query, true); // Fetch as an associative array

    return $result ? $result : null;
}

function getFormTeacherDetails($form, $academicYr, $term) {
    $db = getDBConnection();
    if (!$db) return null;

    $query = "SELECT formTeacher, FTSign FROM formClass WHERE name = :form AND academicYr = :academicYr AND term = :term";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':form', $form, SQLITE3_TEXT);
    $stmt->bindValue(':academicYr', $academicYr, SQLITE3_TEXT);
    $stmt->bindValue(':term', $term, SQLITE3_TEXT);

    $result = $stmt->execute();
    return $result ? $result->fetchArray(SQLITE3_ASSOC) : null;
}

function getStudentData($regNo, $form, $academicYr, $term) {
    // Determine the correct table names based on the form
    $formNumber = str_replace('Form ', '', $form);
    $studentTable = "f" . $formNumber . "Student"; 
    $gradesTable = "fgrade" . $formNumber;

    $db = getDBConnection();
    if (!$db) return null;

    // Query to get student information from the relevant student table
    $studentQuery = "SELECT * FROM $studentTable WHERE regNo = :regNo AND academicYr = :academicYr AND term = :term";
    $stmt = $db->prepare($studentQuery);
    $stmt->bindValue(':regNo', $regNo, SQLITE3_TEXT);
    $stmt->bindValue(':academicYr', $academicYr, SQLITE3_TEXT);
    $stmt->bindValue(':term', $term, SQLITE3_TEXT);

    $result = $stmt->execute();
    $studentData = $result ? $result->fetchArray(SQLITE3_ASSOC) : null;

    if ($studentData) {
        // Query to get grades from the relevant grades table
        $gradeQuery = "SELECT * FROM $gradesTable WHERE student_id = (SELECT rowid FROM $studentTable WHERE regNo = :regNo AND academicYr = :academicYr AND term = :term)";
        $gradeStmt = $db->prepare($gradeQuery);
        $gradeStmt->bindValue(':regNo', $regNo, SQLITE3_TEXT);
        $gradeStmt->bindValue(':academicYr', $academicYr, SQLITE3_TEXT);
        $gradeStmt->bindValue(':term', $term, SQLITE3_TEXT);

        $gradesResult = $gradeStmt->execute();
        $grades = $gradesResult ? $gradesResult->fetchArray(SQLITE3_ASSOC) : null;
        
        // Store grades in student data if found
        if ($grades) {
            $studentData['grades'] = $grades;
        }
    }

    return $studentData;
}

function calculateOverallGrade($grades, $form) {
    $englishScore = $grades['score_english'] ?? 0;

    if ($form == 'Form 1' || $form == 'Form 2') {
        if ($englishScore < 50) {
            return 'F';
        }

        $bestScores = array_values(array_diff_key($grades, ['score_english' => $englishScore]));
        rsort($bestScores);
        $top5Total = array_sum(array_slice($bestScores, 0, 5)) + $englishScore;

        if ($top5Total > 480) return 'A';
        if ($top5Total > 420) return 'B';
        if ($top5Total > 360) return 'C';
        if ($top5Total > 300) return 'D';
        return 'F';
    } else { // Form 3 and Form 4
        if ($englishScore < 50) return 'Null';
    
        // Calculate English point
        $englishPoint = ($englishScore >= 90) ? 1 : (($englishScore >= 80) ? 2 : (($englishScore >= 75) ? 3 : (($englishScore >= 70) ? 4 : (($englishScore >= 65) ? 5 : (($englishScore >= 60) ? 6 : (($englishScore >= 55) ? 7 : (($englishScore >= 50) ? 8 : 9)))))));
        
        $bestPoints = [];
        foreach ($grades as $subject => $score) {
            if ($subject != 'score_english') { // Exclude English here
                $point = ($score >= 90) ? 1 : (($score >= 80) ? 2 : (($score >= 75) ? 3 : (($score >= 70) ? 4 : (($score >= 65) ? 5 : (($score >= 60) ? 6 : (($score >= 55) ? 7 : (($score >= 50) ? 8 : 9)))))));
                if ($point < 9) $bestPoints[] = $point;
            }
        }
    
        if (count($bestPoints) < 5) return 'Null'; // Ensure at least 5 other subjects are valid
        sort($bestPoints);
        
        // Sum top 5 lowest points and include the English point
        $sumPoints = array_sum(array_slice($bestPoints, 0, 5)) + $englishPoint;
    
        return $sumPoints;
    }
}

// Retrieve form data and image paths from query parameters
$regNo = $_GET['regNo'] ?? '';
$form = $_GET['form'] ?? '';
$academicYr = $_GET['academicYr'] ?? '';
$term = $_GET['term'] ?? '';

// Get school and form teacher details
$schoolDetails = getSchoolDetails();
$formTeacherDetails = getFormTeacherDetails($form, $academicYr, $term);

// Get student data
$studentData = getStudentData($regNo, $form, $academicYr, $term);

if (!$studentData || !$schoolDetails || !$formTeacherDetails) {
    echo "<p>No data found for the given student details or school information.</p>";
    exit;
}

// Calculate overall grade based on the scores
$overallGrade = calculateOverallGrade($studentData['grades'], $form);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Performance Report</title>
    <link rel="stylesheet" href="css/studentreport.css">
</head>
<body>
    <div class="report">
        <div class="header">
            <img src="data:image/png;base64,<?= base64_encode($schoolDetails['logo']) ?>" alt="School Logo" class="school-logo">
            <h2><?= htmlspecialchars($schoolDetails['name']) ?></h2>
            <p><?= htmlspecialchars($schoolDetails['address']) ?></p>
            <p>Phone: <?= htmlspecialchars($schoolDetails['phone']) ?>, Email: <?= htmlspecialchars($schoolDetails['email']) ?></p>
        </div>

        <h3>Student Performance Report</h3>

        <div class="details">
            <p><strong>Name:</strong> <?= htmlspecialchars($studentData['fullName']) ?></p>
            <p><strong>Reg No:</strong> <?= htmlspecialchars($regNo) ?></p>
        </div>

        <div class="academic-info">
            <p><strong>Academic Year:</strong> <?= htmlspecialchars($academicYr) ?></p>
            <p><strong>Class:</strong> <?= htmlspecialchars($form) ?></p>
            <p><strong>Term:</strong> <?= htmlspecialchars($term) ?></p>
        </div>

        <table>
            <tr>
                <th>Subject</th>
                <th>Score</th>
                <?php if ($form == 'Form 1' || $form == 'Form 2'): ?>
                    <th>Grade</th>
                <?php else: ?>
                    <th>Points</th>
                <?php endif; ?>
                <th>Remarks</th>
            </tr>
            <?php
            $subjects = ['agriculture', 'additional_math', 'biology', 'business_studies', 'computer_studies', 'chichewa', 'chemistry', 'english', 'geography', 'history', 'life_skills', 'mathematics', 'social_studies', 'physics', 'home_economics'];
            foreach ($subjects as $subject) {
                $scoreKey = "score_" . $subject;
                $score = $studentData['grades'][$scoreKey] ?? '--';
                $gradeOrPoint = $remarks = '--';

                if ($score !== '--') {
                    if ($form == 'Form 1' || $form == 'Form 2') {
                        $gradeOrPoint = ($score >= 80) ? 'A' : (($score >= 70) ? 'B' : (($score >= 60) ? 'C' : (($score >= 50) ? 'D' : 'F')));
                        $remarks = ($score >= 80) ? 'Excellent' : (($score >= 70) ? 'Good' : (($score >= 60) ? 'Average' : 'Needs Improvement'));
                    } else {
                        $gradeOrPoint = ($score >= 90) ? '1' : (($score >= 80) ? '2' : (($score >= 75) ? '3' : (($score >= 70) ? '4' : (($score >= 65) ? '5' : (($score >= 60) ? '6' : (($score >= 55) ? '7' : (($score >= 50) ? '8' : '9')))))));
                        $remarks = ($score >= 90) ? 'Outstanding' : (($score >= 80) ? 'Very Good' : (($score >= 70) ? 'Good' : 'Needs Improvement'));
                    }
                }

                echo "<tr>
                        <td>" . ucfirst(str_replace('_', ' ', $subject)) . "</td>
                        <td>" . htmlspecialchars($score) . "</td>
                        <td>" . htmlspecialchars($gradeOrPoint) . "</td>
                        <td>" . htmlspecialchars($remarks) . "</td>
                      </tr>";
            }
            ?>
        </table>

        <div class="evaluate">
            <?php if ($form == 'Form 1' || $form == 'Form 2'): ?>
                <p><strong>Overall Grade:</strong> <?= htmlspecialchars($overallGrade) ?></p>
            <?php else: ?>
                <p><strong>Total Points:</strong> <?= htmlspecialchars($overallGrade) ?></p>
            <?php endif; ?>
            <p><strong>Remarks:</strong> 
                <?= ($overallGrade === 'F' || $overallGrade === 'Null') ? 'Persevere' : 'Congratulations!'; ?>
            </p>
        </div>



        <div class="signature">
            <p>Head Teacher: <?= htmlspecialchars($schoolDetails['headTeacher']) ?></p>
            <img src="data:image/png;base64,<?= base64_encode($schoolDetails['HDSign']) ?>" alt="Head Teacher's Signature" class="signature-img">
            <p>Form Teacher: <?= htmlspecialchars($formTeacherDetails['formTeacher']) ?></p>
            <img src="data:image/png;base64,<?= base64_encode($formTeacherDetails['FTSign']) ?>" alt="Form Teacher's Signature" class="signature-img">
        </div>

        <div class="stamp">
            <img src="data:image/png;base64,<?= base64_encode($schoolDetails['stamp']) ?>" alt="School Stamp" class="stamp-img">
        </div>

        <button onclick="window.print()">Print Report</button>
    </div>
</body>
</html>
