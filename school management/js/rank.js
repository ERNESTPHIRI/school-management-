// rank.js

document.addEventListener('DOMContentLoaded', function() {
    const rankForm = document.getElementById('rankForm');
    const resultDiv = document.getElementById('result');
    const printBtn = document.getElementById('printBtn');

    rankForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // Retrieve form data
        const academicYear = document.getElementById('academicYear').value;
        const form = document.getElementById('form').value;
        const term = document.getElementById('term').value;

        // Basic validation
        if (!academicYear || !form || !term) {
            alert('Please select all fields.');
            return;
        }

        // Prepare data for POST request
        const formData = new FormData();
        formData.append('academicYear', academicYear);
        formData.append('form', form);
        formData.append('term', term);

        // Send AJAX request to rank.php
        fetch('rank.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayResults(data.data, academicYear, form, term);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing your request.');
        });
    });

    function displayResults(students, academicYear, form, term) {
        // Clear previous results
        resultDiv.innerHTML = '';

        if (students.length === 0) {
            resultDiv.innerHTML = '<p>No student records found for the selected criteria.</p>';
            printBtn.style.display = 'none';
            return;
        }

        // Create table elements
        const title = document.createElement('h2');
        title.textContent = `Ranked Students - Form ${form}, ${academicYear}, ${term}`;
        resultDiv.appendChild(title);

        const table = document.createElement('table');

        // Create table header
        const thead = document.createElement('thead');
        const headerRow = document.createElement('tr');
        ['Position', 'Reg No', 'Full Name', 'Total Score', 'Remarks'].forEach(text => {
            const th = document.createElement('th');
            th.textContent = text;
            headerRow.appendChild(th);
        });
        thead.appendChild(headerRow);
        table.appendChild(thead);

        // Create table body
        const tbody = document.createElement('tbody');
        students.forEach(student => {
            const row = document.createElement('tr');

            const positionCell = document.createElement('td');
            positionCell.textContent = student.position;
            row.appendChild(positionCell);

            const regNoCell = document.createElement('td');
            regNoCell.textContent = student.regNo;
            row.appendChild(regNoCell);

            const nameCell = document.createElement('td');
            nameCell.textContent = student.fullName;
            row.appendChild(nameCell);

            const scoreCell = document.createElement('td');
            scoreCell.textContent = student.totalScore;
            row.appendChild(scoreCell);

            const remarksCell = document.createElement('td');
            remarksCell.textContent = student.remarks;
            row.appendChild(remarksCell);

            tbody.appendChild(row);
        });
        table.appendChild(tbody);
        resultDiv.appendChild(table);

        // Show print button
        printBtn.style.display = 'block';
    }

    // Print functionality
    printBtn.addEventListener('click', function() {
        window.print();
    });
});
