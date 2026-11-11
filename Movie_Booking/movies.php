<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once "database.php";


/*
|--------------------------------------------------------------------------
| LOGIN CHECK
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


/*
|--------------------------------------------------------------------------
| LANGUAGE FILTER
|--------------------------------------------------------------------------
*/

$language = $_GET['language'] ?? 'all';


/*
|--------------------------------------------------------------------------
| MOVIE QUERY
|--------------------------------------------------------------------------
*/

if ($language === 'tamil') {

    $sql = "
        SELECT
            id,
            title,
            movie_language,
            genre,
            duration,
            price,
            description,
            created_at
        FROM movies
        WHERE LOWER(TRIM(movie_language)) = 'tamil'
        ORDER BY id DESC
    ";

} elseif ($language === 'english') {

    $sql = "
        SELECT
            id,
            title,
            movie_language,
            genre,
            duration,
            price,
            description,
            created_at
        FROM movies
        WHERE LOWER(TRIM(movie_language)) = 'english'
        ORDER BY id DESC
    ";

} else {

    $sql = "
        SELECT
            id,
            title,
            movie_language,
            genre,
            duration,
            price,
            description,
            created_at
        FROM movies
        ORDER BY id DESC
    ";
}


/*
|--------------------------------------------------------------------------
| EXECUTE QUERY
|--------------------------------------------------------------------------
*/

$result = mysqli_query($conn, $sql);

if (!$result) {

    die(
        "<div style='
            padding:30px;
            font-family:Arial;
            color:#c00000;
        '>
            <h2>Movie Query Error</h2>
            <p>" .
            htmlspecialchars(mysqli_error($conn))
            .
            "</p>
        </div>"
    );
}

?>


<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>CineBook | Movies</title>


<style>

/* ==================================================
   RESET
================================================== */

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}


/* ==================================================
   BODY
================================================== */

body {

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    background: #f7f7f7;

    color: #151515;

}


/* ==================================================
   HEADER
================================================== */

.header {

    height: 76px;

    background: #ffffff;

    border-bottom:
        1px solid #eeeeee;

    display: flex;

    align-items: center;

}


.header-inner {

    width: 92%;

    max-width: 1250px;

    margin: auto;

    display: flex;

    align-items: center;

    justify-content:
        space-between;

}


/* LOGO */

.logo {

    text-decoration: none;

    color: #111111;

    font-size: 25px;

    font-weight: 900;

    letter-spacing: -1px;

}


.logo span {

    color: #e50914;

}


/* NAVIGATION */

.nav {

    display: flex;

    align-items: center;

    gap: 28px;

}


.nav a {

    text-decoration: none;

    color: #555555;

    font-size: 12px;

    font-weight: 700;

    transition: .2s;

}


.nav a:hover {

    color: #e50914;

}


.nav a.active {

    color: #e50914;

}


/* ==================================================
   MAIN
================================================== */

.main {

    width: 92%;

    max-width: 1250px;

    margin: auto;

    padding:
        50px 0 80px;

}


/* ==================================================
   PAGE TITLE
================================================== */

.heading {

    margin-bottom: 30px;

}


.heading-label {

    color: #e50914;

    font-size: 9px;

    font-weight: 900;

    letter-spacing: 3px;

}


.heading h1 {

    font-size: 42px;

    margin-top: 8px;

    letter-spacing: -1.5px;

}


.heading p {

    margin-top: 9px;

    color: #888888;

    font-size: 12px;

}


/* ==================================================
   FILTER BUTTONS
================================================== */

.filters {

    display: flex;

    gap: 10px;

    margin-bottom: 32px;

}


.filter {

    text-decoration: none;

    padding:
        11px 23px;

    border-radius: 8px;

    border:
        1px solid #dddddd;

    background: #ffffff;

    color: #555555;

    font-size: 11px;

    font-weight: 800;

    transition: .2s;

}


.filter:hover {

    border-color: #e50914;

    color: #e50914;

}


.filter.active {

    background: #e50914;

    border-color: #e50914;

    color: #ffffff;

}


/* ==================================================
   MOVIE GRID
================================================== */

.movie-grid {

    display: grid;

    grid-template-columns:
        repeat(4, 1fr);

    gap: 20px;

}


/* ==================================================
   MOVIE CARD
================================================== */

.movie-card {

    background: #ffffff;

    border:
        1px solid #eeeeee;

    border-radius: 16px;

    overflow: hidden;

    transition:
        transform .25s,
        box-shadow .25s,
        border-color .25s;

}


.movie-card:hover {

    transform:
        translateY(-6px);

    border-color:
        #f0b8bc;

    box-shadow:
        0 18px 45px
        rgba(0,0,0,.08);

}


/* ==================================================
   MOVIE COVER
================================================== */

.movie-cover {

    height: 180px;

    background:
        linear-gradient(
            145deg,
            #111111,
            #450509
        );

    position: relative;

    display: flex;

    align-items: center;

    justify-content: center;

}


.movie-icon {

    font-size: 50px;

    opacity: .15;

}


.movie-language {

    position: absolute;

    top: 14px;

    left: 14px;

    background: #e50914;

    color: #ffffff;

    padding:
        6px 10px;

    border-radius: 5px;

    font-size: 8px;

    font-weight: 900;

    text-transform: uppercase;

}


/* ==================================================
   MOVIE CONTENT
================================================== */

.movie-content {

    padding: 20px;

}


.movie-title {

    font-size: 18px;

    font-weight: 800;

    margin-bottom: 9px;

}


.movie-description {

    color: #888888;

    font-size: 10px;

    line-height: 1.7;

    min-height: 40px;

    margin-bottom: 16px;

}


