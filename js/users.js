// users.js

// Array of users
const userList = [
    { id: 1, image: "../img/braver-blank-pfp.jpg", name: "yuh", description: "lorem ipsum" },
    { id: 2, image: "../img/braver-blank-pfp.jpg", name: "yaur", description: "lorem ipsum" },
    { id: 3, image: "../img/braver-blank-pfp.jpg", name: "hey", description: "lorem ipsum" },
    { id: 4, image: "../img/braver-blank-pfp.jpg", name: "henl", description: "lorem ipsum" },
    { id: 5, image: "../img/braver-blank-pfp.jpg", name: "aye", description: "lorem ipsum" },
    { id: 6, image: "../img/braver-blank-pfp.jpg", name: "naur", description: "lorem ipsum" },
    { id: 7, image: "../img/braver-blank-pfp.jpg", name: "womp", description: "lorem ipsum" },
    { id: 8, image: "../img/braver-blank-pfp.jpg", name: "hronk", description: "lorem ipsum" },
    { id: 9, image: "../img/braver-blank-pfp.jpg", name: "mimmi", description: "lorem ipsum" }
];

// Add to local storage
localStorage.setItem("userList", JSON.stringify(userList));
const userArray = localStorage.getItem("userList");

// Converts the text into an object
const users = JSON.parse(userArray);
console.log(users);

const usersContainer = document.getElementById("user-list");

// Iterates through each item and creates a card for it then displays it in the users container
if (users) {
    users.forEach(user => {
        const card = createCard(user);
        usersContainer.appendChild(card);
    });
} else {
    usersContainer.innerHTML = "<p>No user available.</p>";
}

function createCard(user) {
    const card = document.createElement("div");
    card.classList.add("usersCard");

    const image = document.createElement("img");
    image.src = user.image;
    card.appendChild(image);

    const name = document.createElement("h2");
    name.textContent = user.name;
    card.appendChild(name);

    const description = document.createElement("p");
    description.textContent = user.description;
    card.appendChild(description);

    const viewProfile = document.createElement("button");
    viewProfile.textContent = "View profile";
    viewProfile.addEventListener("click", () => {
        window.location.href = "userpage.php?id=" + user.id;
    });
    card.appendChild(viewProfile);

    return card;
}
