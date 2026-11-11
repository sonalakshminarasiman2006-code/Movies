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
// RECEIVE DATA FROM SEAT SELECTION
// ==================================================

$movie_id = isset($_POST['movie_id'])
    ? (int) $_POST['movie_id']
    : 0;

$booking_date = isset($_POST['booking_date'])
    ? trim($_POST['booking_date'])
    : '';

$show_time = isset($_POST['show_time'])
    ? trim($_POST['show_time'])
    : '';

$selected_seats = isset($_POST['selected_seats'])
    ? trim($_POST['selected_seats'])
    : '';


// ==================================================
// VALIDATION
// ==================================================

if ($movie_id <= 0) {

    die("
        <div class='error-page'>
            <h2>Movie was not received.</h2>
            <p>Please go back and select the movie again.</p>
            <a href='movies.php'>Back to Movies</a>
        </div>
    ");
}


if ($booking_date === '') {

    die("
        <div class='error-page'>
            <h2>Show date was not received.</h2>
            <p>Please go back to seat selection and select the show date again.</p>
            <a href='seat_selection.php?movie_id={$movie_id}'>
                Back to Seat Selection
            </a>
        </div>
    ");
}


if ($show_time === '') {

    die("
        <div class='error-page'>
            <h2>Show time was not received.</h2>
            <p>Please go back and select the show time again.</p>
            <a href='booking.php?movie_id={$movie_id}'>
                Back to Show Selection
            </a>
        </div>
    ");
}


if ($selected_seats === '') {

    die("
        <div class='error-page'>
            <h2>No seats selected.</h2>
            <p>Please select at least one seat.</p>
            <a href='javascript:history.back()'>
                Back to Seat Selection
            </a>
        </div>
    ");
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

    die("
        <div class='error-page'>
            <h2>Movie not found.</h2>
            <p>Please select a valid movie.</p>
            <a href='movies.php'>Back to Movies</a>
        </div>
    ");
}


// ==================================================
// PROCESS SEATS
// ==================================================

$seat_array = array_filter(
    array_map(
        'trim',
        explode(',', $selected_seats)
    )
);


// Remove duplicate seats

$seat_array = array_values(
    array_unique($seat_array)
);


$seat_count = count($seat_array);


if ($seat_count <= 0) {

    die("
        <div class='error-page'>
            <h2>No valid seats selected.</h2>
            <a href='javascript:history.back()'>
                Back
            </a>
        </div>
    ");
}


// ==================================================
// CALCULATE TOTAL
// ==================================================

$price_per_seat = (float) $movie['price'];

$total_amount =
    $seat_count * $price_per_seat;


// ==================================================
// DISPLAY SEATS
// ==================================================

$seat_display = implode(
    ', ',
    $seat_array
);

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
    CineBook | Payment
</title>


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

    max-width: 1050px;

    margin: auto;

    padding:
        45px 0 80px;

}


/* ==================================================
   PAYMENT TOP AREA
================================================== */

.payment-top {

    display: flex;

    align-items: center;

    gap: 12px;

    margin-bottom: 25px;

}


/* ==================================================
   SMALL BACK ARROW
================================================== */

.back-arrow {

    width: 34px;

    height: 34px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 50%;

    background: #ffffff;

    border: 1px solid #dddddd;

    color: #222222;

    text-decoration: none;

    font-size: 20px;

    line-height: 1;

    transition: 0.2s;

    flex-shrink: 0;

}


.back-arrow:hover {

    background: #e50914;

    border-color: #e50914;

    color: #ffffff;

}


/* ==================================================
   TITLE
================================================== */

.heading {

    margin-bottom: 0;

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

}


.heading p {

    margin-top: 7px;

    color: #888888;

    font-size: 11px;

}


/* ==================================================
   PAYMENT GRID
================================================== */

.payment-grid {

    display: grid;

    grid-template-columns:
        1.3fr .7fr;

    gap: 25px;

}


/* ==================================================
   CARD
================================================== */

.card {

    background: #ffffff;

    border:
        1px solid #eeeeee;

    border-radius: 16px;

    padding: 28px;

    box-shadow:
        0 15px 40px
        rgba(0,0,0,.05);

}


/* ==================================================
   MOVIE HEADER
================================================== */

.movie-header {

    background:
        linear-gradient(
            135deg,
            #111111,
            #420508
        );

    color: #ffffff;

    padding: 25px;

    border-radius: 12px;

    margin-bottom: 25px;

}


.movie-header h2 {

    font-size: 24px;

    margin-bottom: 9px;

}


.movie-header p {

    color: #dddddd;

    font-size: 10px;

    line-height: 1.8;

}


/* ==================================================
   SECTION TITLE
================================================== */

.section-title {

    font-size: 14px;

    font-weight: 900;

    margin-bottom: 18px;

}


/* ==================================================
   DETAILS
================================================== */

.detail {

    display: flex;

    justify-content:
        space-between;

    padding: 14px 0;

    border-bottom:
        1px solid #eeeeee;

}


.detail:last-child {

    border-bottom: none;

}


.detail-label {

    color: #888888;

    font-size: 10px;

}


.detail-value {

    color: #222222;

    font-size: 11px;

    font-weight: 800;

    text-align: right;

}


.seats-value {

    color: #e50914;

}


/* ==================================================
   PAYMENT METHODS
================================================== */

.payment-methods {

    margin-top: 25px;

}


.method {

    display: flex;

    align-items: center;

    gap: 12px;

    border:
        1px solid #dddddd;

    padding: 15px;

    border-radius: 9px;

    margin-bottom: 10px;

    cursor: pointer;

}


.method:hover {

    border-color:
        #e50914;

}


.method input {

    accent-color:
        #e50914;

}


.method-title {

    font-size: 11px;

    font-weight: 800;

}


.method-description {

    font-size: 9px;

    color: #999999;

    margin-top: 3px;

}


/* ==================================================
   SUMMARY
================================================== */

.summary-title {

    font-size: 16px;

    font-weight: 900;

    margin-bottom: 22px;

}


.summary-row {

    display: flex;

    justify-content:
        space-between;

    padding: 12px 0;

    font-size: 10px;

    color: #777777;

}


.summary-row strong {

    color: #222222;

}


.total-row {

    border-top:
        1px solid #eeeeee;

    margin-top: 10px;

    padding-top: 20px;

    display: flex;

    justify-content:
        space-between;

    align-items: center;

}


.total-label {

    font-size: 12px;

    font-weight: 900;

}


.total-price {

    color: #e50914;

    font-size: 24px;

    font-weight: 900;

}


/* ==================================================
   PAY BUTTON
================================================== */

.pay-button {

    width: 100%;

    margin-top: 25px;

    padding: 15px;

    border: none;

    border-radius: 8px;

    background: #e50914;

    color: #ffffff;

    font-size: 11px;

    font-weight: 900;

    cursor: pointer;

}


.pay-button:hover {

    background: #b70710;

}


/* ==================================================
   SECURE
================================================== */

.secure {

    text-align: center;

    margin-top: 15px;

    color: #999999;

    font-size: 8px;

}


/* ==================================================
   ERROR PAGE
================================================== */

.error-page {

    max-width: 600px;

    margin: 100px auto;

    padding: 40px;

    text-align: center;

    background: #ffffff;

    border-radius: 15px;

    box-shadow:
        0 15px 40px
        rgba(0,0,0,.08);

}


.error-page h2 {

    color: #e50914;

    margin-bottom: 12px;

}


.error-page p {

    color: #777777;

    font-size: 12px;

    margin-bottom: 20px;

}


.error-page a {

    display: inline-block;

    padding: 12px 20px;

    background: #e50914;

    color: #ffffff;

    text-decoration: none;

    border-radius: 7px;

    font-size: 10px;

    font-weight: 800;

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

@media (max-width: 750px) {

    .nav {

        display: none;

    }

    .payment-grid {

        grid-template-columns: 1fr;

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


    <!-- BACK ARROW + HEADING -->

    <div class="payment-top">


        <a
            href="seat_selection.php?movie_id=<?php echo $movie_id; ?>"
            class="back-arrow"
            title="Back to Seat Selection"
        >
            ←
        </a>


        <div class="heading">

            <small>
                STEP 3 OF 4
            </small>

            <h1>
                Payment
            </h1>

            <p>
                Review your booking details and complete payment.
            </p>

        </div>


    </div>


    <div class="payment-grid">


        <!-- ==================================================
             LEFT
        ================================================== -->

        <div class="card">


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

                </p>

            </div>


            <div class="section-title">

                Booking Details

            </div>


            <div class="detail">

                <span class="detail-label">
                    Show Date
                </span>

                <span class="detail-value">

                    <?php

                    echo htmlspecialchars(
                        $booking_date
                    );

                    ?>

                </span>

            </div>


            <div class="detail">

                <span class="detail-label">
                    Show Time
                </span>

                <span class="detail-value">

                    <?php

                    echo htmlspecialchars(
                        $show_time
                    );

                    ?>

                </span>

            </div>


            <div class="detail">

                <span class="detail-label">
                    Selected Seats
                </span>

                <span class="detail-value seats-value">

                    <?php

                    echo htmlspecialchars(
                        $seat_display
                    );

                    ?>

                </span>

            </div>


            <div class="detail">

                <span class="detail-label">
                    Number of Seats
                </span>

                <span class="detail-value">

                    <?php

                    echo $seat_count;

                    ?>

                </span>

            </div>


            <div class="detail">

                <span class="detail-label">
                    Price Per Seat
                </span>

                <span class="detail-value">

                    ₹<?php

                    echo number_format(
                        $price_per_seat,
                        2
                    );

                    ?>

                </span>

            </div>


            <!-- PAYMENT METHODS -->

            <div class="payment-methods">

                <div class="section-title">

                    Select Payment Method

                </div>


                <label class="method">

                    <input
                        type="radio"
                        name="payment_method_radio"
                        value="UPI"
                        form="paymentForm"
                        required
                    >

                    <div>

                        <div class="method-title">
                            UPI
                        </div>

                        <div class="method-description">
                            Pay using UPI
                        </div>

                    </div>

                </label>


                <label class="method">

                    <input
                        type="radio"
                        name="payment_method_radio"
                        value="Card"
                        form="paymentForm"
                    >

                    <div>

                        <div class="method-title">
                            Debit / Credit Card
                        </div>

                        <div class="method-description">
                            Pay securely using your card
                        </div>

                    </div>

                </label>


                <label class="method">

                    <input
                        type="radio"
                        name="payment_method_radio"
                        value="Net Banking"
                        form="paymentForm"
                    >

                    <div>

                        <div class="method-title">
                            Net Banking
                        </div>

                        <div class="method-description">
                            Pay using your bank account
                        </div>

                    </div>

                </label>

            </div>


        </div>


        <!-- ==================================================
             RIGHT SUMMARY
        ================================================== -->

        <div class="card">


            <div class="summary-title">

                Payment Summary

            </div>


            <div class="summary-row">

                <span>
                    Movie
                </span>

                <strong>

                    <?php

                    echo htmlspecialchars(
                        $movie['title']
                    );

                    ?>

                </strong>

            </div>


            <div class="summary-row">

                <span>
                    Seats
                </span>

                <strong>

                    <?php

                    echo $seat_count;

                    ?>

                </strong>

            </div>


            <div class="summary-row">

                <span>
                    Price / Seat
                </span>

                <strong>

                    ₹<?php

                    echo number_format(
                        $price_per_seat,
                        2
                    );

                    ?>

                </strong>

            </div>


            <div class="summary-row">

                <span>
                    Sub Total
                </span>

                <strong>

                    ₹<?php

                    echo number_format(
                        $total_amount,
                        2
                    );

                    ?>

                </strong>

            </div>


            <div class="summary-row">

                <span>
                    Convenience Fee
                </span>

                <strong>
                    ₹0.00
                </strong>

            </div>


            <div class="total-row">

                <span class="total-label">
                    Total Amount
                </span>

                <span class="total-price">

                    ₹<?php

                    echo number_format(
                        $total_amount,
                        2
                    );

                    ?>

                </span>

            </div>


            <!-- ==================================================
                 PAYMENT FORM
            ================================================== -->

            <form
                action="payment_success.php"
                method="POST"
                id="paymentForm"
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
                        $booking_date
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
                    value="<?php
                    echo htmlspecialchars(
                        $selected_seats
                    );
                    ?>"
                >


                <input
                    type="hidden"
                    name="total_amount"
                    value="<?php
                    echo $total_amount;
                    ?>"
                >


                <input
                    type="hidden"
                    name="movie_title"
                    value="<?php
                    echo htmlspecialchars(
                        $movie['title']
                    );
                    ?>"
                >


                <button
                    type="submit"
                    class="pay-button"
                >

                    PAY ₹<?php

                    echo number_format(
                        $total_amount,
                        2
                    );

                    ?>

                </button>


            </form>


            <div class="secure">

                🔒 Secure Payment

            </div>


        </div>


    </div>


</main>


<footer class="footer">

    ©️ <?php echo date('Y'); ?>
    CineBook. All rights reserved.

</footer>


</body>

</html>