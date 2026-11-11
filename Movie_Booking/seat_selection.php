<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once "database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}


/* ==================================================
   GET MOVIE ID
================================================== */

$movie_id = isset($_POST['movie_id'])
    ? (int)$_POST['movie_id']
    : (isset($_GET['movie_id'])
        ? (int)$_GET['movie_id']
        : 0);


/* ==================================================
   GET DATE
================================================== */

$show_date = $_POST['booking_date']
    ?? $_POST['show_date']
    ?? $_GET['booking_date']
    ?? $_GET['show_date']
    ?? '';

$show_date = trim($show_date);


/* ==================================================
   GET TIME
================================================== */

$show_time = $_POST['show_time']
    ?? $_GET['show_time']
    ?? '';

$show_time = trim($show_time);


/* ==================================================
   VALIDATION
================================================== */

if ($movie_id <= 0) {
    die("Movie was not received.");
}

if ($show_date === '') {
    die("Show date was not received.");
}

if ($show_time === '') {
    die("Show time was not received.");
}


/* ==================================================
   GET MOVIE
================================================== */

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        id,
        title,
        movie_language,
        genre,
        price
     FROM movies
     WHERE id = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $movie_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$movie = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


if (!$movie) {
    header("Location: movies.php");
    exit();
}


/* ==================================================
   GET BOOKED SEATS
   SAME MOVIE + DATE + TIME
================================================== */

$booked_seats = [];

$stmt = mysqli_prepare(
    $conn,
    "SELECT seats
     FROM bookings
     WHERE movie_id = ?
     AND show_date = ?
     AND show_time = ?"
);

