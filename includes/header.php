<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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

    <title>NoteNest</title>

    <link
        rel="stylesheet"
        href="<?php echo BASE_URL; ?>/css/style.css"
    >

    <script
        src="<?php echo BASE_URL; ?>/js/script.js"
        defer
    ></script>

</head>

<body>

<header class="site-header">

    <nav class="navbar">

        <!-- Logo -->
        <div class="nav-left">

            <a
                href="<?php echo BASE_URL; ?>/index.php"
                class="logo"
            >
                <span class="logo-icon">📚</span>
                <span>NoteNest</span>
            </a>

        </div>


        <!-- Center Navigation -->
        <div class="nav-center">

            <a href="<?php echo BASE_URL; ?>/index.php">
                Home
            </a>

            <?php if (isset($_SESSION["user_id"])): ?>

                <a href="<?php echo BASE_URL; ?>/blogs/create.php">
                    Create Note
                </a>

            <?php endif; ?>

        </div>


        <!-- Right Side -->
        <div class="nav-right">

            <?php if (isset($_SESSION["user_id"])): ?>

                <div class="profile-dropdown">

                    <button
                        class="profile-button"
                        id="profileButton"
                        type="button"
                    >

                        <span class="avatar">
                            <?php
                            echo strtoupper(
                                substr(
                                    $_SESSION["username"],
                                    0,
                                    1
                                )
                            );
                            ?>
                        </span>

                        <span class="profile-name">
                            <?php
                            echo htmlspecialchars(
                                $_SESSION["username"]
                            );
                            ?>
                        </span>

                        <span class="dropdown-arrow">
                            ▾
                        </span>

                    </button>


                    <div
                        class="dropdown-menu"
                        id="profileMenu"
                    >

                        <a href="<?php echo BASE_URL; ?>/index.php">
                            🏠 Home
                        </a>

                        <a href="<?php echo BASE_URL; ?>/blogs/create.php">
                            ✏️ Create Note
                        </a>

                        <div class="dropdown-divider"></div>

                        <a
                            href="<?php echo BASE_URL; ?>/auth/logout.php"
                            class="dropdown-logout"
                        >
                            🚪 Logout
                        </a>

                    </div>

                </div>


            <?php else: ?>

                <a style="margin-right: 20px"
                    href="<?php echo BASE_URL; ?>/auth/login.php"
                    class="login-link"
                >
                    Login
                </a>

                <a
                    href="<?php echo BASE_URL; ?>/auth/register.php"
                    class="register-btn"
                >
                    Get Started
                </a>

            <?php endif; ?>

        </div>

    </nav>

</header>