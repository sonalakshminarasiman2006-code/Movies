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

$name = "";
$email = "";

$error = "";


// ==================================================
// REGISTER
// ==================================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";


    // --------------------------------------------------
    // VALIDATION
    // --------------------------------------------------

    if ($name === "") {

        $error = "Please enter your name.";

    } elseif ($email === "") {

        $error = "Please enter your email address.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } elseif ($password === "") {

        $error = "Please enter a password.";

    } elseif (strlen($password) < 6) {

        $error = "Password must contain at least 6 characters.";

    } else {


        // --------------------------------------------------
        // CHECK EXISTING EMAIL
        // --------------------------------------------------

        $check = mysqli_prepare(
            $conn,
            "SELECT id FROM users WHERE email = ? LIMIT 1"
        );

        mysqli_stmt_bind_param(
            $check,
            "s",
            $email
        );

        mysqli_stmt_execute($check);

        mysqli_stmt_store_result($check);


        if (mysqli_stmt_num_rows($check) > 0) {

            $error = "An account with this email already exists.";

        } else {


            // --------------------------------------------------
            // HASH PASSWORD
            // --------------------------------------------------

            $hashed_password = password_hash(
                $password,
                PASSWORD_DEFAULT
            );


            // --------------------------------------------------
            // INSERT USER
            //
            // IMPORTANT:
            // Your database column is `name`
            // NOT `full_name`
            // --------------------------------------------------

            $insert = mysqli_prepare(
                $conn,
                "INSERT INTO users
                    (name, email, password)
                 VALUES
                    (?, ?, ?)"
            );


            mysqli_stmt_bind_param(
                $insert,
                "sss",
                $name,
                $email,
                $hashed_password
            );


            if (mysqli_stmt_execute($insert)) {

                // ------------------------------------------
                // GET NEW USER ID
                // ------------------------------------------

                $user_id =
                    mysqli_insert_id($conn);


                // ------------------------------------------
                // LOGIN IMMEDIATELY
                // ------------------------------------------

                $_SESSION["user_id"] =
                    $user_id;

                $_SESSION["name"] =
                    $name;

                $_SESSION["email"] =
                    $email;


                // ------------------------------------------
                // DIRECT TO DASHBOARD
                // ------------------------------------------

                header(
                    "Location: dashboard.php"
                );

                exit();

            } else {

                $error =
                    "Registration failed. Please try again.";

            }

        }

        mysqli_stmt_close($check);
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
    CineBook | Create Account
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
            #fff5f5 50%,
            #ffe9e9 100%
        );

    display: flex;

    align-items: center;

    justify-content: center;

    color: #171717;

    padding: 25px;
}


/* ==================================================
   MAIN CONTAINER
================================================== */

.register-wrapper {

    width: 100%;

    max-width: 1050px;

    min-height: 650px;

    background: #ffffff;

    border-radius: 24px;

    overflow: hidden;

    display: grid;

    grid-template-columns: 45% 55%;

    box-shadow:
        0 25px 70px
        rgba(0, 0, 0, 0.12);
}


/* ==================================================
   LEFT SIDE
================================================== */

.brand-section {

    position: relative;

    background:
        linear-gradient(
            145deg,
            #9f0000,
            #e50914 50%,
            #c40000
        );

    color: #ffffff;

    padding: 55px;

    display: flex;

    flex-direction: column;

    justify-content: space-between;

    overflow: hidden;
}


/* Decorative circles */

.brand-section::before {

    content: "";

    position: absolute;

    width: 320px;

    height: 320px;

    border: 1px solid
        rgba(255,255,255,.15);

    border-radius: 50%;

    top: -100px;

    right: -100px;
}


.brand-section::after {

    content: "";

    position: absolute;

    width: 450px;

    height: 450px;

    border: 1px solid
        rgba(255,255,255,.10);

    border-radius: 50%;

    bottom: -250px;

    left: -150px;
}


/* ==================================================
   LOGO
================================================== */

.logo {

    position: relative;

    z-index: 2;

    font-size: 27px;

    font-weight: 900;

    letter-spacing: -1px;
}


.logo span {
    color: #ffffff;
}


/* ==================================================
   BRAND CONTENT
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


.brand-content h1 {

    font-size: 44px;

    line-height: 1.05;

    letter-spacing: -2px;

    margin-bottom: 18px;
}


.brand-content h1 span {
    color: #ffe0e0;
}


.brand-content p {

    max-width: 340px;

    color: rgba(255,255,255,.78);

    font-size: 12px;

    line-height: 1.8;
}


/* ==================================================
   BRAND FOOTER
================================================== */

.brand-footer {

    position: relative;

    z-index: 2;

    font-size: 9px;

    color: rgba(255,255,255,.6);

    letter-spacing: .5px;
}


/* ==================================================
   RIGHT SIDE
================================================== */

.form-section {

    padding: 55px 65px;

    display: flex;

    align-items: center;

    justify-content: center;
}


