<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = $page_title ?? "CineBook";

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <meta
        name="description"
        content="CineBook - Movie Ticket Booking"
    >

    <title>
        <?= htmlspecialchars($page_title); ?>
    </title>

    <link
        rel="stylesheet"
        href="/movie_booking/assets/css/style.css"
    >

</head>

<body>