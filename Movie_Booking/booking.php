<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once "database.php";


// ==================================================
// LOGIN CHECK
// ==================================================

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


// ==================================================
// GET MOVIE ID
// ==================================================

$movie_id = isset($_GET['movie_id'])
    ? (int) $_GET['movie_id']
    : 0;


if ($movie_id <= 0) {
    header("Location: movies.php");
    exit();
}


// ==================================================
// GET MOVIE
// ==================================================

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        id,
        title,
        movie_language,
        genre,
        duration,
        price,
        description
     FROM movies
     WHERE id = ?"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $movie_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$movie = mysqli_fetch_assoc($result);


if (!$movie) {
    header("Location: movies.php");
    exit();
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>CineBook | Book Ticket</title>


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
    font-family: Arial, Helvetica, sans-serif;
    background: #f7f7f7;
    color: #151515;
}


/* ==================================================
   HEADER
================================================== */

.header {
    height: 76px;
    background: #ffffff;
    border-bottom: 1px solid #eeeeee;
    display: flex;
    align-items: center;
}

.header-inner {
    width: 92%;
    max-width: 1250px;
    margin: auto;

    display: flex;
    align-items: center;
    justify-content: space-between;
}


.logo {
    text-decoration: none;
    color: #111111;

    font-size: 25px;
    font-weight: 900;
}

.logo span {
    color: #e50914;
}


.nav {
    display: flex;
    gap: 28px;
}


.nav a {
    text-decoration: none;
    color: #555555;

    font-size: 12px;
    font-weight: 700;
}


.nav a:hover {
    color: #e50914;
}


/* ==================================================
   MAIN
================================================== */

.main {
    width: 92%;
    max-width: 900px;

    margin: auto;

    padding: 50px 0 80px;
}


/* ==================================================
   TITLE
================================================== */

.heading {
    margin-bottom: 30px;
}


.heading small {
    color: #e50914;

    font-size: 9px;
    font-weight: 900;

    letter-spacing: 3px;
}


.heading h1 {
    margin-top: 8px;

    font-size: 38px;

    letter-spacing: -1px;
}


.heading p {
    margin-top: 8px;

    color: #888888;

    font-size: 11px;
}


/* ==================================================
   BOOKING CARD
================================================== */

.booking-card {
    background: #ffffff;

    border: 1px solid #eeeeee;

    border-radius: 18px;

    padding: 30px;

    box-shadow:
        0 15px 40px rgba(0, 0, 0, 0.05);
}


/* ==================================================
   MOVIE HEADER
================================================== */

.movie-header {

    background:
        linear-gradient(
            135deg,
            #111111,
            #470609
        );

    color: #ffffff;

    border-radius: 14px;

    padding: 25px;

    margin-bottom: 30px;
}


.movie-header h2 {
    font-size: 26px;

    margin-bottom: 10px;
}


.movie-header p {
    color: #dddddd;

    font-size: 10px;

    line-height: 1.8;
}


/* ==================================================
   FORM
================================================== */

.form-group {
    margin-bottom: 23px;
}


.form-group label {
    display: block;

    margin-bottom: 9px;

    font-size: 11px;

    font-weight: 800;
}


.form-group input,
.form-group select {

    width: 100%;

    height: 48px;

    padding: 0 14px;

    border: 1px solid #dddddd;

    border-radius: 8px;

    background: #ffffff;

    color: #222222;

    font-size: 12px;

    outline: none;
}


.form-group input:focus,
.form-group select:focus {
    border-color: #e50914;

    box-shadow:
        0 0 0 3px rgba(229, 9, 20, 0.08);
}


/* ==================================================
   BUTTON
================================================== */

.continue-button {

    width: 100%;

    height: 50px;

    border: none;

    border-radius: 8px;

    background: #e50914;

    color: #ffffff;

    font-size: 11px;

    font-weight: 900;

    cursor: pointer;

    transition: 0.2s;
}


.continue-button:hover {
    background: #b70710;
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

@media (max-width: 700px) {

    .nav {
        display: none;
    }

    .booking-card {
        padding: 20px;
    }

    .heading h1 {
        font-size: 32px;
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

            <a href="movies.php">
                Movies
            </a>

            <a href="bookings.php">
                My Bookings
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


    <div class="heading">

        <small>
            STEP 1 OF 4
        </small>

        <h1>
            Book Your Ticket
        </h1>

        <p>
            Select your preferred date and show time.
        </p>

    </div>


    <div class="booking-card">


        <!-- ==================================================
             MOVIE
        ================================================== -->

        <div class="movie-header">

            <h2>

                <?php

                echo htmlspecialchars(
                    $movie['title']
                );

                ?>

            </h2>


            <p>

                <?php

                echo htmlspecialchars(
                    $movie['movie_language']
                );

                ?>

                &nbsp; • &nbsp;

                <?php

                echo htmlspecialchars(
                    $movie['genre']
                );

                ?>

                &nbsp; • &nbsp;

                <?php

                echo htmlspecialchars(
                    $movie['duration']
                );

                ?>

                &nbsp; • &nbsp;

                ₹<?php

                echo number_format(
                    (float) $movie['price'],
                    2
                );

                ?>

                / seat

            </p>

        </div>


        <!-- ==================================================
             IMPORTANT:
             THIS FORM SENDS ALL DATA TO SEAT SELECTION
        ================================================== -->

        <form
            action="seat_selection.php"
            method="POST"
            id="bookingForm"
        >


            <!-- MOVIE ID -->

            <input
                type="hidden"
                name="movie_id"
                value="<?php
                echo (int) $movie['id'];
                ?>"
            >


            <!-- BOOKING DATE -->

            <div class="form-group">

                <label for="booking_date">
                    Show Date
                </label>


                <input
                    type="date"
                    id="booking_date"
                    name="booking_date"
                    min="<?php
                    echo date('Y-m-d');
                    ?>"
                    required
                >

            </div>


            <!-- SHOW TIME -->

            <div class="form-group">

                <label for="show_time">
                    Show Time
                </label>


                <select
                    id="show_time"
                    name="show_time"
                    required
                >

                    <option value="">
                        Select Show Time
                    </option>

                    <option value="10:00 AM">
                        10:00 AM
                    </option>

                    <option value="01:30 PM">
                        01:30 PM
                    </option>

                    <option value="04:30 PM">
                        04:30 PM
                    </option>

                    <option value="07:30 PM">
                        07:30 PM
                    </option>

                    <option value="10:30 PM">
                        10:30 PM
                    </option>

                </select>

            </div>


            <!-- CONTINUE -->

            <button
                type="submit"
                class="continue-button"
            >

                CONTINUE TO SEAT SELECTION →

            </button>


        </form>


    </div>

</main>


<footer class="footer">

    © <?php echo date('Y'); ?>
    CineBook. All rights reserved.

</footer>


</body>

</html>