/* ==================================================
   MOVIE INFO
================================================== */

.movie-info {

    display: flex;

    flex-wrap: wrap;

    gap: 7px;

    margin-bottom: 18px;

}


.info {

    background: #f5f5f5;

    color: #666666;

    padding:
        6px 9px;

    border-radius: 5px;

    font-size: 9px;

}


.price {

    color: #e50914;

    font-weight: 900;

}


/* ==================================================
   BOOK BUTTON
================================================== */

.book-button {

    display: block;

    width: 100%;

    text-align: center;

    text-decoration: none;

    background: #e50914;

    color: #ffffff;

    padding: 13px;

    border-radius: 8px;

    font-size: 10px;

    font-weight: 900;

    transition: .2s;

}


.book-button:hover {

    background: #b70710;

}


/* ==================================================
   EMPTY STATE
================================================== */

.empty {

    background: #ffffff;

    border:
        1px solid #eeeeee;

    border-radius: 16px;

    padding: 70px 25px;

    text-align: center;

}


.empty-icon {

    font-size: 42px;

    margin-bottom: 15px;

}


.empty h2 {

    font-size: 20px;

    margin-bottom: 8px;

}


.empty p {

    color: #888888;

    font-size: 11px;

}


/* ==================================================
   FOOTER
================================================== */

.footer {

    background: #111111;

    color: #777777;

    text-align: center;

    padding: 25px;

    font-size: 9px;

}


/* ==================================================
   RESPONSIVE
================================================== */

@media (max-width: 1050px) {

    .movie-grid {

        grid-template-columns:
            repeat(3, 1fr);

    }

}


@media (max-width: 760px) {

    .nav {

        display: none;

    }

    .movie-grid {

        grid-template-columns:
            repeat(2, 1fr);

    }

}


@media (max-width: 500px) {

    .movie-grid {

        grid-template-columns: 1fr;

    }

    .heading h1 {

        font-size: 34px;

    }

    .filters {

        flex-wrap: wrap;

    }

}

</style>

</head>


<body>


<!-- ==================================================
     HEADER
================================================== -->

<header class="header">

    <div class="header-inner">


        <a
            href="dashboard.php"
            class="logo"
        >

            CINE<span>BOOK</span>

        </a>


        <nav class="nav">


            <a href="dashboard.php">
                Home
            </a>


            <a
                href="movies.php"
                class="active"
            >

                Movies

            </a>


            <a href="bookings.php">
                Bookings
            </a>


            <a href="profile.php">
                Profile
            </a>


            <a href="logout.php">
                Logout
            </a>


        </nav>


    </div>

</header>


<!-- ==================================================
     MAIN
================================================== -->

<main class="main">


    <section class="heading">


        <div class="heading-label">
            CINEBOOK COLLECTION
        </div>


        <h1>
            Movies
        </h1>


        <p>
            Choose your language and book your favourite movie.
        </p>


    </section>


    <!-- ==================================================
         FILTERS
    ================================================== -->

    <div class="filters">


        <a
            href="movies.php"
            class="filter
            <?php
            echo $language === 'all'
                ? 'active'
                : '';
            ?>"
        >

            All Movies

        </a>


        <a
            href="movies.php?language=tamil"
            class="filter
            <?php
            echo $language === 'tamil'
                ? 'active'
                : '';
            ?>"
        >

            Tamil

        </a>


        <a
            href="movies.php?language=english"
            class="filter
            <?php
            echo $language === 'english'
                ? 'active'
                : '';
            ?>"
        >

            English

        </a>


    </div>


    <!-- ==================================================
         MOVIES
    ================================================== -->


    <?php if (mysqli_num_rows($result) > 0): ?>


        <div class="movie-grid">


            <?php while ($movie = mysqli_fetch_assoc($result)): ?>


                <article class="movie-card">


                    <div class="movie-cover">


                        <div class="movie-icon">
                            🎬
                        </div>


                        <div class="movie-language">

                            <?php

                            echo htmlspecialchars(
                                $movie['movie_language']
                            );

                            ?>

                        </div>


                    </div>


                    <div class="movie-content">


                        <h2 class="movie-title">

                            <?php

                            echo htmlspecialchars(
                                $movie['title']
                            );

                            ?>

                        </h2>


                        <p class="movie-description">

                            <?php

                            echo htmlspecialchars(
                                $movie['description']
                            );

                            ?>

                        </p>


                        <div class="movie-info">


                            <span class="info">

                                <?php

                                echo htmlspecialchars(
                                    $movie['genre']
                                );

                                ?>

                            </span>


                            <span class="info">

                                <?php

                                echo htmlspecialchars(
                                    $movie['duration']
                                );

                                ?>

                            </span>


                            <span class="info price">

                                ₹<?php

                                echo htmlspecialchars(
                                    $movie['price']
                                );

                                ?>

                            </span>


                        </div>


                        <a
                            href="booking.php?movie_id=<?php
                            echo (int)$movie['id'];
                            ?>"
                            class="book-button"
                        >

                            BOOK NOW

                        </a>


                    </div>


                </article>


            <?php endwhile; ?>


        </div>


    <?php else: ?>


        <div class="empty">


            <div class="empty-icon">
                🎬
            </div>


            <h2>
                No Movies Found
            </h2>


            <p>
                No movies are available in this category.
            </p>


        </div>


    <?php endif; ?>


</main>


<!-- ==================================================
     FOOTER
================================================== -->

<footer class="footer">

    © <?php echo date('Y'); ?>
    CineBook. All rights reserved.

</footer>


</body>

</html>