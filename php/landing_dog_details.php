<title>Pawpedia - Dog Details</title>
    <link rel="stylesheet" href="../css/site_layout.css">
    <link rel="stylesheet" href="../css/dog_details.css">
    <style>
        .logo a
        {
            text-decoration: none;
            color: white;
        }
        .search-form
        {
            background-color: #dba181;
            width: 45%;
            max-width: 15%;
            padding-top: 20px;
            padding-left: 20px;
            padding-right: 40px;
        }

        .search-form button
        {
            padding: 10px;
            text-align: center;
            width: 35%;
        }
    </style>
</head>
<body>
<nav class="top-navbar">
        <div class="logo">
        <a href="landing.php"><strong>Pawpedia</strong></a> 
        </div>

        <div class="nav-links">
            <a href="signup.php">Signup</a>
            <a href="login.php">Login</a>
        </div>
    </nav>

    <div class="breadcrumb">
        <?php
        $searchTag = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
        echo "<a href=\"landing.php?search=" . urlencode($searchTag) . "\"> < Back</a>";
        ?>
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
                        echo "</div>";
                        echo "</div>"; 
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
                    event.preventDefault(); 
                    logout(); 
                });
            } else {
                console.error('Logout link element not found.');
            }
        });
    </script>
</body>
</html>
