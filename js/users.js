document.addEventListener('DOMContentLoaded', function() {
    const usersContainer = document.getElementById("user-list");

    if (usersData && usersData.length > 0) {
        usersData.forEach(user => {
            if (user.username) { // Ensure that the user has a username
                const card = createCard(user);
                usersContainer.appendChild(card);
            } else {
                console.warn("Skipping user with missing username:", user); // Debugging line
            }
        });
    } else {
        usersContainer.innerHTML = "<p>No users available.</p>";
    }

    function createCard(user) {
        const card = document.createElement("div");
        card.classList.add("usersCard");

        // const image = document.createElement("img");
        // image.src = user.image || "../img/braver-blank-pfp.jpg"; 
        // card.appendChild(image);

        const name = document.createElement("h2");
        name.textContent = user.username;
        card.appendChild(name);

        const viewProfile = document.createElement("button");
        viewProfile.textContent = "View profile";
        viewProfile.addEventListener("click", () => {
            window.location.href = "../php/userpage.php?id=" + encodeURIComponent(user.username);
        });
        card.appendChild(viewProfile);

        return card;
    }
});
