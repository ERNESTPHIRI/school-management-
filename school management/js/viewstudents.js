let studentData = []; // Global array to store student data

// Event listener for submitting the 'view students' form
document.getElementById('viewStudentsForm').addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent default form submission

    const form = document.getElementById('form').value;
    const term = document.getElementById('term').value;
    const academicYr = document.getElementById('academicYr').value;

    // Fetch request to view students based on form, term, and academic year
    fetch('viewstudents.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `form=${encodeURIComponent(form)}&term=${encodeURIComponent(term)}&academicYr=${encodeURIComponent(academicYr)}`,
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }
        return response.json(); // Parse JSON response
    })
    .then(data => {
        const tbody = document.querySelector('#studentsTable tbody');
        tbody.innerHTML = ''; // Clear previous table rows

        if (data.error) {
            alert(data.error); // Display error if present in the response
        } else {
            studentData = data; // Store data globally

            // Loop through each student and create a table row
            data.forEach(student => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${student.regNo}</td>
                    <td>${student.fullName}</td>
                    <td>${student.dob}</td>
                    <td>${student.gender}</td>
                    <td>${student.phone}</td>
                    <td>${student.guardian}</td>
                    <td>${student.address1}</td>
                    <td>${student.address2}</td>
                    <td><button class="editBtn" data-regno="${student.regNo}">Edit</button></td>`;
                tbody.appendChild(row); // Append row to the table body
            });
        }
    })
    .catch(error => console.error('Error fetching student data:', error)); // Log errors
});

// Event listener for handling edit buttons within the table
document.addEventListener('click', function (event) {
    if (event.target.classList.contains('editBtn')) {
        const regNo = event.target.dataset.regno;

        // Find student data using regNo from global studentData array
        const student = studentData.find(s => s.regNo === regNo);

        if (student) {
            // Populate the edit form fields with student's details
            document.getElementById('editRegNo').value = student.regNo;
            document.getElementById('editFullName').value = student.fullName;
            document.getElementById('editDOB').value = student.dob;
            document.getElementById('editGender').value = student.gender;
            document.getElementById('editPhone').value = student.phone;
            document.getElementById('editGuardian').value = student.guardian;
            document.getElementById('editAddress1').value = student.address1;
            document.getElementById('editAddress2').value = student.address2;
            document.getElementById('editForm').value = student.form;
            document.getElementById('editTerm').value = student.term;
            document.getElementById('editAcademicYr').value = student.academicYr;

            // Display the edit form container
            document.getElementById('editStudentFormContainer').style.display = 'block';
        } else {
            alert('Student data not found!');
        }
    }
});

// Event listener for submitting the edit student form
document.getElementById('editStudentForm').addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent default form submission

    // Get form data for editing
    const regNo = document.getElementById('editRegNo').value;
    const fullName = document.getElementById('editFullName').value;
    const dob = document.getElementById('editDOB').value;
    const gender = document.getElementById('editGender').value;
    const phone = document.getElementById('editPhone').value;
    const guardian = document.getElementById('editGuardian').value;
    const address1 = document.getElementById('editAddress1').value;
    const address2 = document.getElementById('editAddress2').value;
    const form = document.getElementById('editForm').value;
    const term = document.getElementById('editTerm').value;
    const academicYr = document.getElementById('editAcademicYr').value;

    // AJAX request to update student details
    fetch('updatestudent.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `regNo=${encodeURIComponent(regNo)}&fullName=${encodeURIComponent(fullName)}&dob=${encodeURIComponent(dob)}&gender=${encodeURIComponent(gender)}&phone=${encodeURIComponent(phone)}&guardian=${encodeURIComponent(guardian)}&address1=${encodeURIComponent(address1)}&address2=${encodeURIComponent(address2)}&form=${encodeURIComponent(form)}&term=${encodeURIComponent(term)}&academicYr=${encodeURIComponent(academicYr)}`,
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok: ' + response.statusText);
        }
        return response.json();
    })
    .then(data => {
        if (data.error) {
            alert(data.error); // Display error message
        } else {
            alert('Student details updated successfully!'); // Success message
            document.getElementById('editStudentFormContainer').style.display = 'none'; // Hide edit form
            document.getElementById('viewStudentsForm').submit(); // Re-fetch students data
        }
    })
    .catch(error => console.error('Error updating student details:', error)); // Log errors
});

// Event listener for printing the table to PDF
document.getElementById('printToPDF').addEventListener('click', function () {
    const form = document.getElementById('form').value;
    const term = document.getElementById('term').value;
    const academicYr = document.getElementById('academicYr').value;

    const studentTable = document.getElementById('studentsTable').outerHTML;
    const title = `Students - ${form} ${term} ${academicYr}`;

    const style = `
        <style>
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid black; padding: 8px; text-align: left; }
        </style>`;

    const win = window.open('', '', 'height=700,width=700');
    win.document.write(`<html><head><title>${title}</title></head><body>`);
    win.document.write(`<h2>${title}</h2>`);
    win.document.write(studentTable);
    win.document.write(style);
    win.document.write('</body></html>');
    win.document.close();
    win.print();
});
