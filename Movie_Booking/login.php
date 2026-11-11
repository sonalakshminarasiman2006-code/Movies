<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

require_once "database.php";


// ==================================================
// IF ALREADY LOGGED IN
// ==================================================

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}


// ==================================================
// VARIABLES
// ==================================================

$email = "";
$error = "";


// ==================================================
// LOGIN
// ==================================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";


    // --------------------------------------------------
    // VALIDATION
    // --------------------------------------------------

    if ($email === "") {

        $error = "Please enter your email address.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } elseif ($password === "") {

        $error = "Please enter your password.";

    } else {


        // --------------------------------------------------
        // FIND USER
        // --------------------------------------------------

        $stmt = mysqli_prepare(
            $conn,
            "SELECT id, name, email, password
             FROM users
             WHERE email = ?
             LIMIT 1"
        );


        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $email
        );


        mysqli_stmt_execute($stmt);


        $result = mysqli_stmt_get_result($stmt);


        $user = mysqli_fetch_assoc($result);


        // --------------------------------------------------
        // VERIFY PASSWORD
        // --------------------------------------------------

        if (
            $user &&
            password_verify(
                $password,
                $user["password"]
            )
        ) {


            // ------------------------------------------
            // CREATE SESSION
            // ------------------------------------------

            $_SESSION["user_id"] =
                $user["id"];

            $_SESSION["name"] =
                $user["name"];

            $_SESSION["email"] =
                $user["email"];


            // ------------------------------------------
            // DIRECT TO DASHBOARD
            // ------------------------------------------

            header(
                "Location: dashboard.php"
            );

            exit();


        } else {

            $error =
                "Incorrect email or password.";

        }


        mysqli_stmt_close($stmt);
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

<title>
    CineBook | Login
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

    min-height: 100vh;

    font-family:
        Arial,
        Helvetica,
        sans-serif;

    background:
        linear-gradient(
            135deg,
            #ffffff 0%,
            #fff6f6 50%,
            #ffe8e8 100%
        );

    display: flex;

    align-items: center;

    justify-content: center;

    color: #171717;

    padding: 25px;
}


/* ==================================================
   MAIN CARD
================================================== */

.login-wrapper {

    width: 100%;

    max-width: 1050px;

    min-height: 650px;

    background: #ffffff;

    border-radius: 24px;

    overflow: hidden;

    display: grid;

    grid-template-columns: 55% 45%;

    box-shadow:
        0 25px 70px
        rgba(0, 0, 0, 0.12);
}


/* ==================================================
   LEFT LOGIN FORM
================================================== */

.form-section {

    padding: 55px 70px;

    display: flex;

    align-items: center;

    justify-content: center;
}


.form-container {

    width: 100%;

    max-width: 410px;
}


/* ==================================================
   LOGO
================================================== */

.logo {

    color: #111111;

    font-size: 27px;

    font-weight: 900;

    letter-spacing: -1px;

    margin-bottom: 55px;
}


.logo span {
    color: #e50914;
}


/* ==================================================
   FORM HEADER
================================================== */

.form-header {

    margin-bottom: 30px;
}


.form-header small {

    color: #e50914;

    font-size: 9px;

    font-weight: 900;

    letter-spacing: 3px;
}


.form-header h1 {

    font-size: 34px;

    margin-top: 8px;

    letter-spacing: -1.5px;
}


.form-header p {

    margin-top: 9px;

    color: #888888;

    font-size: 11px;

    line-height: 1.7;
}


/* ==================================================
   ERROR
================================================== */

.error-message {

    background: #fff1f1;

    border: 1px solid #ffd1d1;

    color: #c00000;

    padding: 12px 14px;

    border-radius: 8px;

    font-size: 10px;

    margin-bottom: 18px;
}


/* ==================================================
   FORM GROUP
================================================== */

.form-group {

    margin-bottom: 19px;
}


.form-group label {

    display: block;

    margin-bottom: 8px;

    font-size: 10px;

    font-weight: 800;

    color: #333333;
}


/* ==================================================
   INPUT
================================================== */

.input-box {

    position: relative;
}


.input-icon {

    position: absolute;

    left: 14px;

    top: 50%;

    transform: translateY(-50%);

    color: #999999;

    font-size: 13px;

    pointer-events: none;
}


.form-group input {

    width: 100%;

    height: 50px;

    border: 1px solid #dddddd;

    border-radius: 9px;

    padding:
        0 45px 0 42px;

    background: #ffffff;

    color: #222222;

    font-size: 12px;

    outline: none;

    transition: .2s;
}


