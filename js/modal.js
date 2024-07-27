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
        modalUsername.innerHTML = `Posted by: <a href="userpage.php?id=${username}">${username}</a>`;
        const tagArray = JSON.parse(tags);
        modalTags.innerHTML = "Tags: " + tagArray.map(tag => `<a href="index.php?search=${encodeURIComponent(tag)}">${tag}</a>`).join(", ");
        commentPostId.value = postId;
        
        var img = document.getElementById('modalImage');//new code--------------------------------
        var randomTilt = (Math.random() * 4 - 2); 
        img.style.transform = 'rotate(' + randomTilt + 'deg)';//end---

        commentsSection.innerHTML = ''; // Clear existing comments
        const commentsArray = JSON.parse(comments).reverse(); 
        commentsArray.forEach(function(comment) {
            const commentDiv = document.createElement('div');
            commentDiv.innerHTML = `<div class="comment-box"><strong><a href="userpage.php?id=${comment.username}">${comment.username}</a></strong>: ${comment.text}</div>`; // Username bold and clickable
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
