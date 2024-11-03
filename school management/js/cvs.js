document.getElementById('cvs-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const academicYr = document.getElementById('academicYr').value;
    const form = document.getElementById('form').value;
    const term = document.getElementById('term').value;
    const subject = document.getElementById('subject').value;

    fetch('cvs.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `academicYr=${academicYr}&form=${form}&term=${term}&subject=${subject}`,
    })
    .then(response => response.json())
    .then(data => {
        const resultsDiv = document.getElementById('results');

        if (data.error) {
            resultsDiv.innerHTML = `<p>${data.error}</p>`;
        } else {
            let table = '<table><thead><tr><th>Reg No</th><th>Name of Student</th><th>Score</th></tr></thead><tbody>';
            data.forEach(row => {
                table += `<tr><td>${row.regNo}</td><td>${row.fullName}</td><td>${row.score}</td></tr>`;
            });
            table += '</tbody></table>';
            resultsDiv.innerHTML = table;
        }
    })
    .catch(error => console.error('Error:', error));
});
