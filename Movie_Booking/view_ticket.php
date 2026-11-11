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


$user_id = (int)$_SESSION['user_id'];


// ==================================================
// GET BOOKING ID
// ==================================================

$booking_id = isset($_GET['id'])
    ? (int)$_GET['id']
    : 0;


if ($booking_id <= 0) {
    header("Location: mybooking.php");
    exit();
}


// ==================================================
// GET BOOKING
// ==================================================

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        b.id,
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
     WHERE b.id = ?
     AND b.user_id = ?
     LIMIT 1"
);


if (!$stmt) {
    die(
        "Database error: "
        . mysqli_error($conn)
    );
}


mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $booking_id,
    $user_id
);


mysqli_stmt_execute($stmt);


$result =
    mysqli_stmt_get_result($stmt);


$booking =
    mysqli_fetch_assoc($result);


mysqli_stmt_close($stmt);


if (!$booking) {

    die("
        <div style='
            text-align:center;
            padding:80px;
            font-family:Arial;
        '>
            <h2>Booking not found</h2>

            <p>
                This booking does not exist
                or does not belong to you.
            </p>

            <a href='mybooking.php'>
                Back to My Bookings
            </a>
        </div>
    ");

}


// ==================================================
// PROCESS SEATS
// ==================================================

$seat_string = trim(
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
    CineBook | View Ticket
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


/* ==================================================
   HEADER
================================================== */

.header {

    height: 70px;

    background: #ffffff;

    border-bottom:
        1px solid #eeeeee;

    display: flex;

    align-items: center;

}


.header-inner {

    width: 92%;

    max-width: 1100px;

    margin: auto;

    display: flex;

    align-items: center;

    justify-content:
        space-between;

}


.logo {

    text-decoration: none;

    color: #111111;

    font-size: 24px;

    font-weight: 900;

}


.logo span {

    color: #e50914;

}


.nav {

    display: flex;

    gap: 22px;

}


.nav a {

    text-decoration: none;

    color: #555555;

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

    width: 92%;

    max-width: 700px;

    margin: auto;

    padding:
        35px 0 70px;

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

    border-radius: 50%;

    border:
        1px solid #dddddd;

    background: #ffffff;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    text-decoration: none;

    color: #222222;

    font-size: 21px;

    font-weight: bold;

}


.back a:hover {

    background: #e50914;

    color: #ffffff;

}


/* ==================================================
   TICKET
================================================== */

.ticket {

    background: #ffffff;

    border-radius: 20px;

    overflow: hidden;

    box-shadow:
        0 20px 60px
        rgba(0,0,0,.10);

    border:
        1px solid #eeeeee;

}


/* ==================================================
   TICKET HEADER
================================================== */

.ticket-header {

    background:
        linear-gradient(
            135deg,
            #111111,
            #450608
        );

    color: #ffffff;

    padding: 28px;

}


.ticket-header small {

    color: #ff5962;

    font-size: 9px;

    font-weight: 900;

    letter-spacing: 3px;

}


.ticket-header h1 {

    font-size: 27px;

    margin-top: 8px;

}


.ticket-header p {

    color: #cccccc;

    font-size: 10px;

    margin-top: 8px;

}


.booking-id {

    margin-top: 15px;

    display: inline-block;

    background: #e50914;

    padding: 8px 13px;

    border-radius: 20px;

    font-size: 9px;

    font-weight: 900;

}


/* ==================================================
   DETAILS
================================================== */

.details {

    padding: 28px;

    display: grid;

    grid-template-columns:
        1fr 1fr;

    gap: 22px;

}


.detail {

    min-width: 0;

}


.detail label {

    display: block;

    color: #999999;

    font-size: 8px;

    font-weight: 900;

    letter-spacing: 1px;

    margin-bottom: 7px;

}


.detail strong {

    display: block;

    color: #222222;

    font-size: 11px;

    word-break: break-word;

}


/* ==================================================
   SEATS
================================================== */

.seats-section {

    margin:
        0 28px 25px;

    padding: 18px;

    background: #fff5f5;

    border:
        1px solid #ffd4d4;

    border-radius: 12px;

}


.seats-section label {

    display: block;

    color: #999999;

    font-size: 8px;

    font-weight: 900;

    letter-spacing: 1px;

    margin-bottom: 12px;

}


.seats {

    display: flex;

    flex-wrap: wrap;

    gap: 8px;

}


.seat {

    background: #e50914;

    color: #ffffff;

    padding:
        9px 13px;

    border-radius: 6px;

    font-size: 10px;

    font-weight: 900;

}


.no-seats {

    color: #e50914;

    font-size: 10px;

    font-weight: bold;

}


/* ==================================================
   TOTAL
================================================== */

.total {

    border-top:
        1px solid #eeeeee;

    padding:
        20px 28px;

    display: flex;

    justify-content:
        space-between;

    align-items: center;

}


.total span {

    color: #888888;

    font-size: 11px;

    font-weight: 700;

}


.total strong {

    color: #e50914;

    font-size: 23px;

    font-weight: 900;

}


/* ==================================================
   FOOTER AREA
================================================== */

.ticket-footer {

    border-top:
        1px dashed #dddddd;

    padding: 20px 28px;

    display: flex;

    justify-content:
        space-between;

    align-items: center;

}


.ticket-footer span {

    color: #999999;

    font-size: 9px;

}


.print-button {

    border: none;

    background: #e50914;

    color: #ffffff;

    padding:
        10px 18px;

    border-radius: 7px;

    font-size: 9px;

    font-weight: 900;

    cursor: pointer;

}


.print-button:hover {

    background: #b70710;

}


/* ==================================================
   RESPONSIVE
================================================== */

@media(max-width:650px) {

    .nav {

        display: none;

    }


    .details {

        grid-template-columns: 1fr;

    }


    .ticket-header {

        padding: 23px;

    }


    .details {

        padding: 23px;

    }


    .seats-section {

        margin:
            0 23px 23px;

    }


    .total {

        padding:
            18px 23px;

    }


    .ticket-footer {

        padding:
            18px 23px;

        flex-direction: column;

        gap: 12px;

        align-items: stretch;

    }


    .print-button {

        width: 100%;

    }

}


/* ==================================================
   PRINT
================================================== */

@media print {

    body {

        background: white;

    }


    .header,
    .back,
    .print-button {

        display: none;

    }


    .main {

        width: 100%;

        max-width: none;

        padding: 0;

    }


    .ticket {

        box-shadow: none;

        border: 1px solid #ddd;

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

<a href="mybooking.php">
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


<!-- BACK -->

<div class="back">

<a
    href="mybooking.php"
    title="Back to My Bookings"
>
    ←
</a>

</div>


<!-- ==================================================
     TICKET
================================================== -->

<div class="ticket">


<!-- TICKET HEADER -->

<div class="ticket-header">


<small>
    CINEBOOK MOVIE TICKET
</small>


<h1>

<?php

echo htmlspecialchars(
    $booking['title']
    ?? 'Movie'
);

?>

</h1>


<p>

<?php

echo htmlspecialchars(
    $booking['movie_language']
    ?? ''
);

?>

<?php if (
    !empty($booking['genre'])
): ?>

&nbsp; • &nbsp;

<?php

echo htmlspecialchars(
    $booking['genre']
);

?>

<?php endif; ?>

</p>


<div class="booking-id">

BOOKING #

<?php

echo (int)$booking['id'];

?>

</div>


</div>


<!-- ==================================================
     DETAILS
================================================== -->

<div class="details">


<!-- CUSTOMER NAME -->

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


<!-- ==================================================
     BOOKED SEATS
================================================== -->

<div class="seats-section">


<label>
BOOKED SEATS
</label>


<div class="seats">


<?php if (
    $seat_count > 0
): ?>


<?php foreach (
    $seat_array
    as $seat
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


<!-- ==================================================
     TOTAL PRICE
================================================== -->

<div class="total">

<span>
TOTAL PRICE
</span>

<strong>

₹<?php

echo number_format(
    (float)$booking['total_price'],
    2
);

?>

</strong>

</div>


<!-- ==================================================
     FOOTER
================================================== -->

<div class="ticket-footer">

<span>
Thank you for booking with CineBook.
</span>


<button
    type="button"
    class="print-button"
    onclick="window.print()"
>
    PRINT TICKET
</button>


</div>


</div>


</main>


</body>

</html>