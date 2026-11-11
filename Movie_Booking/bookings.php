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

$user_id = (int) $_SESSION['user_id'];


// ==================================================
// GET BOOKINGS
// ==================================================

$sql = "
    SELECT
        b.id,
        b.user_id,
        b.movie_id,
        b.name,
        b.email,
        b.show_date,
        b.show_time,
        b.seats,
        b.total_price,
        b.created_at,
        m.title,
        m.movie_language,
        m.genre
    FROM bookings b
    LEFT JOIN movies m
        ON b.movie_id = m.id
    WHERE b.user_id = ?
    ORDER BY b.id DESC
";


$stmt = mysqli_prepare($conn, $sql);

if (!$stmt) {
    die(
        "Database Error: "
        . mysqli_error($conn)
    );
}


mysqli_stmt_bind_param(
    $stmt,
    "i",
    $user_id
);


mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
    CineBook | My Bookings
</title>

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}


body {

    min-height: 100vh;

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    background:
        linear-gradient(
            135deg,
            #fff5f5,
            #ffffff
        );

    color: #222;

}


/* ================= HEADER ================= */

.header {

    height: 72px;

    background: #fff;

    border-bottom:
        1px solid #eee;

    display: flex;

    align-items: center;

}


.header-inner {

    width: 92%;

    max-width: 1200px;

    margin: auto;

    display: flex;

    align-items: center;

    justify-content:
        space-between;

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


/* ================= MAIN ================= */

.main {

    width: 92%;

    max-width: 1100px;

    margin: auto;

    padding:
        35px 0 70px;

}


/* ================= BACK ================= */

.back {

    margin-bottom: 20px;

}


.back a {

    width: 40px;

    height: 40px;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    border-radius: 50%;

    background: #fff;

    border:
        1px solid #ddd;

    text-decoration: none;

    color: #222;

    font-size: 22px;

}


.back a:hover {

    background: #e50914;

    color: #fff;

}


/* ================= TITLE ================= */

.page-title {

    margin-bottom: 30px;

}


.page-title small {

    color: #e50914;

    font-size: 9px;

    font-weight: 900;

    letter-spacing: 3px;

}


.page-title h1 {

    margin-top: 8px;

    font-size: 36px;

}


.page-title p {

    margin-top: 7px;

    color: #888;

    font-size: 11px;

}


/* ================= BOOKING CARD ================= */

.booking-card {

    background: #fff;

    border:
        1px solid #eee;

    border-radius: 18px;

    overflow: hidden;

    margin-bottom: 25px;

    box-shadow:
        0 15px 45px
        rgba(0,0,0,.06);

}


/* ================= CARD HEADER ================= */

.booking-header {

    background:
        linear-gradient(
            135deg,
            #111,
            #450608
        );

    color: #fff;

    padding: 22px 25px;

    display: flex;

    align-items: center;

    justify-content:
        space-between;

    gap: 15px;

}


.movie-title {

    font-size: 21px;

    font-weight: 900;

}


.movie-info {

    color: #ccc;

    font-size: 9px;

    margin-top: 6px;

}


.booking-id {

    background: #e50914;

    color: #fff;

    padding:
        8px 12px;

    border-radius: 20px;

    font-size: 9px;

    font-weight: 900;

}


/* ================= BODY ================= */

.booking-body {

    padding: 25px;

}


.details {

    display: grid;

    grid-template-columns:
        1fr 1fr 1fr;

    gap: 20px;

}


.detail label {

    display: block;

    color: #999;

    font-size: 8px;

    font-weight: 900;

    letter-spacing: 1px;

    margin-bottom: 7px;

}


.detail strong {

    font-size: 11px;

    color: #222;

    word-break: break-word;

}


/* ================= SEATS ================= */

.seats-box {

    margin-top: 22px;

    background: #fff5f5;

    border:
        1px solid #ffd5d5;

    border-radius: 12px;

    padding: 18px;

}


.seats-box label {

    display: block;

    color: #999;

    font-size: 8px;

    font-weight: 900;

    letter-spacing: 1px;

    margin-bottom: 12px;

}


.seats-list {

    display: flex;

    flex-wrap: wrap;

    gap: 8px;

}


.seat {

    background: #e50914;

    color: #fff;

    padding:
        8px 12px;

    border-radius: 6px;

    font-size: 10px;

    font-weight: 900;

}


.no-seats {

    color: #e50914;

    font-size: 10px;

    font-weight: 700;

}


/* ================= BOTTOM ================= */

.booking-bottom {

    border-top:
        1px solid #eee;

    padding:
        18px 25px;

    display: flex;

    align-items: center;

    justify-content:
        space-between;

    gap: 20px;

}


.total {

    color: #888;

    font-size: 10px;

}


.total strong {

    display: block;

    color: #e50914;

    font-size: 21px;

    margin-top: 5px;

}


/* ================= BUTTONS ================= */

.buttons {

    display: flex;

    gap: 10px;

}


.btn {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    padding:
        12px 17px;

    border-radius: 7px;

    text-decoration: none;

    font-size: 9px;

    font-weight: 900;

}


.view-btn {

    background: #111;

    color: #fff;

}


.view-btn:hover {

    background: #333;

}


.cancel-btn {

    background: #e50914;

    color: #fff;

}


.cancel-btn:hover {

    background: #b70710;

}


/* ================= EMPTY ================= */

.empty {

    background: #fff;

    border:
        1px solid #eee;

    border-radius: 18px;

    padding: 60px 25px;

    text-align: center;

}


.empty h2 {

    font-size: 22px;

}


.empty p {

    color: #888;

    font-size: 11px;

    margin-top: 8px;

}


.empty a {

    display: inline-block;

    margin-top: 20px;

    padding:
        13px 20px;

    background: #e50914;

    color: #fff;

    text-decoration: none;

    border-radius: 7px;

    font-size: 10px;

    font-weight: 900;

}


/* ================= FOOTER ================= */

.footer {

    background: #111;

    color: #777;

    text-align: center;

    padding: 25px;

    font-size: 9px;

}


/* ================= MOBILE ================= */

@media(max-width:750px) {

    .nav {
        display: none;
    }

    .details {
        grid-template-columns:
            1fr 1fr;
    }

}


@media(max-width:550px) {

    .details {
        grid-template-columns:
            1fr;
    }

    .booking-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .booking-bottom {
        flex-direction: column;
        align-items: stretch;
    }

    .buttons {
        flex-direction: column;
    }

    .btn {
        width: 100%;
    }

}

</style>

</head>


<body>


<!-- ================= HEADER ================= -->

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


<!-- ================= MAIN ================= -->

<main class="main">


<div class="back">

<a
    href="dashboard.php"
    title="Back"
>
    ←
</a>

</div>


<div class="page-title">

<small>
    CINEBOOK
</small>

<h1>
    My Bookings
</h1>

<p>
    View your booked movie tickets and manage your bookings.
</p>

</div>


<?php if (mysqli_num_rows($result) > 0): ?>


<?php while ($booking = mysqli_fetch_assoc($result)): ?>


<?php

/* ================= SEATS ================= */

$seat_string =
    trim(
        (string)$booking['seats']
    );


$seat_array = [];


if ($seat_string !== '') {

    $seat_array = array_filter(
        array_map(
            'trim',
            explode(
                ',',
                $seat_string
            )
        )
    );

    $seat_array = array_values(
        array_unique(
            $seat_array
        )
    );
}


$seat_count =
    count($seat_array);


/* ================= MOVIE ================= */

$movie_title =
    !empty($booking['title'])
    ? $booking['title']
    : 'Movie';


$language =
    !empty($booking['movie_language'])
    ? $booking['movie_language']
    : '';


$genre =
    !empty($booking['genre'])
    ? $booking['genre']
    : '';

?>


<div class="booking-card">


<!-- ================= HEADER ================= -->

<div class="booking-header">

<div>

<div class="movie-title">

<?php

echo htmlspecialchars(
    $movie_title
);

?>

</div>


<div class="movie-info">

<?php

if ($language !== '') {

    echo htmlspecialchars(
        $language
    );

}


if (
    $language !== ''
    &&
    $genre !== ''
) {

    echo " • ";

}


if ($genre !== '') {

    echo htmlspecialchars(
        $genre
    );

}

?>

</div>

</div>


<div class="booking-id">

BOOKING #

<?php

echo (int)$booking['id'];

?>

</div>

</div>


<!-- ================= BODY ================= -->

<div class="booking-body">


<div class="details">


<!-- NAME -->

<div class="detail">

<label>
CUSTOMER NAME
</label>

<strong>

<?php

echo htmlspecialchars(
    $booking['name']
);

?>

</strong>

</div>


<!-- EMAIL -->

<div class="detail">

<label>
EMAIL
</label>

<strong>

<?php

echo htmlspecialchars(
    $booking['email']
);

?>

</strong>

</div>


<!-- SHOW DATE -->

<div class="detail">

<label>
SHOW DATE
</label>

<strong>

<?php

echo htmlspecialchars(
    $booking['show_date']
);

?>

</strong>

</div>


<!-- SHOW TIME -->

<div class="detail">

<label>
SHOW TIME
</label>

<strong>

<?php

echo htmlspecialchars(
    $booking['show_time']
);

?>

</strong>

</div>


<!-- NUMBER OF SEATS -->

<div class="detail">

<label>
NUMBER OF SEATS
</label>

<strong>

<?php

echo $seat_count;

?>

</strong>

</div>


<!-- BOOKED ON -->

<div class="detail">

<label>
BOOKED ON
</label>

<strong>

<?php

echo htmlspecialchars(
    $booking['created_at']
);

?>

</strong>

</div>


</div>


<!-- ================= BOOKED SEATS ================= -->

<div class="seats-box">

<label>
BOOKED SEATS
</label>


<div class="seats-list">


<?php if ($seat_count > 0): ?>


<?php foreach (
    $seat_array as $seat
): ?>

<span class="seat">

<?php

echo htmlspecialchars(
    $seat
);

?>

</span>

<?php endforeach; ?>


<?php else: ?>

<span class="no-seats">

No seats information available

</span>

<?php endif; ?>


</div>

</div>


</div>


<!-- ================= BOTTOM ================= -->

<div class="booking-bottom">


<div class="total">

TOTAL PRICE

<strong>

₹<?php

echo number_format(
    (float)$booking['total_price'],
    2
);

?>

</strong>

</div>


<div class="buttons">


<a
    href="view_ticket.php?id=<?php echo (int)$booking['id']; ?>"
    class="btn view-btn"
>
    🎟 VIEW TICKET
</a>


<a
    href="cancel_booking.php?id=<?php echo (int)$booking['id']; ?>"
    class="btn cancel-btn"
>
    ✕ CANCEL BOOKING
</a>


</div>


</div>


</div>


<?php endwhile; ?>


<?php else: ?>


<!-- ================= NO BOOKINGS ================= -->

<div class="empty">

<h2>
    No Bookings Yet
</h2>

<p>
    You have not booked any movies yet.
</p>

<a href="movies.php">
    BOOK A MOVIE
</a>

</div>


<?php endif; ?>


<?php

mysqli_stmt_close($stmt);

?>


</main>


<footer class="footer">

© <?php echo date('Y'); ?>
CineBook. All rights reserved.

</footer>


</body>

</html>