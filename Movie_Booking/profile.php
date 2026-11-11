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
// PROFILE UPLOAD FOLDER
// ==================================================

$upload_dir = __DIR__ . "/uploads/profile/";

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}


// ==================================================
// MESSAGE VARIABLES
// ==================================================

$success = "";
$error = "";


// ==================================================
// GET USER DETAILS
// ==================================================

$stmt = mysqli_prepare(
    $conn,
    "SELECT
        id,
        name,
        email,
        phone,
        password
     FROM users
     WHERE id = ?
     LIMIT 1"
);

if (!$stmt) {
    die("Database error: " . mysqli_error($conn));
}

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $user_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


if (!$user) {
    die("User profile not found.");
}


// ==================================================
// PROFILE PHOTO FILE
// ==================================================

$photo_file =
    $upload_dir . "user_" . $user_id . ".jpg";

$photo_url =
    "uploads/profile/user_" . $user_id . ".jpg";


// ==================================================
// PHOTO UPLOAD
// ==================================================

if (
    isset($_POST['upload_photo'])
    &&
    isset($_FILES['profile_photo'])
) {

    $file = $_FILES['profile_photo'];


    if ($file['error'] !== UPLOAD_ERR_OK) {

        $error = "Please select a valid photo.";

    } else {

        $allowed_types = [
            'image/jpeg',
            'image/png',
            'image/webp'
        ];


        $file_type =
            mime_content_type(
                $file['tmp_name']
            );


        if (
            !in_array(
                $file_type,
                $allowed_types
            )
        ) {

            $error =
                "Only JPG, PNG or WEBP images are allowed.";

        } elseif (
            $file['size'] > 5 * 1024 * 1024
        ) {

            $error =
                "Photo size must be less than 5 MB.";

        } else {

            $image_info =
                getimagesize(
                    $file['tmp_name']
                );


            if ($image_info === false) {

                $error =
                    "The selected file is not a valid image.";

            } else {

                /*
                 * Convert the uploaded image
                 * into JPG so that every profile
                 * photo has the same filename.
                 */

                $source = null;


                if (
                    $file_type === 'image/jpeg'
                ) {

                    $source =
                        imagecreatefromjpeg(
                            $file['tmp_name']
                        );

                } elseif (
                    $file_type === 'image/png'
                ) {

                    $source =
                        imagecreatefrompng(
                            $file['tmp_name']
                        );

                } elseif (
                    $file_type === 'image/webp'
                ) {

                    $source =
                        imagecreatefromwebp(
                            $file['tmp_name']
                        );
                }


                if ($source) {

                    /*
                     * Create square image
                     */

                    $width =
                        imagesx($source);

                    $height =
                        imagesy($source);


                    $size =
                        min(
                            $width,
                            $height
                        );


                    $src_x =
                        ($width - $size) / 2;

                    $src_y =
                        ($height - $size) / 2;


                    $destination =
                        imagecreatetruecolor(
                            500,
                            500
                        );


                    imagecopyresampled(
                        $destination,
                        $source,
                        0,
                        0,
                        $src_x,
                        $src_y,
                        500,
                        500,
                        $size,
                        $size
                    );


                    if (
                        imagejpeg(
                            $destination,
                            $photo_file,
                            90
                        )
                    ) {

                        $success =
                            "Profile photo updated successfully.";

                    } else {

                        $error =
                            "Unable to save the profile photo.";
                    }


                    imagedestroy($source);

                    imagedestroy(
                        $destination
                    );

                } else {

                    $error =
                        "Unable to process this image.";
                }
            }
        }
    }
}


// ==================================================
// REMOVE PHOTO
// ==================================================

if (
    isset($_POST['remove_photo'])
) {

    if (
        file_exists($photo_file)
    ) {

        unlink($photo_file);

        $success =
            "Profile photo removed successfully.";

    } else {

        $error =
            "No profile photo found.";
    }
}


// ==================================================
// UPDATE PROFILE DETAILS
// ==================================================