.form-group input:focus {

    border-color: #e50914;

    box-shadow:
        0 0 0 3px
        rgba(229, 9, 20, .08);
}


/* ==================================================
   SHOW PASSWORD
================================================== */

.password-toggle {

    position: absolute;

    right: 14px;

    top: 50%;

    transform: translateY(-50%);

    border: none;

    background: none;

    color: #888888;

    cursor: pointer;

    font-size: 10px;

    font-weight: 700;
}


.password-toggle:hover {
    color: #e50914;
}


/* ==================================================
   OPTIONS
================================================== */

.options {

    display: flex;

    align-items: center;

    justify-content: space-between;

    margin-top: 5px;

    margin-bottom: 24px;
}


.remember {

    display: flex;

    align-items: center;

    gap: 7px;

    color: #777777;

    font-size: 9px;
}


.remember input {

    width: 13px;

    height: 13px;

    accent-color: #e50914;

    cursor: pointer;
}


.forgot {

    color: #e50914;

    font-size: 9px;

    font-weight: 800;

    text-decoration: none;
}


.forgot:hover {
    text-decoration: underline;
}


/* ==================================================
   LOGIN BUTTON
================================================== */

.login-btn {

    width: 100%;

    height: 51px;

    border: none;

    border-radius: 9px;

    background: #e50914;

    color: #ffffff;

    font-size: 10px;

    font-weight: 900;

    letter-spacing: .6px;

    cursor: pointer;

    transition: .2s;

    box-shadow:
        0 9px 22px
        rgba(229, 9, 20, .23);
}


.login-btn:hover {

    background: #bd0710;

    transform: translateY(-1px);
}


/* ==================================================
   REGISTER LINK
================================================== */

.register-link {

    text-align: center;

    margin-top: 25px;

    color: #888888;

    font-size: 10px;
}


.register-link a {

    color: #e50914;

    font-weight: 800;

    text-decoration: none;

    margin-left: 4px;
}


.register-link a:hover {
    text-decoration: underline;
}


/* ==================================================
   RIGHT RED SECTION
================================================== */

.brand-section {

    position: relative;

    background:
        linear-gradient(
            145deg,
            #9e0000,
            #e50914 52%,
            #c40000
        );

    color: #ffffff;

    padding: 55px;

    display: flex;

    flex-direction: column;

    justify-content: space-between;

    overflow: hidden;
}


/* ==================================================
   DECORATIVE CIRCLES
================================================== */

.brand-section::before {

    content: "";

    position: absolute;

    width: 360px;

    height: 360px;

    border: 1px solid
        rgba(255,255,255,.15);

    border-radius: 50%;

    top: -120px;

    right: -120px;
}


.brand-section::after {

    content: "";

    position: absolute;

    width: 500px;

    height: 500px;

    border: 1px solid
        rgba(255,255,255,.10);

    border-radius: 50%;

    bottom: -300px;

    left: -180px;
}


/* ==================================================
   RED CONTENT
================================================== */

.brand-content {

    position: relative;

    z-index: 2;
}


.brand-content .tag {

    display: inline-block;

    padding: 7px 12px;

    border: 1px solid
        rgba(255,255,255,.35);

    border-radius: 30px;

    font-size: 9px;

    font-weight: 800;

    letter-spacing: 2px;

    margin-bottom: 22px;
}


.brand-content h2 {

    font-size: 44px;

    line-height: 1.05;

    letter-spacing: -2px;

    margin-bottom: 18px;
}


.brand-content h2 span {

    color: #ffe0e0;
}


.brand-content p {

    max-width: 340px;

    color:
        rgba(255,255,255,.78);

    font-size: 12px;

    line-height: 1.8;
}


/* ==================================================
   FEATURE LIST
================================================== */

.features {

    position: relative;

    z-index: 2;

    margin-top: 28px;
}


.feature {

    display: flex;

    align-items: center;

    gap: 11px;

    margin-bottom: 13px;

    color:
        rgba(255,255,255,.85);

    font-size: 10px;
}


.feature-dot {

    width: 22px;

    height: 22px;

    border-radius: 50%;

    background:
        rgba(255,255,255,.15);

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 9px;
}


/* ==================================================
   BRAND FOOTER
================================================== */

.brand-footer {

    position: relative;

    z-index: 2;

    color:
        rgba(255,255,255,.55);

    font-size: 9px;
}


/* ==================================================
   RESPONSIVE
================================================== */

