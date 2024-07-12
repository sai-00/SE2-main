document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('myModal');
    const modalImage = document.getElementById('modalImage');
    const modalText = document.getElementById('modalText');
    const modalUsername = document.getElementById('modalUsername');
    const modalTags = document.getElementById('modalTags');
    const commentsSection = document.getElementById('commentsSection');
    const commentPostId = document.getElementById('commentPostId');

    window.openModal = function(imageSrc, text, postId, comments, username, tags) {
        console.log('Opening modal with image:', imageSrc, 'text:', text, 'username:', username, 'tags:', tags);

        modalImage.src = imageSrc;
        modalText.textContent = text;
        modalUsername.textContent = "Posted by: " + username;
        modalTags.textContent = "Tags: " + tags;
        commentPostId.value = postId;

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

function displayComments(comments) {
    const commentsSection = document.getElementById('commentsSection');
    commentsSection.innerHTML = ''; // Clear previous comments
    const parsedComments = JSON.parse(comments);
    parsedComments.forEach(comment => {
        const commentDiv = document.createElement('div');
        commentDiv.className = 'comment';
        const commentText = document.createElement('p');
        commentText.innerText = comment.text;
        const commentUser = document.createElement('p');
        commentUser.innerText = 'Comment by: ' + comment.username;
        commentDiv.appendChild(commentText);
        commentDiv.appendChild(commentUser);
        commentsSection.appendChild(commentDiv);
    });
}