if (
    isset($_POST['save_profile'])
) {

    $name =
        trim(
            $_POST['name'] ?? ''
        );

    $email =
        trim(
            $_POST['email'] ?? ''
        );

    $phone =
        trim(
            $_POST['phone'] ?? ''
        );


    if (
        $name === ''
        ||
        $email === ''
        ||
        $phone === ''
    ) {

        $error =
            "Please fill all details.";

    } elseif (
        !filter_var(
            $email,
            FILTER_VALIDATE_EMAIL
        )
    ) {

        $error =
            "Please enter a valid email address.";

    } else {

        $update = mysqli_prepare(
            $conn,
            "UPDATE users
             SET
                name = ?,
                email = ?,
                phone = ?
             WHERE id = ?"
        );


        if (!$update) {

            $error =
                "Database error: "
                . mysqli_error($conn);

        } else {

            mysqli_stmt_bind_param(
                $update,
                "sssi",
                $name,
                $email,
                $phone,
                $user_id
            );


            if (
                mysqli_stmt_execute(
                    $update
                )
            ) {

                $success =
                    "Profile updated successfully.";

                $user['name'] =
                    $name;

                $user['email'] =
                    $email;

                $user['phone'] =
                    $phone;

            } else {

                $error =
                    "Profile update failed: "
                    . mysqli_stmt_error(
                        $update
                    );
            }


            mysqli_stmt_close(
                $update
            );
        }
    }
}


// ==================================================
// CHECK PHOTO AGAIN
// ==================================================

$photo_exists =
    file_exists($photo_file);

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
    CineBook | Profile
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
            #fff5f5,
            #ffffff
        );

    color: #222222;

}


/* ==================================================
   HEADER
================================================== */

.header {

    height: 72px;

    background: #ffffff;

    border-bottom:
        1px solid #eeeeee;

    display: flex;

    align-items: center;

}


.header-inner {

    width: 92%;

    max-width: 1150px;

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

    gap: 25px;

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

    max-width: 850px;

    margin: auto;

    padding:
        35px 0 70px;

}


/* ==================================================
   BACK ARROW
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

    font-size: 22px;

    font-weight: bold;

    transition: .2s;

}


.back a:hover {

    background: #e50914;

    color: #ffffff;

    border-color:
        #e50914;

}


/* ==================================================
   PROFILE CARD
================================================== */

.profile-card {

    background: #ffffff;

    border:
        1px solid #eeeeee;

    border-radius: 20px;

    overflow: hidden;

    box-shadow:
        0 20px 55px
        rgba(0,0,0,.08);

}


/* ==================================================
   PROFILE HEADER
================================================== */

.profile-header {

    background:
        linear-gradient(
            135deg,
            #111111,
            #450608
        );

    color: #ffffff;

    padding: 35px;

    display: flex;

    align-items: center;

    gap: 25px;

}


/* ==================================================
   PHOTO AREA
================================================== */

.photo-wrapper {

    position: relative;

    width: 105px;

    height: 105px;

    flex-shrink: 0;

}


.profile-photo {

    width: 105px;

    height: 105px;

    border-radius: 50%;

    background: #e50914;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 38px;

    font-weight: 900;

    border:
        4px solid #ffffff;

    overflow: hidden;

}


.profile-photo img {

    width: 100%;

    height: 100%;

    object-fit: cover;

}


/* ==================================================
   EDIT PHOTO BUTTON
================================================== */

.edit-photo {

    position: absolute;

    right: -5px;

    bottom: -5px;

    width: 36px;

    height: 36px;

    border-radius: 50%;

    background: #ffffff;

    color: #e50914;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 17px;

    cursor: pointer;

    border:
        2px solid #e50914;

    box-shadow:
        0 4px 12px
        rgba(0,0,0,.2);

}


.edit-photo:hover {

    background: #e50914;

    color: #ffffff;

}


/* ==================================================
   PROFILE TEXT
================================================== */

.profile-header h1 {

    font-size: 25px;

}


