document.getElementById('uploadCA').addEventListener('click', function() {
    loadContent('Upload Continuous Assessment');
});

document.getElementById('uploadET').addEventListener('click', function() {
    loadContent('Upload End of Term');
});

document.getElementById('viewFinalGrade').addEventListener('click', function() {
    loadContent('View Final Grade');
});

function loadContent(content) {
    const contentDiv = document.getElementById('content');
    contentDiv.innerHTML = `<h2>${content}</h2><p>This section will handle ${content.toLowerCase()} functionality.</p>`;
}
