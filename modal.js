document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('myModal');
    const modalImage = document.getElementById('modalImage');
    const modalText = document.getElementById('modalText');

    // Function to open the modal with post details
    window.openModal = function(imageSrc, text) {
        console.log('Opening modal with image:', imageSrc, 'and text:', text);
        modalImage.src = imageSrc;
        modalText.textContent = text;
        modal.style.display = "block";
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
