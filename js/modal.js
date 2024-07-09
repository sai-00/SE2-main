document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('myModal');
    const modalImage = document.getElementById('modalImage');
    const modalText = document.getElementById('modalText');

    // Function to open the modal with post details
    window.openModal = function(imageSrc, text) {
        document.getElementById('modalImage').src = imageSrc;
        document.getElementById('modalText').innerText = text;
        document.getElementById('commentPostId').value = postId;
        document.getElementById('commentsSection').innerHTML = comments.map(comment => {
            return `<p><strong>${comment.username}:</strong> ${comment.text}</p>`;
        }).join('');
        document.getElementById('myModal').style.display = 'block';
    }

    // Function to close the modal
    window.closeModal = function() {
        modal.style.display = "none";
    }

    // Close the modal if user clicks outside of it
    window.onclick = function(event) {
        if (event.target === modal) {
            closeModal();
        }
    }
});