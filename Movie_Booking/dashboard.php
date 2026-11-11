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
// GET USER DETAILS
// ==================================================

$user_id = (int)$_SESSION['user_id'];

$user_name = "User";
$user_email = "";


$stmt = mysqli_prepare(
    $conn,
    "SELECT name, email
     FROM users
     WHERE id = ?
     LIMIT 1"
);

if ($stmt) {

    mysqli_stmt_bind_param(
        $stmt,
        "i",
        $user_id
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $user = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    if ($user) {

        $user_name =
            !empty($user['name'])
            ? $user['name']
            : "User";

        $user_email =
            $user['email'] ?? "";
    }
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

<title>CineBook | Dashboard</title>


<style>

/* ==================================================
   RESET
================================================== */

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}


body {

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    background:
        linear-gradient(
            135deg,
            #fff5f6,
            #ffffff 45%,
            #fff0f2
        );

    color: #181818;

    min-height: 100vh;
}


/* ==================================================
   HEADER
================================================== */

.header {

    height: 74px;

    background:
        rgba(255,255,255,.96);

    border-bottom:
        1px solid #eeeeee;

    display: flex;

    align-items: center;

    position: sticky;

    top: 0;

    z-index: 100;

    backdrop-filter: blur(10px);
}


.header-inner {

    width: 92%;

    max-width: 1250px;

    margin: auto;

    display: flex;

    align-items: center;

    justify-content: space-between;
}


/* ==================================================
   LOGO
================================================== */

.logo {

    text-decoration: none;

    color: #111;

    font-size: 26px;

    font-weight: 900;

    letter-spacing: -1px;
}


.logo span {

    color: #e50914;
}


/* ==================================================
   NAVIGATION
================================================== */

.nav {

    display: flex;

    align-items: center;

    gap: 25px;
}


.nav a {

    text-decoration: none;

    color: #555;

    font-size: 11px;

    font-weight: 800;

    transition: .2s;
}


.nav a:hover {

    color: #e50914;
}


/* USER PROFILE TOP RIGHT */

.user-mini {

    display: flex;

    align-items: center;

    gap: 9px;

    padding-left: 12px;

    border-left:
        1px solid #eeeeee;
}


.user-circle {

    width: 35px;

    height: 35px;

    border-radius: 50%;

    background:
        linear-gradient(
            135deg,
            #e50914,
            #ff5a64
        );

    color: white;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 12px;

    font-weight: 900;
}


.user-mini-name {

    font-size: 10px;

    font-weight: 800;

    color: #333;

    max-width: 90px;

    overflow: hidden;

    text-overflow: ellipsis;

    white-space: nowrap;
}


/* ==================================================
   MAIN
================================================== */

.main {

    width: 92%;

    max-width: 1250px;

    margin: auto;

    padding:
        45px 0 70px;
}


/* ==================================================
   HERO
================================================== */

.hero {

    position: relative;

    overflow: hidden;

    border-radius: 28px;

    padding: 55px;

    color: white;

    background:
        linear-gradient(
            120deg,
            #111111,
            #2b0306,
            #e50914
        );

    box-shadow:
        0 25px 70px
        rgba(
            229,
            9,
            20,
            .18
        );
}


.hero::before {

    content: "";

    position: absolute;

    width: 300px;

    height: 300px;

    border-radius: 50%;

    background:
        rgba(
            255,
            255,
            255,
            .07
        );

    right: -80px;

    top: -120px;
}


.hero::after {

    content: "";

    position: absolute;

    width: 180px;

    height: 180px;

    border-radius: 50%;

    background:
        rgba(
            255,
            255,
            255,
            .05
        );

    right: 180px;

    bottom: -100px;
}


.hero-content {

    position: relative;

    z-index: 2;

    max-width: 650px;
}


.hero-small {

    font-size: 9px;

    font-weight: 900;

    letter-spacing: 4px;

    color: #ffb7bd;

    margin-bottom: 12px;
}


.hero h1 {

    font-size: 42px;

    line-height: 1.1;

    margin-bottom: 15px;
}


.hero h1 span {

    color: #ff6872;
}


.hero p {

    color: #e5e5e5;

    font-size: 12px;

    line-height: 1.8;

    max-width: 560px;
}


/* ==================================================
   HERO BUTTONS
================================================== */

.hero-buttons {

    display: flex;

    gap: 12px;

    margin-top: 28px;

    flex-wrap: wrap;
}


.hero-btn {

    text-decoration: none;

    padding:
        13px 22px;

    border-radius: 9px;

    font-size: 10px;

    font-weight: 900;

    transition: .2s;
}


.hero-primary {

    background: white;

    color: #e50914;
}


.hero-secondary {

    background:
        rgba(
            255,
            255,
            255,
            .12
        );

    color: white;

    border:
        1px solid
        rgba(
            255,
            255,
            255,
            .25
        );
}


.hero-btn:hover {

    transform:
        translateY(-2px);
}


/* ==================================================
   WELCOME
================================================== */

.welcome {

    margin-top: 35px;

    margin-bottom: 20px;
}


.welcome small {

    color: #e50914;

    font-size: 9px;

    font-weight: 900;

    letter-spacing: 3px;
}


.welcome h2 {

    font-size: 25px;

    margin-top: 7px;
}


.welcome p {

    color: #888;

    font-size: 10px;

    margin-top: 5px;
}


/* ==================================================
   FEATURE CARDS
================================================== */

.features {

    display: grid;

    grid-template-columns:
        repeat(3, 1fr);

    gap: 18px;
}


.feature {

    background: white;

    border:
        1px solid #eeeeee;

    border-radius: 18px;

    padding: 25px;

    text-decoration: none;

    color: #222;

    box-shadow:
        0 12px 35px
        rgba(
            0,
            0,
            0,
            .05
        );

    transition:
        transform .2s,
        box-shadow .2s;
}


.feature:hover {

    transform:
        translateY(-5px);

    box-shadow:
        0 20px 45px
        rgba(
            0,
            0,
            0,
            .09
        );
}


.feature-icon {

    width: 48px;

    height: 48px;

    border-radius: 13px;

    display: flex;

    align-items: center;

    justify-content: center;

    background: #fff0f1;

    color: #e50914;

    font-size: 20px;

    margin-bottom: 18px;
}


.feature h3 {

    font-size: 14px;

    margin-bottom: 7px;
}


.feature p {

    color: #999;

    font-size: 9px;

    line-height: 1.6;
}


.feature-arrow {

    margin-top: 18px;

    color: #e50914;

    font-size: 10px;

    font-weight: 900;
}


/* ==================================================
   QUICK ACTIONS
================================================== */

.section-title {

    margin-top: 38px;

    margin-bottom: 18px;
}


.section-title small {

    color: #e50914;

    font-size: 8px;

    font-weight: 900;

    letter-spacing: 3px;
}


.section-title h2 {

    font-size: 21px;

    margin-top: 6px;
}


.actions {

    display: grid;

    grid-template-columns:
        repeat(4, 1fr);

    gap: 14px;
}


.action {

    background: white;

    border:
        1px solid #eeeeee;

    border-radius: 14px;

    padding: 20px;

    text-decoration: none;

    color: #222;

    transition: .2s;
}


.action:hover {

    border-color:
        #ffc4c8;

    background:
        #fffafb;

    transform:
        translateY(-3px);
}


.action-icon {

    font-size: 20px;

    margin-bottom: 12px;
}


.action h4 {

    font-size: 11px;

    margin-bottom: 5px;
}


.action p {

    color: #999;

    font-size: 8px;
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

@media(max-width:850px) {

    .nav {
        display: none;
    }

    .features {
        grid-template-columns: 1fr;
    }

    .actions {
        grid-template-columns:
            repeat(2, 1fr);
    }

    .hero {
        padding: 38px 28px;
    }

    .hero h1 {
        font-size: 32px;
    }

}


@media(max-width:500px) {

    .main {
        width: 94%;
    }

    .hero {
        padding: 30px 22px;
        border-radius: 20px;
    }

    .hero h1 {
        font-size: 28px;
    }

    .actions {
        grid-template-columns: 1fr;
    }

    .user-mini-name {
        display: none;
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


<div class="user-mini">

<div class="user-circle">

<?php

echo strtoupper(
    substr(
        $user_name,
        0,
        1
    )
);

?>

</div>


<div class="user-mini-name">

<?php

echo htmlspecialchars(
    $user_name
);

?>

</div>

</div>


</nav>


</div>

</header>



<!-- ==================================================
     MAIN
================================================== -->

<main class="main">


<!-- HERO -->

<section class="hero">


<div class="hero-content">


<div class="hero-small">

WELCOME TO CINEBOOK

</div>


<h1>

Hello,

<span>
<?php

echo htmlspecialchars(
    $user_name
);

?>

</span>

🍿

</h1>


<p>

Book your favourite movies,
choose your preferred seats,
and enjoy a simple and secure
cinema booking experience.

</p>


<div class="hero-buttons">


<a
    href="movies.php"
    class="hero-btn hero-primary"
>

🎬 BOOK A MOVIE

</a>


<a
    href="bookings.php"
    class="hero-btn hero-secondary"
>

🎟 MY BOOKINGS

</a>


</div>


</div>

</section>



<!-- WELCOME -->

<div class="welcome">

<small>
YOUR CINEBOOK
</small>


<h2>
Everything you need in one place
</h2>


<p>
Choose a movie, book your seats and manage your tickets easily.
</p>

</div>



<!-- FEATURES -->

<div class="features">


<a
    href="movies.php"
    class="feature"
>

<div class="feature-icon">
🎬
</div>


<h3>
Browse Movies
</h3>


<p>
Explore available movies and
choose the movie you want to watch.
</p>


<div class="feature-arrow">
EXPLORE MOVIES →
</div>

</a>



<a
    href="bookings.php"
    class="feature"
>

<div class="feature-icon">
🎟️
</div>


<h3>
My Bookings
</h3>


<p>
View your booked tickets,
seat numbers and booking details.
</p>


<div class="feature-arrow">
VIEW BOOKINGS →
</div>

</a>



<a
    href="profile.php"
    class="feature"
>

<div class="feature-icon">
👤
</div>


<h3>
My Profile
</h3>


<p>
View and edit your personal
customer profile information.
</p>


<div class="feature-arrow">
EDIT PROFILE →
</div>

</a>


</div>



<!-- QUICK ACTIONS -->

<div class="section-title">

<small>
QUICK ACCESS
</small>


<h2>
Manage your account
</h2>

</div>


<div class="actions">


<a
    href="movies.php"
    class="action"
>

<div class="action-icon">
🍿
</div>


<h4>
Book Movie
</h4>


<p>
Find your next movie
</p>

</a>



<a
    href="bookings.php"
    class="action"
>

<div class="action-icon">
🎫
</div>


<h4>
View Tickets
</h4>


<p>
See your booking history
</p>

</a>



<a
    href="profile.php"
    class="action"
>

<div class="action-icon">
⚙️
</div>


<h4>
Profile Settings
</h4>


<p>
Manage your account
</p>

</a>



<a
    href="logout.php"
    class="action"
>

<div class="action-icon">
🚪
</div>


<h4>
Logout
</h4>


<p>
Sign out securely
</p>

</a>


</div>


</main>



<!-- FOOTER -->

<footer class="footer">

© <?php echo date('Y'); ?>
CineBook. All rights reserved.

</footer>


</body>

</html>