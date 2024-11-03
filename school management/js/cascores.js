document.getElementById('getStudentBtn').addEventListener('click', function(event) {
    event.preventDefault(); // Prevent default form submission

    const academicYear = document.getElementById('academicYr').value;
    const term = document.getElementById('term').value;
    const form = document.getElementById('form').value;
    const regNo = document.getElementById('regNo').value;

    if (regNo.trim() === '') {
        alert('Please enter the student registration number.');
        return;
    }

    // Retrieve student and show form to enter CAT scores
    fetch('casores.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `academicYr=${encodeURIComponent(academicYear)}&term=${encodeURIComponent(term)}&form=${encodeURIComponent(form)}&regNo=${encodeURIComponent(regNo)}`
    })
    .then(response => response.text()) // Get the raw response text
    .then(text => {
        console.log('Raw response from server:', text); // Log the raw HTML or JSON response
        
        try {
            // Try to parse the response as JSON
            const data = JSON.parse(text);

            if (data.status === 'success') {
                const student = data.student;

                const subjects = [
                    'Agriculture', 'Additional Mathematics', 'Biology', 'Business Studies', 
                    'Computer Studies', 'Chichewa', 'Chemistry', 'English', 
                    'Geography', 'History', 'Life Skills', 'Mathematics', 
                    'Social Studies', 'Physics', 'Home Economics'
                ];

                // Create the form to enter scores
                let scoreFormHtml = `<h2>Enter Scores for Student: ${student.fullName} (RegNo: ${student.regNo})</h2>`;
                scoreFormHtml += `<form method="POST" action="insertCat.php">`; // Corrected form action
                scoreFormHtml += `<input type="hidden" name="regNo" value="${student.regNo}">`;
                scoreFormHtml += `<input type="hidden" name="academicYr" value="${academicYear}">`;
                scoreFormHtml += `<input type="hidden" name="term" value="${term}">`;
                scoreFormHtml += `<input type="hidden" name="form" value="${form}">`;

                scoreFormHtml += `<label for="subject">Select Subject:</label>`;
                scoreFormHtml += `<select name="subject" required>`;
                subjects.forEach(subject => {
                    scoreFormHtml += `<option value="${subject}">${subject}</option>`;
                });
                scoreFormHtml += `</select><br>`;

                scoreFormHtml += `<label for="score">Enter CAT Score:</label>`;
                scoreFormHtml += `<input type="number" step="0.01" name="score" required><br>`;

                scoreFormHtml += `<button type="submit">Submit Score</button></form>`;
                document.getElementById('scoreFormContainer').innerHTML = scoreFormHtml; // Update the container with the form
            } else {
                alert(data.message);
            }
        } catch (e) {
            // If parsing fails, log the error and show the raw text
            console.error('Error parsing JSON:', e);
            alert('An error occurred. Raw response: ' + text);
        }
    })
    .catch(error => {
        // Display error on the page
        const errorContainer = document.createElement('div');
        errorContainer.style.color = 'red';
        errorContainer.innerHTML = `<strong>An error occurred:</strong> ${error.message}`;
        document.body.appendChild(errorContainer);
    });
});