.profile-header p {

    color: #cccccc;

    font-size: 10px;

    margin-top: 7px;

}


/* ==================================================
   PHOTO FORM
================================================== */

.photo-form {

    position: absolute;

    width: 1px;

    height: 1px;

    overflow: hidden;

}


/* ==================================================
   PHOTO OPTIONS
================================================== */

.photo-options {

    background: #ffffff;

    padding:
        15px 35px;

    border-bottom:
        1px solid #eeeeee;

    display: flex;

    gap: 10px;

}


.photo-btn {

    padding:
        9px 15px;

    border-radius: 7px;

    border:
        1px solid #dddddd;

    background: #ffffff;

    color: #333333;

    font-size: 9px;

    font-weight: 900;

    cursor: pointer;

}


.photo-btn:hover {

    border-color:
        #e50914;

    color: #e50914;

}


.remove-btn {

    color: #e50914;

}


/* ==================================================
   FORM AREA
================================================== */

.form-area {

    padding: 32px;

}


.form-title {

    font-size: 17px;

    font-weight: 900;

    margin-bottom: 22px;

}


/* ==================================================
   MESSAGE
================================================== */

.success {

    background: #effff3;

    border:
        1px solid #bde8c7;

    color: #217a35;

    padding: 12px;

    border-radius: 8px;

    font-size: 10px;

    margin-bottom: 20px;

}


.error {

    background: #fff1f1;

    border:
        1px solid #ffcaca;

    color: #c40000;

    padding: 12px;

    border-radius: 8px;

    font-size: 10px;

    margin-bottom: 20px;

}


/* ==================================================
   FORM GRID
================================================== */

.form-grid {

    display: grid;

    grid-template-columns:
        1fr 1fr;

    gap: 20px;

}


.form-group {

    display: flex;

    flex-direction: column;

}


.form-group.full {

    grid-column:
        1 / -1;

}


.form-group label {

    font-size: 9px;

    font-weight: 900;

    color: #777777;

    margin-bottom: 7px;

    letter-spacing: .5px;

}


.form-group input {

    width: 100%;

    padding: 13px;

    border:
        1px solid #dddddd;

    border-radius: 8px;

    outline: none;

    font-size: 11px;

    color: #222222;

}


.form-group input:focus {

    border-color:
        #e50914;

    box-shadow:
        0 0 0 3px
        rgba(229,9,20,.08);

}


/* ==================================================
   PASSWORD
================================================== */

.password-note {

    margin-top: 7px;

    color: #999999;

    font-size: 8px;

}


/* ==================================================
   BUTTONS
================================================== */

.buttons {

    margin-top: 25px;

    display: flex;

    gap: 10px;

}


.save-btn {

    border: none;

    background: #e50914;

    color: #ffffff;

    padding:
        14px 25px;

    border-radius: 8px;

    font-size: 10px;

    font-weight: 900;

    cursor: pointer;

}


.save-btn:hover {

    background: #b70710;

}


