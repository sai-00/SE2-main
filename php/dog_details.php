<!DOCTYPE html>
<html lang="en">
<head>
    <title>Pawpedia - Dog Details</title>
    <link rel="stylesheet" href="../css/site_layout.css">
    <link rel="stylesheet" href="../css/dog_details.css">
</head>
<body>
    <nav class="top-navbar">
        <div class="logo">
            <strong>Pawpedia</strong>
        </div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="users.php">Users</a>
            <a href="profile.php">Profile</a>
            <a href="#" id="logout">Logout</a>
        </div>
    </nav>

    <div class="breadcrumb">
        <a href="index.php"> < Back</a>
    </div>

    <section class="main-content">
        <?php
        $searchTag = isset($_GET['search']) ? strtolower(htmlspecialchars($_GET['search'])) : '';

        if (!empty($searchTag)) {
            $dogsFile = '../json/dog_details.json';
            if (file_exists($dogsFile)) {
                $dogs = json_decode(file_get_contents($dogsFile), true);
                foreach ($dogs as $dog) {
                    if (strtolower($dog['breed']) === $searchTag) {
                        echo "<div class='dog-info'>";
                        echo "<img src='" . htmlspecialchars($dog['image']) . "' alt='Dog Image'>";
                        echo "<div class='details'>";
                        echo "<h1>" . htmlspecialchars($dog['breed']) . "</h1>";
                        echo "<div class='section-bg'>";
                        echo "<h2>Fun Facts</h2>";
                        echo "<div class='justify-text'>" . htmlspecialchars_decode($dog['fun_facts']) . "</div>";
                        echo "</div>";
                        echo "<div class='section-bg'>";
                        echo "<h2>General Care</h2>";
                        echo "<div class='justify-text'>" . htmlspecialchars_decode($dog['general_care']) . "</div>";
                        echo "</div>";
                        echo "<div class='section-bg'>";
                        echo "<h2>Sources</h2>";
                        echo "<div class='justify-text'>" . htmlspecialchars_decode($dog['sources']) . "</div>";
                        echo "</div>";
                        echo "</div>"; // Close details
                        echo "</div>"; // Close dog-info
                    }
                }
            } else {
                echo "<p>No dog entries available.</p>";
            }
        } else {
            echo "<p>No breed specified.</p>";
        }
        ?>
    </section>

    <script type="module">
        function logout() {
            window.location.href = "login.php";
        }

        document.addEventListener('DOMContentLoaded', function() {
            var logoutLink = document.getElementById('logout');
            if (logoutLink) {
                logoutLink.addEventListener('click', function(event) {
                    event.preventDefault(); // Prevent default link behavior
                    logout(); // Call logout function
                });
            } else {
                console.error('Logout link element not found.');
            }
        });
    </script>
</body>
</html>
