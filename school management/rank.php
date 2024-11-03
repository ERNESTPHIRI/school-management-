<?php
// rank.php

header('Content-Type: application/json');

// Include the database connection file
require_once __DIR__ . '/db/db.php';

try {
    // Use the SQLite connection from db.php
    $db = getDBConnection();
    if (!$db) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
        exit;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]);
    exit;
}

// Retrieve and sanitize POST parameters
$academicYear = isset($_POST['academicYear']) ? trim($_POST['academicYear']) : '';
$form = isset($_POST['form']) ? intval($_POST['form']) : 0;
$term = isset($_POST['term']) ? trim($_POST['term']) : '';

// Validate inputs
if (empty($academicYear) || $form < 1 || $form > 4 || empty($term)) {
    echo json_encode(['success' => false, 'message' => 'Invalid or missing parameters.']);
    exit;
}

// Determine table names based on form
$fgradeTable = "fgrade{$form}";
$studentTable = "f{$form}Student";

// Check if tables exist (optional but recommended)
try {
    $result = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='{$fgradeTable}'");
    if ($result->fetchArray() === false) {
        throw new Exception("Table {$fgradeTable} does not exist.");
    }

    $result = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='{$studentTable}'");
    if ($result->fetchArray() === false) {
        throw new Exception("Table {$studentTable} does not exist.");
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    exit;
}

// Fetch student grades and details
try {
    $stmt = $db->prepare("
        SELECT g.*, s.regNo, s.fullName
        FROM {$fgradeTable} g
        JOIN {$studentTable} s ON g.student_id = s.rowid
        WHERE g.academic_year = :academicYear AND g.term = :term
    ");
    $stmt->bindValue(':academicYear', $academicYear, SQLITE3_TEXT);
    $stmt->bindValue(':term', $term, SQLITE3_TEXT);
    $result = $stmt->execute();

    $students = [];
    while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
        $students[] = $row;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error fetching data: ' . $e->getMessage()]);
    exit;
}

// Define all subject columns
$subjects = [
    'score_agriculture',
    'score_additional_math',
    'score_biology',
    'score_business_studies',
    'score_computer_studies',
    'score_chichewa',
    'score_chemistry',
    'score_english',
    'score_geography',
    'score_history',
    'score_life_skills',
    'score_mathematics',
    'score_social_studies',
    'score_physics',
    'score_home_economics'
];

// Process each student for ranking
$rankedStudents = [];

foreach ($students as $student) {
    $englishScore = floatval($student['score_english']);

    // Check if English score is less than 50
    if ($englishScore < 50) {
        $rankedStudents[] = [
            'regNo' => $student['regNo'],
            'fullName' => $student['fullName'],
            'totalScore' => number_format($englishScore, 2),
            'remarks' => 'Failed'
        ];
        continue;
    }

    // Collect other subject scores excluding English
    $otherScores = [];
    foreach ($subjects as $subject) {
        if ($subject === 'score_english') continue;
        $score = isset($student[$subject]) ? floatval($student[$subject]) : 0;
        $otherScores[] = $score;
    }

    // Sort scores in descending order and pick top 5
    rsort($otherScores, SORT_NUMERIC);
    $topFiveScores = array_slice($otherScores, 0, 5);

    // Calculate total score
    $totalScore = $englishScore + array_sum($topFiveScores);

    $rankedStudents[] = [
        'regNo' => $student['regNo'],
        'fullName' => $student['fullName'],
        'totalScore' => number_format($totalScore, 2),
        'remarks' => 'Passed'
    ];
}

// Sort the students: Passed first (sorted by totalScore descending), then Failed
usort($rankedStudents, function($a, $b) {
    if ($a['remarks'] === $b['remarks']) {
        if ($a['remarks'] === 'Passed') {
            return $b['totalScore'] <=> $a['totalScore'];
        }
        return 0; // Both Failed, no change in order
    }
    return ($a['remarks'] === 'Passed') ? -1 : 1;
});

// Assign position numbers
foreach ($rankedStudents as $index => &$student) {
    $student['position'] = $index + 1;
}
unset($student); // Break reference

// Reorder array to have position first
$finalRankedList = array_map(function($student) {
    return [
        'position' => $student['position'],
        'regNo' => $student['regNo'],
        'fullName' => $student['fullName'],
        'totalScore' => $student['totalScore'],
        'remarks' => $student['remarks']
    ];
}, $rankedStudents);

// Return the ranked list as JSON
echo json_encode(['success' => true, 'data' => $finalRankedList]);
?>
