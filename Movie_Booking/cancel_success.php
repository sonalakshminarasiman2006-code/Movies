<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$booking_id = isset($_GET['booking_id'])
    ? (int)$_GET['booking_id']
    : 0;

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
    CineBook | Booking Cancelled
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
    display: flex;
    flex-direction: column;
}

.header {
    height: 70px;
    background: #ffffff;
    border-bottom: 1px solid #eeeeee;
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

.main {
    width: 92%;
    max-width: 600px;
    margin: auto;
    padding: 40px 0;
}

.card {
    background: #ffffff;
    border-radius: 20px;
    padding: 45px 30px;
    text-align: center;
    box-shadow: 0 18px 50px rgba(0,0,0,.08);
}

.back {
    text-align: left;
    margin-bottom: 25px;
}

.back a {
    width: 40px;
    height: 40px;
    border: 1px solid #dddddd;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    color: #222222;
    font-size: 21px;
}

.back a:hover {
    background: #e50914;
    color: white;
}

.icon {
    width: 75px;
    height: 75px;
    margin: auto;
    border-radius: 50%;
    background: #e50914;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    font-weight: bold;
}

.small {
    color: #e50914;
    font-size: 9px;
    font-weight: 900;
    letter-spacing: 3px;
    margin-top: 20px;
}

h1 {
    font-size: 30px;
    margin-top: 8px;
}

.message {
    color: #888888;
    font-size: 11px;
    line-height: 1.7;
    margin-top: 10px;
}

.booking-id {
    margin-top: 20px;
    background: #fff5f5;
    border: 1px solid #ffd5d5;
    border-radius: 10px;
    padding: 14px;
    color: #555555;
    font-size: 11px;
}

.booking-id strong {
    color: #e50914;
}

.buttons {
    display: flex;
    gap: 10px;
    margin-top: 25px;
}

.btn {
    flex: 1;
    padding: 14px;
    border-radius: 8px;
    text-decoration: none;
    font-size: 10px;
    font-weight: 900;
}

.primary {
    background: #e50914;
    color: white;
}

.secondary {
    border: 1px solid #dddddd;
    color: #333333;
}

@media(max-width:600px) {

    .nav {
        display: none;
    }

    .card {
        padding: 35px 20px;
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
    href="mybooking.php"
    title="Back"
>
    ←
</a>

</div>


<div class="icon">
    ✓
</div>


<div class="small">
    CINEBOOK
</div>


<h1>
    Booking Cancelled
</h1>


<p class="message">

Your movie booking has been
successfully cancelled.

</p>


<?php if ($booking_id > 0): ?>

<div class="booking-id">

Booking ID:
<strong>
#<?php echo $booking_id; ?>
</strong>

</div>

<?php endif; ?>


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
    BOOK ANOTHER TICKET
</a>

</div>


</div>

</main>

</body>

</html>