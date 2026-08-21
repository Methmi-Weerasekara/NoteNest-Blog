<?php

require_once "../config/database.php";
require_once "../includes/header.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];

    if (empty($username) || empty($email) || empty($password)) {
        $message = "Please fill in all fields.";
    } elseif ($password !== $confirmPassword) {
        $message = "Passwords do not match.";
    } else {

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO user (username, email, password)
                VALUES (?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "sss",
            $username,
            $email,
            $passwordHash
        );

        if (mysqli_stmt_execute($stmt)) {
            $message = "Registration successful!";
        } else {
            $message = "Registration failed. Email may already exist.";
        }

        mysqli_stmt_close($stmt);
    }
}

?>

<main class="auth-page">

    <div class="auth-container">

        <div class="auth-card">

            <div class="auth-header">

                <div class="auth-icon">
                    ✨
                </div>

                <h1>Join NoteNest</h1>

                <p>
                    Start sharing your knowledge with others
                </p>

            </div>


            <?php if (!empty($message)): ?>

                <div class="auth-message
                    <?php
                        echo ($message === "Registration successful!")
                            ? "success"
                            : "error";
                    ?>"
                >
                    <?php echo htmlspecialchars($message); ?>
                </div>

            <?php endif; ?>


            <form
                method="POST"
                class="auth-form"
            >

                <div class="form-group">

                    <label for="username">
                        Username
                    </label>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        placeholder="Choose a username"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="register-email">
                        Email address
                    </label>

                    <input
                        type="email"
                        id="register-email"
                        name="email"
                        placeholder="Enter your email"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="register-password">
                        Password
                    </label>

                    <input
                        type="password"
                        id="register-password"
                        name="password"
                        placeholder="Create a password"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="confirm-password">
                        Confirm password
                    </label>

                    <input
                        type="password"
                        id="confirm-password"
                        name="confirm_password"
                        placeholder="Re-enter your password"
                        required
                    >

                </div>


                <button
                    type="submit"
                    class="auth-button"
                >
                    Create Account
                </button>

            </form>


            <div class="auth-footer">

                <p>
                    Already have an account?

                    <a href="<?php echo BASE_URL; ?>/auth/login.php">
                        Sign in
                    </a>
                </p>

            </div>

        </div>

    </div>

</main>


<?php require_once "../includes/footer.php"; ?>