document.addEventListener('DOMContentLoaded', function() {
    // Fetch users from users.json
    fetch('../json/users.json')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(users => {
            const usersContainer = document.getElementById("user-list");

            // Iterates through each user and creates a card for it, then displays it in the users container
            if (users && users.length > 0) {
                users.forEach(user => {
                    const card = createCard(user);
                    usersContainer.appendChild(card);
                });
            } else {
                usersContainer.innerHTML = "<p>No users available.</p>";
            }
        })
        .catch(error => {
            console.error("Error fetching users:", error);
            const usersContainer = document.getElementById("user-list");
            usersContainer.innerHTML = "<p>Error loading users.</p>";
        });

    // Function to create a user card
    function createCard(user) {
        const card = document.createElement("div");
        card.classList.add("usersCard");

        const image = document.createElement("img");
        image.src = user.image || "../img/braver-blank-pfp.jpg"; 
        card.appendChild(image);

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
