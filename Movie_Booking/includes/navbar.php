<?php

$current_page =
    basename($_SERVER["PHP_SELF"]);

?>

<header class="site-header">


    <div class="navbar">


        <!-- LOGO -->

        <a
            href="/movie_booking/user/dashboard.php"
            class="nav-logo"
        >

            CINE<span>BOOK</span>

        </a>


        <!-- NAVIGATION -->

        <nav class="main-nav">


            <a
                href="/movie_booking/user/dashboard.php"
                class="<?= $current_page === 'dashboard.php' ? 'active' : ''; ?>"
            >
                Home
            </a>


            <a
                href="/movie_booking/movies/index.php"
                class="<?= $current_page === 'index.php' ? 'active' : ''; ?>"
            >
                Movies
            </a>


            <a
                href="/movie_booking/user/bookings.php"
                class="<?= $current_page === 'bookings.php' ? 'active' : ''; ?>"
            >
                My Bookings
            </a>


            <a
                href="/movie_booking/user/profile.php"
                class="<?= $current_page === 'profile.php' ? 'active' : ''; ?>"
            >
                Profile
            </a>


        </nav>


        <!-- RIGHT SIDE -->

        <div class="nav-right">


            <?php if (isset($_SESSION["user_id"])): ?>


                <div class="nav-user">

                    <div class="nav-avatar">

                        <?= strtoupper(
                            substr(
                                $_SESSION["user_name"],
                                0,
                                1
                            )
                        ); ?>

                    </div>


                    <span>

                        <?= htmlspecialchars(
                            $_SESSION["user_name"]
                        ); ?>

                    </span>

                </div>


                <a
                    href="/movie_booking/auth/logout.php"
                    class="logout-button"
                >
                    Logout
                </a>


            <?php else: ?>


                <a
                    href="/movie_booking/auth/login.php"
                    class="nav-login"
                >
                    Login
                </a>


                <a
                    href="/movie_booking/auth/register.php"
                    class="nav-register"
                >
                    Register
                </a>


            <?php endif; ?>


        </div>


    </div>


</header>