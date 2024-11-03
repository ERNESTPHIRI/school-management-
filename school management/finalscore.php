<?php
require_once 'db/db.php'; // Ensure the database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Receive JSON data
    $data = json_decode(file_get_contents('php://input'), true);

    // Fetch and calculate scores
    if ($data['action'] == 'get_scores') {
        $form = $data['form'];
        $academicYear = $data['academic_year'];
        $term = $data['term'];
        $subject = $data['subject'];

        // Fetch data from the respective tables
        $db = getDBConnection();
        $tableStudents = "f{$form}Student";
        $tableCascores = "cascore{$form}";
        $tableEotscores = "eotscores{$form}";

        $subjectColumn = "score_{$subject}";

        // Outer join to fetch data from both cascore and eotscore, even if one is missing
        $query = "SELECT s.regNo, s.fullName, 
                         COALESCE(c.$subjectColumn, 0) AS continuous_score, 
                         COALESCE(e.$subjectColumn, 0) AS end_term_score
                  FROM $tableStudents s
                  LEFT JOIN $tableCascores c ON s.rowid = c.student_id 
                      AND c.academic_year = :academicYear AND c.term = :term
                  LEFT JOIN $tableEotscores e ON s.rowid = e.student_id 
                      AND e.academic_year = :academicYear AND e.term = :term";

        $stmt = $db->prepare($query);
        $stmt->bindValue(':academicYear', $academicYear);
        $stmt->bindValue(':term', $term);
        $result = $stmt->execute();

        $students = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $continuousScore = $row['continuous_score'];
            $endTermScore = $row['end_term_score'];

            // Treat 0 as null (not involved in the calculation)
            $continuousScore = $continuousScore ?: null;
            $endTermScore = $endTermScore ?: null;

            // Skip students with no valid scores in both groups
            if ($continuousScore === null && $endTermScore === null) {
                continue;
            }

            // Apply the logic for score calculation
            if ($continuousScore !== null && $endTermScore !== null) {
                // Both scores available, apply 40%-60% weighting
                $finalScore = ($continuousScore * 0.4) + ($endTermScore * 0.6);
            } elseif ($continuousScore !== null) {
                // Only continuous assessment available, use it as 100%
                $finalScore = $continuousScore;
            } elseif ($endTermScore !== null) {
                // Only end of term score available, use it as 100%
                $finalScore = $endTermScore;
            }

            // Round the final score to the nearest whole number
            $finalScore = round($finalScore);

            // Prepare student data with the calculated final score
            $students[] = [
                'regNo' => $row['regNo'],
                'fullName' => $row['fullName'],
                'final_score' => $finalScore
            ];
        }

        echo json_encode($students);
        exit();
    }

    // Approve scores
    if ($data['action'] == 'approve_scores') {
        $form = $data['form'];
        $students = $data['students'];  // Array of student scores
        $academicYear = $data['academic_year'];
        $term = $data['term'];
        $subject = $data['subject'];

        $db = getDBConnection();
        $tableFgrades = "fgrade{$form}";
        $subjectColumn = "score_{$subject}";

        foreach ($students as $student) {
            $regNo = $student['regNo'];
            $finalScore = $student['final_score'];

            // Check if the student exists in fgrade table
            $query = "SELECT id FROM $tableFgrades WHERE student_id = (SELECT rowid FROM f{$form}Student WHERE regNo = :regNo)";
            $stmt = $db->prepare($query);
            $stmt->bindValue(':regNo', $regNo);
            $result = $stmt->execute();

            if ($result->fetchArray()) {
                // Update existing record
                $updateQuery = "UPDATE $tableFgrades 
                                SET $subjectColumn = :finalScore 
                                WHERE student_id = (SELECT rowid FROM f{$form}Student WHERE regNo = :regNo)";
                $stmt = $db->prepare($updateQuery);
            } else {
                // Insert new record
                $insertQuery = "INSERT INTO $tableFgrades (student_id, academic_year, term, $subjectColumn)
                                VALUES ((SELECT rowid FROM f{$form}Student WHERE regNo = :regNo), :academicYear, :term, :finalScore)";
                $stmt = $db->prepare($insertQuery);
            }

            // Bind values and execute the query
            $stmt->bindValue(':finalScore', $finalScore);
            $stmt->bindValue(':regNo', $regNo);
            $stmt->bindValue(':academicYear', $academicYear);
            $stmt->bindValue(':term', $term);
            $stmt->execute();
        }

        // Respond with success message
        echo json_encode(['status' => 'success']);
        exit();
    }
}
?>
