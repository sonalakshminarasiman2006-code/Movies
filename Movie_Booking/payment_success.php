<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once "database.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_SESSION['user_id'];


// ==================================================
// RECEIVE DATA FROM PAYMENT.PHP
// ==================================================

$movie_id = (int)($_POST['movie_id'] ?? 0);

$booking_date = trim(
    $_POST['booking_date'] ?? ''
);

$show_time = trim(
    $_POST['show_time'] ?? ''
);

$selected_seats = trim(
    $_POST['selected_seats'] ?? ''
);


// ==================================================
// VALIDATION
// ==================================================

if ($movie_id <= 0) {
    die("Movie was not received.");
}

if ($booking_date === '') {
    die("Show date was not received.");
}

if ($show_time === '') {
    die("Show time was not received.");
}

if ($selected_seats === '') {
    die("Selected seats were not received.");
}


// ==================================================
// CLEAN SELECTED SEATS
// ==================================================

$seat_array = array_filter(
    array_map(
        'trim',
        explode(',', $selected_seats)
    )
);

$seat_array = array_values(
    array_unique($seat_array)
);

if (count($seat_array) == 0) {
    die("No valid seats selected.");
}


// IMPORTANT:
// This exact string will be saved into bookings.seats

$selected_seats = implode(
    ',',
    $seat_array
);

$seat_count = count($seat_array);


// ==================================================
// GET USER
// ==================================================

$user_query = mysqli_prepare(
    $conn,
    "SELECT name, email
     FROM users
     WHERE id = ?
     LIMIT 1"
);

if (!$user_query) {
    die(mysqli_error($conn));
}

mysqli_stmt_bind_param(
    $user_query,
    "i",
    $user_id
);

mysqli_stmt_execute($user_query);

$user_result =
    mysqli_stmt_get_result($user_query);

$user = mysqli_fetch_assoc(
    $user_result
);

mysqli_stmt_close($user_query);


if (!$user) {
    die("User information not found.");
}


$name = $user['name'];
$email = $user['email'];


// ==================================================
// GET MOVIE
// ==================================================

$movie_query = mysqli_prepare(
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

if (!$movie_query) {
    die(mysqli_error($conn));
}

mysqli_stmt_bind_param(
    $movie_query,
    "i",
    $movie_id
);

mysqli_stmt_execute($movie_query);

$movie_result =
    mysqli_stmt_get_result($movie_query);

$movie = mysqli_fetch_assoc(
    $movie_result
);

mysqli_stmt_close($movie_query);


if (!$movie) {
    die("Movie not found.");
}


// ==================================================
// CALCULATE TOTAL
// ==================================================

$price_per_seat =
    (float)$movie['price'];

$total_price =
    $seat_count *
    $price_per_seat;


// ==================================================
// INSERT BOOKING
// ==================================================

$insert = mysqli_prepare(
    $conn,
    "INSERT INTO bookings
    (
        user_id,
        movie_id,
        name,
        email,
        show_date,
        show_time,
        seats,
        total_price,
        created_at
    )
    VALUES
    (?, ?, ?, ?, ?, ?, ?, ?, NOW())"
);

if (!$insert) {
    die(
        "Booking insert error: "
        . mysqli_error($conn)
    );
}


mysqli_stmt_bind_param(
    $insert,
    "iisssssd",
    $user_id,
    $movie_id,
    $name,
    $email,
    $booking_date,
    $show_time,
    $selected_seats,
    $total_price
);


if (!mysqli_stmt_execute($insert)) {
    die(
        "Booking could not be saved: "
        . mysqli_stmt_error($insert)
    );
}


$booking_id =
    mysqli_insert_id($conn);

mysqli_stmt_close($insert);


// ==================================================
// VERIFY THE SAVED BOOKING
// ==================================================

$verify = mysqli_prepare(
    $conn,
    "SELECT
        id,
        seats,
        total_price
     FROM bookings
     WHERE id = ?
     AND user_id = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $verify,
    "ii",
    $booking_id,
    $user_id
);

mysqli_stmt_execute($verify);

$verify_result =
    mysqli_stmt_get_result($verify);

$saved_booking =
    mysqli_fetch_assoc($verify_result);

mysqli_stmt_close($verify);


if (!$saved_booking) {
    die("Booking verification failed.");
}


// If database did not save seats, STOP here.
// This prevents a fake successful booking.

$saved_seats = trim(
    (string)$saved_booking['seats']
);

if ($saved_seats === '') {
    die(
        "Booking was created, but selected seats were not saved in the database."
    );
}


// ==================================================
// DISPLAY SAVED SEATS
// ==================================================

$saved_seat_array = array_filter(
    array_map(
        'trim',
        explode(',', $saved_seats)
    )
);

$saved_seat_array = array_values(
    array_unique($saved_seat_array)
);

$saved_seat_count =
    count($saved_seat_array);

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
    CineBook | Payment Successful
</title>

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    min-height: 100vh;
    font-family: Arial, sans-serif;
    background: #f7f7f7;
    color: #222;
}

