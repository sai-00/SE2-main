document.addEventListener('DOMContentLoaded', function() {
    const usersContainer = document.getElementById("user-list");
    const userSearchbar = document.getElementById('user-searchbar');
    const usersData = JSON.parse(userSearchbar.dataset.users);

    if (usersData && usersData.length > 0) {
        renderUserList(usersData);
    } else {
        usersContainer.innerHTML = "<p>No users available.</p>";
    }

    function createCard(user) {
        const card = document.createElement("div");
        card.classList.add("usersCard");

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

    function renderUserList(users) {
        usersContainer.innerHTML = ''; // Clear existing users

        if (users.length === 0) {
            usersContainer.innerHTML = '<p>No users found.</p>';
            return;
        }

        users.forEach(user => {
            if (user.username) {
                const card = createCard(user);
                usersContainer.appendChild(card);
            } else {
                console.warn("Skipping user with missing username:", user);
            }
        });
    }

    function searchUsers(query) {
        const lowerCaseQuery = query.toLowerCase();
        const filteredUsers = usersData.filter(user =>
            user.username.toLowerCase().includes(lowerCaseQuery)
        );
        renderUserList(filteredUsers);
    }

    userSearchbar.addEventListener('input', function(event) {
        const query = event.target.value;
        searchUsers(query);
    });

    // Initial render of all users
    renderUserList(usersData);
});
