<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Home | Movie Ticket Booking</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            background: #fff5f5;
            color: #222;
            min-height: 100vh;
        }

        .navbar {
            background: #ffffff;
            border-bottom: 3px solid #e50914;
            padding: 18px 40px;

            display: flex;
            justify-content: space-between;
            align-items: center;

            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        }

        .logo {
            color: #e50914;
            font-size: 25px;
            font-weight: bold;
        }

        .nav-links {
            display: flex;
            gap: 10px;
        }

        .nav-links a {
            text-decoration: none;
            color: #333;
            padding: 10px 17px;
            border-radius: 7px;
            font-weight: bold;
        }

        .nav-links a:hover {
            background: #ffe5e5;
            color: #e50914;
        }

        .logout {
            background: #e50914 !important;
            color: white !important;
        }

        .hero {
            max-width: 1000px;
            margin: 70px auto;
            padding: 70px 30px;

            text-align: center;

            background: white;
            border-radius: 20px;

            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
            border-top: 6px solid #e50914;
        }

        .hero h1 {
            color: #e50914;
            font-size: 42px;
            margin-bottom: 20px;
        }

        .hero p {
            color: #555;
            font-size: 18px;
            margin-bottom: 30px;
        }

        .buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 13px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }

        .primary {
            background: #e50914;
            color: white;
        }

        .primary:hover {
            background: #b20710;
        }

        .secondary {
            background: white;
            color: #e50914;
            border: 2px solid #e50914;
        }

        .secondary:hover {
            background: #ffe5e5;
        }

        @media (max-width: 700px) {

            .navbar {
                flex-direction: column;
                gap: 15px;
                padding: 18px;
            }

            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }

            .hero {
                width: 90%;
                margin: 40px auto;
                padding: 50px 20px;
            }

            .hero h1 {
                font-size: 32px;
            }
        }
    </style>
</head>

<body>

<nav class="navbar">

    <div class="logo">
        🎬 MOVIE BOOKING
    </div>

    <div class="nav-links">

        <a href="home.php">Home</a>

        <a href="movies/index.php">Movies</a>

        <a href="profile.php">Profile</a>

        <a href="logout.php" class="logout">Logout</a>

    </div>

</nav>


<div class="hero">

    <h1>Welcome to Movie Booking</h1>

    <p>
        Book your favourite movie tickets quickly and easily.
    </p>

    <div class="buttons">

        <a href="movies/index.php" class="btn primary">
            Browse Movies
        </a>

        <a href="profile.php" class="btn secondary">
            My Profile
        </a>

    </div>

</div>

</body>
</html>