document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('myModal');
    const modalImage = document.getElementById('modalImage');
    const modalText = document.getElementById('modalText');
    const modalUsername = document.getElementById('modalUsername');
    const modalTags = document.getElementById('modalTags');
    const commentsSection = document.getElementById('commentsSection');

    window.openModal = function(imageSrc, text, id, comments, username, tags) {
        console.log('Opening modal with image:', imageSrc, 'text:', text, 'id:', id, 'username:', username, 'tags:', tags);

        modalImage.src = imageSrc;
        modalText.textContent = text;
        modalUsername.textContent = "Posted by: " + username;
        modalTags.textContent = "Tags: " + tags;

        commentsSection.innerHTML = ''; // Clear existing comments
        const commentsArray = JSON.parse(comments);
        commentsArray.forEach(function(comment) {
            const commentDiv = document.createElement('div');
            commentDiv.textContent = comment.username + ": " + comment.text;
            commentsSection.appendChild(commentDiv);
        });

        modal.style.display = "block";
    };

    window.closeModal = function() {
        modal.style.display = "none";
    };

    window.onclick = function(event) {
        if (event.target === modal) {
            closeModal();
        }
    };
});