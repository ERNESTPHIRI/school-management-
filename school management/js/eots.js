document.getElementById('get-student').addEventListener('click', function () {
    const academic_year = document.getElementById('academic_year').value;
    const form = document.getElementById('form').value;
    const term = document.getElementById('term').value;
    const regNo = document.getElementById('regNo').value;

    const formData = new FormData();
    formData.append('action', 'get_student');
    formData.append('academic_year', academic_year);
    formData.append('form', form);
    formData.append('term', term);
    formData.append('regNo', regNo);

    fetch('eots.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            document.getElementById('student-info').innerText = 'Student Name: ' + data.fullName;
        } else {
            document.getElementById('student-info').innerText = data.message;
        }
    });
});

document.getElementById('submit-score').addEventListener('click', function () {
    const academic_year = document.getElementById('academic_year').value;
    const form = document.getElementById('form').value;
    const term = document.getElementById('term').value;
    const regNo = document.getElementById('regNo').value;
    const subject = document.getElementById('subject').value;
    const score = document.getElementById('score').value;

    const formData = new FormData();
    formData.append('action', 'submit_score');
    formData.append('academic_year', academic_year);
    formData.append('form', form);
    formData.append('term', term);
    formData.append('regNo', regNo);
    formData.append('subject', subject);
    formData.append('score', score);

    fetch('eots.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
    });
});
