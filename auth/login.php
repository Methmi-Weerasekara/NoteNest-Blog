<?php

session_start();

require_once "../config/database.php";
require_once "../includes/header.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $sql = "SELECT id, username, password, role FROM user WHERE email = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) === 1) {

        $user = mysqli_fetch_assoc($result);

       if (password_verify($password, $user["password"])) {

    $_SESSION["user_id"] = $user["id"];
    $_SESSION["username"] = $user["username"];
    $_SESSION["role"] = $user["role"];

    if (isset($_POST["remember_email"])) {
        setcookie("remember_email", $email, time() + (86400 * 30), "/");
    } else {
        setcookie("remember_email", "", time() - 3600, "/");
    }

    header("Location: ../index.php");
    exit();

        } else {
            $message = "Invalid email or password.";
        }

    } else {
        $message = "Invalid email or password.";
    }

    mysqli_stmt_close($stmt);
}

?>

<main class="auth-page">

    <div class="auth-container">

        <div class="auth-card">

            <div class="auth-header">

                <div class="auth-icon">
                    🔐
                </div>

                <h1>Welcome back</h1>

                <p>
                    Sign in to continue to NoteNest
                </p>

            </div>


            <?php if (!empty($message)): ?>

                <div class="auth-message error">
                    <?php echo htmlspecialchars($message); ?>
                </div>

            <?php endif; ?>


            <form
                method="POST"
                class="auth-form"
            >

                <div class="form-group">

                    <label for="email">
                        Email address
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        placeholder="Enter your email"
                        value="<?php
                            echo isset($_COOKIE['remember_email'])
                                ? htmlspecialchars($_COOKIE['remember_email'])
                                : '';
                        ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="password">
                        Password
                    </label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Enter your password"
                        required
                    >

                </div>


                <div class="remember-row">

                    <label class="remember-label">

                        <input
                            type="checkbox"
                            name="remember_email"
                            <?php
                                if (isset($_COOKIE['remember_email'])) {
                                    echo "checked";
                                }
                            ?>
                        >

                        <span>Remember my email</span>

                    </label>

                </div>


                <button
                    type="submit"
                    class="auth-button"
                >
                    Sign In
                </button>

            </form>


            <div class="auth-footer">

                <p>
                    Don't have an account?
                    <a href="<?php echo BASE_URL; ?>/auth/register.php">
                        Create one
                    </a>
                </p>

            </div>

        </div>

    </div>

</main>


<?php require_once "../includes/footer.php"; ?>