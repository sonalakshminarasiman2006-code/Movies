<?php
require_once "../config/database.php";

$query = "SELECT * FROM movies";
$result = mysqli_query($conn, $query);

if (!$result) {
    die("Database Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Movies | Movie Ticket Booking</title>

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

        /* NAVBAR */
        .navbar {
            width: 100%;
            min-height: 70px;
            background: #ffffff;
            border-bottom: 3px solid #e50914;

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 0 40px;

            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
        }

        .logo {
            color: #e50914;
            font-size: 25px;
            font-weight: bold;
        }

        .nav-links {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .nav-links a {
            color: #333;
            text-decoration: none;
            padding: 10px 17px;
            border-radius: 7px;
            font-size: 15px;
            font-weight: 600;
            transition: 0.3s;
        }

        .nav-links a:hover {
            background: #ffe5e5;
            color: #e50914;
        }

        .logout {
            background: #e50914 !important;
            color: white !important;
        }

        .logout:hover {
            background: #b20710 !important;
        }

        /* MAIN */
        .container {
            width: 90%;
            max-width: 1200px;
            margin: 35px auto;
        }

        .top-section {
            margin-bottom: 20px;
        }

        .back-btn {
            display: inline-block;
            text-decoration: none;
            color: #e50914;
            background: #ffffff;
            border: 2px solid #e50914;

            padding: 10px 18px;
            border-radius: 8px;

            font-weight: bold;
            transition: 0.3s;
        }

        .back-btn:hover {
            background: #e50914;
            color: white;
        }

        .page-title {
            text-align: center;
            color: #e50914;
            font-size: 34px;
            margin: 25px 0 35px;
        }

        /* MOVIE GRID */
        .movies {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 25px;
        }

        .movie-card {
            background: #ffffff;

            border: 1px solid #eeeeee;
            border-top: 5px solid #e50914;

            border-radius: 14px;
            padding: 25px;

            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);

            transition: 0.3s;
        }

        .movie-card:hover {
            transform: translateY(-6px);

            box-shadow: 0 10px 25px rgba(229, 9, 20, 0.18);
        }

        .movie-card h2 {
            color: #222;
            margin-bottom: 18px;
            font-size: 22px;
        }

        .movie-card p {
            color: #555;
            margin: 10px 0;
            line-height: 1.5;
        }

        .movie-card strong {
            color: #e50914;
        }

        /* BOOK BUTTON */
        .book-btn {
            display: block;
            text-align: center;

            margin-top: 20px;
            padding: 12px;

            background: #e50914;
            color: white;

            text-decoration: none;
            border-radius: 8px;

            font-weight: bold;

            transition: 0.3s;
        }

        .book-btn:hover {
            background: #b20710;
        }

        /* NO MOVIES */
        .no-movies {
            background: white;
            padding: 45px;
            text-align: center;

            border-radius: 14px;
            border: 1px solid #eeeeee;

            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }

        .no-movies h2 {
            color: #e50914;
        }

        .no-movies p {
            margin-top: 10px;
            color: #666;
        }

        /* MOBILE */
        @media (max-width: 700px) {

            .navbar {
                padding: 18px;
                flex-direction: column;
                gap: 15px;
            }

            .nav-links {
                flex-wrap: wrap;
                justify-content: center;
            }

            .container {
                width: 92%;
            }

            .page-title {
                font-size: 28px;
            }
        }
    </style>
</head>

<body>

<!-- NAVIGATION -->
<nav class="navbar">

    <div class="logo">
        🎬 MOVIE BOOKING
    </div>

    <div class="nav-links">

        <a href="../index.php">
            Home
        </a>

        <a href="../profile.php">
            Profile
        </a>

        <a href="../logout.php" class="logout">
            Logout
        </a>

    </div>

</nav>


<!-- CONTENT -->
<div class="container">

    <div class="top-section">

        <a href="javascript:history.back()" class="back-btn">
            ← Back
        </a>

    </div>

    <h1 class="page-title">
        🎬 Available Movies
    </h1>


    <div class="movies">

        <?php if (mysqli_num_rows($result) > 0): ?>

            <?php while ($movie = mysqli_fetch_assoc($result)): ?>

                <?php
                    $movieId = $movie['id'] ?? '';

                    $movieName =
                        $movie['title']
                        ?? $movie['movie_name']
                        ?? $movie['name']
                        ?? 'Movie';

                    $genre = $movie['genre'] ?? '';
                    $duration = $movie['duration'] ?? '';
                    $price = $movie['price'] ?? '';
                ?>

                <div class="movie-card">

                    <h2>
                        <?php echo htmlspecialchars($movieName); ?>
                    </h2>

                    <?php if ($genre != ''): ?>

                        <p>
                            <strong>Genre:</strong>
                            <?php echo htmlspecialchars($genre); ?>
                        </p>

                    <?php endif; ?>


                    <?php if ($duration != ''): ?>

                        <p>
                            <strong>Duration:</strong>
                            <?php echo htmlspecialchars($duration); ?>
                        </p>

                    <?php endif; ?>


                    <?php if ($price != ''): ?>

                        <p>
                            <strong>Ticket Price:</strong>
                            ₹<?php echo htmlspecialchars($price); ?>
                        </p>

                    <?php endif; ?>


                    <?php if ($movieId != ''): ?>

                        <a
                            href="../booking.php?movie_id=<?php echo urlencode($movieId); ?>"
                            class="book-btn"
                        >
                            Book Ticket
                        </a>

                    <?php endif; ?>

                </div>

            <?php endwhile; ?>


        <?php else: ?>

            <div class="no-movies">

                <h2>No Movies Available</h2>

                <p>
                    Movies will appear here once they are added.
                </p>

            </div>

        <?php endif; ?>

    </div>

</div>

</body>
</html>