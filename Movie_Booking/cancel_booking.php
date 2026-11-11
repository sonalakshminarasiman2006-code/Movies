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

$booking_id = isset($_GET['id'])
    ? (int)$_GET['id']
    : 0;

if ($booking_id <= 0) {
    header("Location: mybooking.php");
    exit();
}


/* CHECK BOOKING */

$stmt = mysqli_prepare(
    $conn,
    "SELECT id
     FROM bookings
     WHERE id = ?
     AND user_id = ?
     LIMIT 1"
);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $booking_id,
    $user_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$booking = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


if (!$booking) {
    header("Location: mybooking.php");
    exit();
}


/* DELETE BOOKING */

$delete = mysqli_prepare(
    $conn,
    "DELETE FROM bookings
     WHERE id = ?
     AND user_id = ?"
);

mysqli_stmt_bind_param(
    $delete,
    "ii",
    $booking_id,
    $user_id
);

if (!mysqli_stmt_execute($delete)) {
    die(
        "Booking cancellation failed: "
        . mysqli_stmt_error($delete)
    );
}

mysqli_stmt_close($delete);


/* SUCCESS PAGE */

header(
    "Location: cancel_success.php?booking_id="
    . $booking_id
);

exit();

?>