@media (max-width: 850px) {

    .login-wrapper {

        grid-template-columns: 1fr;

        max-width: 500px;
    }


    .brand-section {

        order: -1;

        min-height: 300px;

        padding: 35px;
    }


    .brand-content h2 {

        font-size: 34px;
    }


    .brand-footer {

        display: none;
    }


    .form-section {

        padding: 45px 30px;
    }


    .logo {

        margin-bottom: 30px;
    }

}


@media (max-width: 450px) {

    body {
        padding: 10px;
    }


    .login-wrapper {
        border-radius: 16px;
    }


    .form-section {
        padding: 35px 22px;
    }


    .brand-section {
        padding: 30px 25px;
    }


    .options {
        align-items: flex-start;
    }

}

</style>

</head>


<body>


<div class="login-wrapper">


    <!-- ==================================================
         LOGIN FORM
    ================================================== -->

    <section class="form-section">


        <div class="form-container">


            <div class="logo">

                CINE<span>BOOK</span>

            </div>


            <div class="form-header">

                <small>
                    WELCOME BACK
                </small>


                <h1>
                    Sign in
                </h1>


                <p>
                    Login to continue your movie
                    booking experience.
                </p>

            </div>


            <!-- ==================================================
                 ERROR
            ================================================== -->

            <?php if ($error !== ""): ?>

                <div class="error-message">

                    <?php

                    echo htmlspecialchars(
                        $error
                    );

                    ?>

                </div>

            <?php endif; ?>


            <!-- ==================================================
                 LOGIN FORM
            ================================================== -->

            <form
                method="POST"
                action="login.php"
            >


                <!-- EMAIL -->

                <div class="form-group">

                    <label for="email">
                        EMAIL ADDRESS
                    </label>


                    <div class="input-box">

                        <span class="input-icon">
                            ✉
                        </span>


                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="Enter your email"
                            value="<?php
                            echo htmlspecialchars(
                                $email
                            );
                            ?>"
                            autocomplete="email"
                            required
                        >

                    </div>

                </div>


                <!-- PASSWORD -->

                <div class="form-group">

                    <label for="password">
                        PASSWORD
                    </label>


                    <div class="input-box">

                        <span class="input-icon">
                            🔒
                        </span>


                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            autocomplete="current-password"
                            required
                        >


                        <button
                            type="button"
                            class="password-toggle"
                            id="togglePassword"
                        >
                            SHOW
                        </button>

                    </div>

                </div>


                <!-- OPTIONS -->

                <div class="options">


                    <label class="remember">

                        <input
                            type="checkbox"
                            name="remember"
                        >

                        Remember me

                    </label>


                    <a
                        href="#"
                        class="forgot"
                        onclick="
                            alert('Please contact support to reset your password.');
                            return false;
                        "
                    >
                        Forgot Password?
                    </a>


                </div>


                <!-- LOGIN -->

                <button
                    type="submit"
                    class="login-btn"
                >

                    LOGIN TO CINEBOOK →

                </button>


            </form>


            <!-- REGISTER -->

            <div class="register-link">

                Don't have an account?

                <a href="register.php">
                    Create Account
                </a>

            </div>


        </div>

    </section>



    <!-- ==================================================
         RED BRAND SECTION
    ================================================== -->

    <section class="brand-section">


        <div></div>


        <div class="brand-content">

            <div class="tag">
                YOUR MOVIE EXPERIENCE
            </div>


            <h2>

                Your next
                <br>

                <span>movie night</span>
                <br>

                starts here.

            </h2>


            <p>

                Discover movies, choose your seats
                and enjoy a simple booking experience
                with CineBook.

            </p>


            <div class="features">


                <div class="feature">

                    <div class="feature-dot">
                        ✓
                    </div>

                    Easy movie booking

                </div>


                <div class="feature">

                    <div class="feature-dot">
                        ✓
                    </div>

                    Quick seat selection

                </div>


                <div class="feature">

                    <div class="feature-dot">
                        ✓
                    </div>

                    Manage your bookings

                </div>


            </div>

        </div>


        <div class="brand-footer">

            Secure login • Simple booking • CineBook

        </div>


    </section>


</div>


<script>

/* ==================================================
   SHOW / HIDE PASSWORD
================================================== */

const password =
    document.getElementById(
        "password"
    );


const toggle =
    document.getElementById(
        "togglePassword"
    );


toggle.addEventListener(
    "click",
    function () {

        if (
            password.type === "password"
        ) {

            password.type =
                "text";

            toggle.textContent =
                "HIDE";

        } else {

            password.type =
                "password";

            toggle.textContent =
                "SHOW";
        }

    }
);

</script>


</body>

</html>