.form-container {

    width: 100%;

    max-width: 420px;
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


.form-header h2 {

    font-size: 31px;

    margin-top: 8px;

    letter-spacing: -1px;
}


.form-header p {

    margin-top: 8px;

    color: #888888;

    font-size: 11px;

    line-height: 1.6;
}


/* ==================================================
   ERROR
================================================== */

.error-message {

    background: #fff1f1;

    border: 1px solid #ffd2d2;

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

    margin-bottom: 18px;
}


.form-group label {

    display: block;

    margin-bottom: 8px;

    font-size: 10px;

    font-weight: 800;

    color: #333333;
}


.input-box {

    position: relative;
}


.input-box .icon {

    position: absolute;

    left: 14px;

    top: 50%;

    transform: translateY(-50%);

    color: #999999;

    font-size: 13px;
}


.form-group input {

    width: 100%;

    height: 48px;

    border: 1px solid #dddddd;

    border-radius: 9px;

    padding: 0 15px 0 42px;

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
        rgba(229,9,20,.08);
}


/* ==================================================
   REGISTER BUTTON
================================================== */

.register-btn {

    width: 100%;

    height: 50px;

    border: none;

    border-radius: 9px;

    background: #e50914;

    color: #ffffff;

    font-size: 10px;

    font-weight: 900;

    letter-spacing: .5px;

    cursor: pointer;

    margin-top: 5px;

    transition: .2s;

    box-shadow:
        0 8px 20px
        rgba(229,9,20,.22);
}


.register-btn:hover {

    background: #bd0710;

    transform: translateY(-1px);
}


/* ==================================================
   LOGIN LINK
================================================== */

.login-link {

    text-align: center;

    margin-top: 24px;

    color: #888888;

    font-size: 10px;
}


.login-link a {

    color: #e50914;

    font-weight: 800;

    text-decoration: none;

    margin-left: 4px;
}


.login-link a:hover {
    text-decoration: underline;
}


/* ==================================================
   RESPONSIVE
================================================== */

@media (max-width: 800px) {

    .register-wrapper {

        grid-template-columns: 1fr;

        max-width: 500px;
    }


    .brand-section {

        min-height: 260px;

        padding: 35px;
    }


    .brand-content h1 {

        font-size: 34px;
    }


    .brand-footer {
        display: none;
    }


    .form-section {

        padding: 40px 30px;
    }

}


@media (max-width: 450px) {

    body {
        padding: 10px;
    }


    .register-wrapper {
        border-radius: 16px;
    }


    .brand-section {
        padding: 30px 25px;
    }


    .form-section {
        padding: 35px 22px;
    }

}

</style>

</head>


<body>


<div class="register-wrapper">


    <!-- ==================================================
         LEFT BRAND SECTION
    ================================================== -->

    <section class="brand-section">


        <div class="logo">

            CINE<span>BOOK</span>

        </div>


        <div class="brand-content">

            <div class="tag">
                YOUR MOVIE EXPERIENCE
            </div>


            <h1>

                Movies.
                <br>

                Seats.
                <br>

                <span>Memories.</span>

            </h1>


            <p>

                Create your CineBook account and
                enjoy a simple, fast and beautiful
                movie booking experience.

            </p>

        </div>


        <div class="brand-footer">

            Secure account • Easy booking • Instant access

        </div>


    </section>



    <!-- ==================================================
         REGISTER FORM
    ================================================== -->

    <section class="form-section">


        <div class="form-container">


            <div class="form-header">

                <small>
                    GET STARTED
                </small>


                <h2>
                    Create Account
                </h2>


                <p>
                    Create your account to start booking
                    your favourite movies.
                </p>

            </div>



            <?php if ($error !== ""): ?>

                <div class="error-message">

                    <?php
                    echo htmlspecialchars(
                        $error
                    );
                    ?>

                </div>

            <?php endif; ?>



            <form
                method="POST"
                action="register.php"
            >


                <!-- ==================================================
                     NAME
                     DATABASE COLUMN = name
                ================================================== -->

                <div class="form-group">

                    <label for="name">
                        FULL NAME
                    </label>


                    <div class="input-box">

                        <span class="icon">
                            👤
                        </span>


                        <input
                            type="text"
                            id="name"
                            name="name"
                            placeholder="Enter your name"
                            value="<?php
                            echo htmlspecialchars(
                                $name
                            );
                            ?>"
                            autocomplete="name"
                            required
                        >

                    </div>

                </div>



                <!-- ==================================================
                     EMAIL
                ================================================== -->

                <div class="form-group">

                    <label for="email">
                        EMAIL ADDRESS
                    </label>


                    <div class="input-box">

                        <span class="icon">
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



                <!-- ==================================================
                     PASSWORD
                     NO CONFIRM PASSWORD
                ================================================== -->

                <div class="form-group">

                    <label for="password">
                        PASSWORD
                    </label>


                    <div class="input-box">

                        <span class="icon">
                            🔒
                        </span>


                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Create a password"
                            minlength="6"
                            autocomplete="new-password"
                            required
                        >

                    </div>

                </div>



                <!-- ==================================================
                     REGISTER
                ================================================== -->

                <button
                    type="submit"
                    class="register-btn"
                >

                    CREATE ACCOUNT

                </button>


            </form>



            <!-- ==================================================
                 LOGIN
            ================================================== -->

            <div class="login-link">

                Already have an account?

                <a href="login.php">
                    Login
                </a>

            </div>


        </div>

    </section>


</div>


</body>

</html>