.cancel-btn {

    background: #ffffff;

    color: #333333;

    border:
        1px solid #dddddd;

    padding:
        14px 25px;

    border-radius: 8px;

    text-decoration: none;

    font-size: 10px;

    font-weight: 900;

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

@media(max-width:650px) {

    .nav {
        display: none;
    }


    .profile-header {

        padding: 25px;

        flex-direction: column;

        text-align: center;

    }


    .form-area {

        padding: 23px;

    }


    .photo-options {

        padding:
            15px 23px;

    }


    .form-grid {

        grid-template-columns: 1fr;

    }


    .form-group.full {

        grid-column: auto;

    }


    .buttons {

        flex-direction: column;

    }


    .save-btn,
    .cancel-btn {

        width: 100%;

        text-align: center;

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
    href="dashboard.php"
    title="Back"
>
    ←
</a>

</div>


<div class="profile-card">


<!-- ==================================================
     PROFILE HEADER
================================================== -->

<div class="profile-header">


<div class="photo-wrapper">


<div class="profile-photo">

<?php if ($photo_exists): ?>

<img
    src="<?php
        echo htmlspecialchars(
            $photo_url
        );
    ?>?v=<?php echo time(); ?>"
    alt="Profile Photo"
>

<?php else: ?>

<?php

echo strtoupper(
    substr(
        $user['name'],
        0,
        1
    )
);

?>

<?php endif; ?>

</div>


<!-- EDIT ICON -->

<label
    for="profile_photo"
    class="edit-photo"
    title="Edit Profile Photo"
>
    ✎
</label>


</div>


<div>

<h1>

<?php

echo htmlspecialchars(
    $user['name']
);

?>

</h1>


<p>
CineBook Customer Profile
</p>

</div>


</div>


<!-- ==================================================
     PHOTO UPLOAD FORM
================================================== -->

<form
    method="POST"
    enctype="multipart/form-data"
    id="photoForm"
>


<input
    type="file"
    name="profile_photo"
    id="profile_photo"
    class="photo-form"
    accept="image/jpeg,image/png,image/webp"
    onchange="document.getElementById('photoForm').submit();"
>


<input
    type="hidden"
    name="upload_photo"
    value="1"
>


</form>


<!-- ==================================================
     PHOTO OPTIONS
================================================== -->

<div class="photo-options">


<label
    for="profile_photo"
    class="photo-btn"
>
    ✎ EDIT PHOTO
</label>


<?php if ($photo_exists): ?>

<form
    method="POST"
    style="display:inline;"
>

<input
    type="hidden"
    name="remove_photo"
    value="1"
>

<button
    type="submit"
    class="photo-btn remove-btn"
>
    🗑 REMOVE PHOTO
</button>

</form>

<?php endif; ?>


</div>


<!-- ==================================================
     FORM AREA
================================================== -->

<div class="form-area">


<div class="form-title">
    Personal Details
</div>


<?php if ($success !== ""): ?>

<div class="success">

<?php

echo htmlspecialchars(
    $success
);

?>

</div>

<?php endif; ?>


<?php if ($error !== ""): ?>

<div class="error">

<?php

echo htmlspecialchars(
    $error
);

?>

</div>

<?php endif; ?>


<form
    method="POST"
    action="profile.php"
>


<div class="form-grid">


<!-- NAME -->

<div class="form-group">

<label>
NAME
</label>

<input
    type="text"
    name="name"
    value="<?php
        echo htmlspecialchars(
            $user['name']
        );
    ?>"
    required
>

</div>


<!-- EMAIL -->

<div class="form-group">

<label>
EMAIL ADDRESS
</label>

<input
    type="email"
    name="email"
    value="<?php
        echo htmlspecialchars(
            $user['email']
        );
    ?>"
    required
>

</div>


<!-- PHONE -->

<div class="form-group">

<label>
PHONE NUMBER
</label>

<input
    type="text"
    name="phone"
    value="<?php
        echo htmlspecialchars(
            $user['phone']
        );
    ?>"
    required
>

</div>


<!-- CUSTOMER ID -->

<div class="form-group">

<label>
CUSTOMER ID
</label>

<input
    type="text"
    value="<?php
        echo (int)$user['id'];
    ?>"
    readonly
>

</div>


<!-- PASSWORD -->

<div class="form-group full">

<label>
PASSWORD
</label>

<input
    type="password"
    value="<?php
        echo htmlspecialchars(
            $user['password']
        );
    ?>"
    readonly
>

<div class="password-note">
    Password is protected and cannot be edited here.
</div>

</div>


</div>


<!-- BUTTONS -->

<div class="buttons">


<button
    type="submit"
    name="save_profile"
    value="1"
    class="save-btn"
>
    SAVE CHANGES
</button>


<a
    href="dashboard.php"
    class="cancel-btn"
>
    BACK
</a>


</div>


</form>


</div>


</div>


</main>


<!-- ==================================================
     FOOTER
================================================== -->

<footer class="footer">

© <?php echo date('Y'); ?>
CineBook. All rights reserved.

</footer>


</body>

</html>