if ($stmt) {

    mysqli_stmt_bind_param(
        $stmt,
        "iss",
        $movie_id,
        $show_date,
        $show_time
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    while ($row = mysqli_fetch_assoc($result)) {

        $seat_string = trim(
            (string)$row['seats']
        );

        if ($seat_string === '') {
            continue;
        }

        $seat_list = explode(
            ',',
            $seat_string
        );

        foreach ($seat_list as $seat) {

            $seat = strtoupper(
                trim($seat)
            );

            if ($seat !== '') {
                $booked_seats[] = $seat;
            }
        }
    }

    mysqli_stmt_close($stmt);
}


/* REMOVE DUPLICATES */

$booked_seats = array_values(
    array_unique($booked_seats)
);


/* ==================================================
   200 SEATS
================================================== */

$total_seats = 200;

$all_seats = [];

for ($i = 1; $i <= $total_seats; $i++) {
    $all_seats[] = "S" . $i;
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

<title>CineBook | Select Seats</title>


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

    background:
        radial-gradient(
            circle at top,
            #fff1f3,
            #f7f7f7 45%,
            #eeeeee
        );

    min-height: 100vh;

    color: #222;
}


/* ==================================================
   HEADER
================================================== */

.header {

    height: 72px;

    background: rgba(
        255,
        255,
        255,
        .95
    );

    border-bottom:
        1px solid #eeeeee;

    display: flex;

    align-items: center;

    position: sticky;

    top: 0;

    z-index: 100;
}


.header-inner {

    width: 92%;

    max-width: 1250px;

    margin: auto;

    display: flex;

    justify-content: space-between;

    align-items: center;
}


.logo {

    text-decoration: none;

    color: #111;

    font-size: 25px;

    font-weight: 900;
}


.logo span {

    color: #e50914;
}


.nav {

    display: flex;

    gap: 25px;
}


.nav a {

    text-decoration: none;

    color: #555;

    font-size: 11px;

    font-weight: 700;
}


.nav a:hover {

    color: #e50914;
}


/* ==================================================
   MAIN
================================================== */

.main {

    width: 94%;

    max-width: 1200px;

    margin: auto;

    padding: 30px 0 80px;
}


/* ==================================================
   BACK BUTTON
================================================== */

.back {

    margin-bottom: 20px;
}


.back a {

    width: 40px;

    height: 40px;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    background: #fff;

    border: 1px solid #ddd;

    border-radius: 50%;

    text-decoration: none;

    color: #222;

    font-size: 21px;

    box-shadow:
        0 5px 20px
        rgba(0,0,0,.08);

    transition: .2s;
}


.back a:hover {

    background: #e50914;

    color: #fff;

    border-color: #e50914;

    transform: translateX(-3px);
}


/* ==================================================
   MOVIE HEADER
================================================== */

.movie-card {

    background:
        linear-gradient(
            135deg,
            #0d0d0d,
            #310407,
            #e50914
        );

    color: white;

    border-radius: 22px;

    padding: 30px;

    margin-bottom: 25px;

    box-shadow:
        0 20px 50px
        rgba(0,0,0,.18);

}


.movie-card h1 {

    font-size: 30px;

    margin-bottom: 8px;
}


.movie-info {

    color: #ddd;

    font-size: 11px;
}


.show-details {

    display: flex;

    gap: 12px;

    flex-wrap: wrap;

    margin-top: 20px;
}


.show-item {

    padding: 10px 16px;

    border-radius: 30px;

    background:
        rgba(
            255,
            255,
            255,
            .13
        );

    border:
        1px solid
        rgba(
            255,
            255,
            255,
            .18
        );

    font-size: 10px;
}


/* ==================================================
   SEAT CARD
================================================== */

.card {

    background: #fff;

    border-radius: 24px;

    padding: 35px;

    box-shadow:
        0 20px 60px
        rgba(0,0,0,.08);

    border:
        1px solid #eeeeee;
}


/* ==================================================
   TITLE
================================================== */

.title {

    text-align: center;

    font-size: 22px;

    font-weight: 900;

    margin-bottom: 7px;
}


.description {

    text-align: center;

    color: #999;

    font-size: 10px;

    margin-bottom: 30px;
}


/* ==================================================
   CINEMA SCREEN
================================================== */

.screen-area {

    text-align: center;

    margin-bottom: 35px;
}


.screen {

    width: 78%;

    max-width: 750px;

    height: 45px;

    margin: auto;

    border-radius:
        50% 50% 10px 10px;

    background:
        linear-gradient(
            to bottom,
            #ffffff,
            #dddddd
        );

    border:
        2px solid #ccc;

    box-shadow:
        0 -10px 30px
        rgba(
            229,
            9,
            20,
            .20
        );

    display: flex;

    align-items: center;

    justify-content: center;

    color: #777;

    font-size: 10px;

    font-weight: 900;

    letter-spacing: 4px;
}


.screen-light {

    width: 70%;

    height: 8px;

    margin: 8px auto 0;

    border-radius: 50%;

    background:
        rgba(
            229,
            9,
            20,
            .18
        );

    filter: blur(5px);
}


/* ==================================================
   LEGEND
================================================== */

.legend {

    display: flex;

    justify-content: center;

    gap: 25px;

    flex-wrap: wrap;

    margin-bottom: 30px;
}


.legend-item {

    display: flex;

    align-items: center;

    gap: 7px;

    color: #777;

    font-size: 9px;
}


.legend-box {

    width: 18px;

    height: 18px;

    border-radius: 5px;
}


.legend-available {

    background: #fff;

    border: 1px solid #bbb;
}


.legend-selected {

    background: #e50914;

    border: 1px solid #e50914;
}


.legend-booked {

    background: #777;

    border: 1px solid #777;
}


/* ==================================================
   SEAT CONTAINER
================================================== */

.seat-wrapper {

    overflow-x: auto;

    padding:
        10px 5px 20px;
}


/* ==================================================
   SEAT GRID
================================================== */

.seat-grid {

    display: grid;

    grid-template-columns:
        repeat(10, 1fr);

    gap: 10px;

    min-width: 850px;

    max-width: 1000px;

    margin: auto;
}


/* ==================================================
   AISLE EFFECT
================================================== */

.seat:nth-child(10n + 6) {

    margin-left: 12px;
}


/* ==================================================
   SEAT
================================================== */

.seat {

    height: 39px;

    border-radius: 8px 8px 5px 5px;

    background: #fff;

    border:
        1px solid #cfcfcf;

    color: #333;

    font-size: 9px;

    font-weight: 800;

    cursor: pointer;

    position: relative;

    transition:
        transform .15s,
        background .15s,
        border .15s;
}


/* Seat bottom */

.seat::after {

    content: "";

    position: absolute;

    left: 5px;

    right: 5px;

    bottom: -3px;

    height: 4px;

    border-radius: 0 0 5px 5px;

    background: #ccc;
}


.seat:hover:not(:disabled) {

    transform:
        translateY(-3px);

    border-color:
        #e50914;

    color:
        #e50914;
}


/* ==================================================
   SELECTED
================================================== */

.seat.selected {

    background:
        linear-gradient(
            135deg,
            #ff3b45,
            #e50914
        );

    border-color:
        #e50914;

    color: white;

    transform:
        translateY(-3px);

    box-shadow:
        0 7px 15px
        rgba(
            229,
            9,
            20,
            .25
        );
}


.seat.selected::after {

    background:
        #b70710;
}


/* ==================================================
   BOOKED
================================================== */

.seat.booked {

    background:
        #777;

    border-color:
        #777;

    color: #fff;

    cursor:
        not-allowed;

    opacity: .65;
}


.seat.booked::after {

    background:
        #555;
}


/* ==================================================
   SELECTED BOX
================================================== */

.selected-box {

    margin-top: 30px;

    padding: 22px;

    border-radius: 15px;

    background:
        linear-gradient(
            135deg,
            #fff6f6,
            #ffffff
        );

    border:
        1px solid #ffd5d5;

    text-align: center;
}


.selected-label {

    color: #999;

    font-size: 8px;

    font-weight: 900;

    letter-spacing: 2px;

    margin-bottom: 8px;
}


.selected-text {

    color: #e50914;

    font-size: 13px;

    font-weight: 900;

    min-height: 18px;
}


/* ==================================================
   BUTTON
================================================== */

.continue-btn {

    width: 100%;

    margin-top: 20px;

    padding: 17px;

    border: none;

    border-radius: 10px;

    background:
        linear-gradient(
            135deg,
            #e50914,
            #b70710
        );

    color: white;

    font-size: 11px;

    font-weight: 900;

    cursor: pointer;

    box-shadow:
        0 10px 25px
        rgba(
            229,
            9,
            20,
            .20
        );

    transition: .2s;
}


.continue-btn:hover:not(:disabled) {

    transform:
        translateY(-2px);

    box-shadow:
        0 14px 30px
        rgba(
            229,
            9,
            20,
            .30
        );
}


.continue-btn:disabled {

    background: #ccc;

    box-shadow: none;

    cursor: not-allowed;
}


/* ==================================================
   FOOTER
================================================== */

.footer {

    background: #111;

    color: #777;

    text-align: center;

    padding: 25px;

    font-size: 9px;
}


/* ==================================================
   MOBILE
================================================== */

@media(max-width:700px) {

    .nav {
        display: none;
    }

    .main {
        width: 94%;
    }

    .card {
        padding: 20px 12px;
    }

    .movie-card {
        padding: 22px;
    }

    .movie-card h1 {
        font-size: 23px;
    }

    .screen {
        width: 90%;
    }

}


/* ==================================================
   SMALL MOBILE
================================================== */

@media(max-width:450px) {

    .screen {
        height: 35px;
        font-size: 8px;
        letter-spacing: 2px;
    }

    .seat-grid {
        gap: 8px;
    }

}

</style>

</head>


<body>


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


<main class="main">


<!-- BACK -->

<div class="back">

<a href="javascript:history.back()">
    ←
</a>

</div>


<!-- MOVIE -->

<div class="movie-card">

<h1>

<?php
echo htmlspecialchars(
    $movie['title']
);
?>

</h1>


<div class="movie-info">

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

₹<?php
echo number_format(
    (float)$movie['price'],
    2
);
?>

</div>


<div class="show-details">

<div class="show-item">

📅

<?php
echo htmlspecialchars(
    $show_date
);
?>

</div>


<div class="show-item">

🕐

<?php
echo htmlspecialchars(
    $show_time
);
?>

</div>

</div>

</div>


<!-- SEAT CARD -->

<div class="card">


<div class="title">

Select Your Seats

</div>


<div class="description">

Choose your preferred seats.
Grey seats are already booked.

</div>


<!-- SCREEN -->

<div class="screen-area">

<div class="screen">

SCREEN

</div>

<div class="screen-light"></div>

</div>


<!-- LEGEND -->

<div class="legend">


<div class="legend-item">

<span
    class="legend-box
           legend-available"
></span>

Available

</div>


<div class="legend-item">

<span
    class="legend-box
           legend-selected"
></span>

Selected

</div>


<div class="legend-item">

<span
    class="legend-box
           legend-booked"
></span>

Booked

</div>


</div>


<!-- FORM -->

<form
    action="payment.php"
    method="POST"
    id="seatForm"
>


<input
    type="hidden"
    name="movie_id"
    value="<?php
        echo $movie_id;
    ?>"
>


<input
    type="hidden"
    name="booking_date"
    value="<?php
        echo htmlspecialchars(
            $show_date
        );
    ?>"
>


<input
    type="hidden"
    name="show_time"
    value="<?php
        echo htmlspecialchars(
            $show_time
        );
    ?>"
>


<input
    type="hidden"
    name="selected_seats"
    id="selected_seats"
>


<div class="seat-wrapper">

<div class="seat-grid">


<?php foreach (
    $all_seats as $seat
): ?>

<?php

$is_booked =
    in_array(
        $seat,
        $booked_seats,
        true
    );

?>


<button
    type="button"
    class="seat <?php
        echo $is_booked
            ? 'booked'
            : '';
    ?>"
    data-seat="<?php
        echo htmlspecialchars(
            $seat
        );
    ?>"
    <?php
    if ($is_booked) {
        echo "disabled";
    }
    ?>
>

<?php
echo htmlspecialchars(
    $seat
);
?>

</button>


<?php endforeach; ?>


</div>

</div>


<!-- SELECTED -->

<div class="selected-box">

<div class="selected-label">

SELECTED SEATS

</div>


<div
    class="selected-text"
    id="selectedDisplay"
>
No seats selected
</div>

</div>


<!-- CONTINUE -->

<button
    type="submit"
    class="continue-btn"
    id="continueButton"
    disabled
>

CONTINUE TO PAYMENT

</button>


</form>


</div>


</main>


<footer class="footer">

© <?php echo date('Y'); ?>
CineBook. All rights reserved.

</footer>


<script>

/* ==================================================
   SEAT SELECTION
================================================== */

const seats =
    document.querySelectorAll(
        ".seat:not(.booked)"
    );


const selectedInput =
    document.getElementById(
        "selected_seats"
    );


const selectedDisplay =
    document.getElementById(
        "selectedDisplay"
    );


const continueButton =
    document.getElementById(
        "continueButton"
    );


let selectedSeats = [];


seats.forEach(
    function(button) {

        button.addEventListener(
            "click",
            function() {

                const seat =
                    this.dataset.seat;


                if (
                    selectedSeats.includes(
                        seat
                    )
                ) {

                    selectedSeats =
                        selectedSeats.filter(
                            function(item) {

                                return item !== seat;

                            }
                        );


                    this.classList.remove(
                        "selected"
                    );

                } else {

                    selectedSeats.push(
                        seat
                    );


                    this.classList.add(
                        "selected"
                    );

                }


                updateSelection();

            }
        );

    }
);


/* ==================================================
   UPDATE SELECTION
================================================== */

function updateSelection() {

    selectedInput.value =
        selectedSeats.join(",");


    if (
        selectedSeats.length === 0
    ) {

        selectedDisplay.textContent =
            "No seats selected";

        continueButton.disabled =
            true;

    } else {

        selectedDisplay.textContent =
            selectedSeats.join(", ");

        continueButton.disabled =
            false;

    }

}


/* ==================================================
   FORM VALIDATION
================================================== */

document
    .getElementById("seatForm")
    .addEventListener(
        "submit",
        function(event) {

            if (
                selectedSeats.length === 0
            ) {

                event.preventDefault();

                alert(
                    "Please select at least one seat."
                );

            }

        }
    );

</script>


</body>

</html>