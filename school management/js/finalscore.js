document.getElementById('getScores').addEventListener('click', function() {
    const form = document.getElementById('form').value;
    const academicYear = document.getElementById('academic_year').value;
    const term = document.getElementById('term').value;
    const subject = document.getElementById('subject').value;

    console.log('Fetching scores...');
    fetch('finalscore.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action: 'get_scores',
            form,
            academic_year: academicYear,
            term,
            subject
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Data received:', data);

        const tbody = document.querySelector('#scoreTable tbody');
        tbody.innerHTML = '';

        data.forEach((student, index) => {
            const row = `<tr>
                <td>${index + 1}</td>
                <td>${student.regNo}</td>
                <td>${student.fullName}</td>
                <td>${student.final_score.toFixed(2)}</td>
            </tr>`;
            tbody.innerHTML += row;
        });
    })
    .catch(error => console.error('Error fetching scores:', error));
});

document.getElementById('approveScores').addEventListener('click', function() {
    const students = [...document.querySelectorAll('#scoreTable tbody tr')].map(row => {
        const regNo = row.children[1].textContent;
        const finalScore = row.children[3].textContent;
        return { regNo, final_score: parseFloat(finalScore) };
    });

    const form = document.getElementById('form').value;
    const academicYear = document.getElementById('academic_year').value;
    const term = document.getElementById('term').value;
    const subject = document.getElementById('subject').value;

    console.log('Approving scores...');

    fetch('finalscore.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            action: 'approve_scores',
            form,
            academic_year: academicYear,
            term,
            subject,
            students
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Scores approved:', data);
        alert('Scores approved successfully!');
    })
    .catch(error => console.error('Error approving scores:', error));
});
