document.getElementById('fetchReportBtn').addEventListener('click', async () => {
    const regNo = document.getElementById('regNo').value;
    const form = document.getElementById('form').value;
    const academicYr = document.getElementById('academicYr').value;
    const term = document.getElementById('term').value;

    const response = await fetch('studentreport.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `regNo=${regNo}&form=${form}&academicYr=${academicYr}&term=${term}`
    });
    const data = await response.json();

    displayReport(data);
});

function displayReport(data) {
    const reportSection = document.getElementById('reportSection');
    reportSection.innerHTML = generateReportHTML(data);
}

function generateReportHTML(data) {
    // HTML generation logic for report including grades, remarks, and grading key based on form
    return `<!-- HTML content based on data -->`;
}

document.getElementById('printReportBtn').addEventListener('click', () => {
    window.print();
});