.header {
    height: 70px;
    background: #fff;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: center;
}

.header-inner {
    width: 92%;
    max-width: 1100px;
    margin: auto;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.logo {
    text-decoration: none;
    color: #111;
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
    color: #555;
    font-size: 11px;
    font-weight: 700;
}

.main {
    width: 92%;
    max-width: 650px;
    margin: auto;
    padding: 45px 0 70px;
}

.card {
    background: #fff;
    padding: 35px;
    border-radius: 18px;
    box-shadow: 0 15px 45px rgba(0,0,0,.08);
}

.back {
    margin-bottom: 20px;
}

.back a {
    width: 38px;
    height: 38px;
    border: 1px solid #ddd;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    color: #222;
    font-size: 20px;
}

.back a:hover {
    background: #e50914;
    color: white;
}

.success-icon {
    width: 72px;
    height: 72px;
    margin: auto;
    border-radius: 50%;
    background: #e50914;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 30px;
    font-weight: bold;
}

.title {
    text-align: center;
}

.small-title {
    color: #e50914;
    font-size: 9px;
    font-weight: 900;
    letter-spacing: 3px;
    margin-top: 18px;
}

.title h1 {
    font-size: 30px;
    margin-top: 8px;
}

.title p {
    color: #888;
    font-size: 11px;
    margin-top: 8px;
}

.ticket {
    margin-top: 25px;
    border: 1px solid #eee;
    border-radius: 12px;
    overflow: hidden;
}

.ticket-header {
    background: #111;
    color: white;
    padding: 21px;
}

.ticket-header h2 {
    font-size: 21px;
}

.ticket-header p {
    color: #ccc;
    font-size: 10px;
    margin-top: 6px;
}

.details {
    padding: 22px;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

.detail label {
    display: block;
    color: #999;
    font-size: 8px;
    font-weight: 800;
    margin-bottom: 6px;
}

.detail strong {
    font-size: 11px;
}

.seats {
    color: #e50914;
}

.total {
    border-top: 1px solid #eee;
    padding: 18px 22px;
    display: flex;
    justify-content: space-between;
}

.total strong {
    color: #e50914;
    font-size: 20px;
}

.buttons {
    display: flex;
    gap: 10px;
    margin-top: 22px;
}

.btn {
    flex: 1;
    text-align: center;
    padding: 13px;
    border-radius: 7px;
    text-decoration: none;
    font-size: 10px;
    font-weight: 900;
}

.primary {
    background: #e50914;
    color: white;
}

.secondary {
    border: 1px solid #ddd;
    color: #333;
}

@media(max-width:650px) {

    .nav {
        display: none;
    }

    .card {
        padding: 22px 16px;
    }

    .details {
        grid-template-columns: 1fr;
    }

    .buttons {
        flex-direction: column;
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


<main class="main">

<div class="card">


<div class="back">

<a
    href="javascript:history.back()"
    title="Back"
>
    ←
</a>

</div>


<div class="success-icon">
    ✓
</div>


<div class="title">

<div class="small-title">
    BOOKING CONFIRMED
</div>

<h1>
    Payment Successful
</h1>

<p>
    Your movie ticket has been booked successfully.
</p>

</div>


<div class="ticket">


<div class="ticket-header">

<h2>

<?php

echo htmlspecialchars(
    $movie['title']
);

?>

</h2>

<p>

Booking ID:
#<?php echo $booking_id; ?>

</p>

</div>


<div class="details">


<div class="detail">

<label>
SHOW DATE
</label>

<strong>

<?php

echo htmlspecialchars(
    $booking_date
);

?>

</strong>

</div>


<div class="detail">

<label>
SHOW TIME
</label>

<strong>

<?php

echo htmlspecialchars(
    $show_time
);

?>

</strong>

</div>


<div class="detail">

<label>
BOOKED SEATS
</label>

<strong class="seats">

<?php

echo htmlspecialchars(
    implode(
        ', ',
        $saved_seat_array
    )
);

?>

</strong>

</div>


<div class="detail">

<label>
NUMBER OF SEATS
</label>

<strong>

<?php

echo $saved_seat_count;

?>

</strong>

</div>


<div class="detail">

<label>
PAYMENT
</label>

<strong>
Paid
</strong>

</div>


</div>


<div class="total">

<span>
Total Amount
</span>

<strong>

₹<?php

echo number_format(
    $total_price,
    2
);

?>

</strong>

</div>


</div>


<div class="buttons">

<a
    href="mybooking.php"
    class="btn primary"
>
    MY BOOKINGS
</a>

<a
    href="movies.php"
    class="btn secondary"
>
    MOVIES
</a>

</div>


</div>

</main>

</body>

</html>