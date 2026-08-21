<?php

require_once "../includes/admin_check.php";
require_once "../includes/csrf.php";
require_once "../config/database.php";
require_once "../includes/header.php";

$message = "";


// Handle role update

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (
        !isset($_POST["csrf_token"]) ||
        !verifyCsrfToken($_POST["csrf_token"])
    ) {
        die("Invalid CSRF token.");
    }

    $user_id = (int) $_POST["user_id"];
    $new_role = $_POST["role"];

    // Prevent invalid roles

    if ($new_role !== "user" && $new_role !== "admin") {

        $message = "Invalid role.";

    // Prevent changing your own role

    } elseif ($user_id === (int) $_SESSION["user_id"]) {

        $message = "You cannot change your own role.";

    } else {

        $updateSql = "UPDATE user
                      SET role = ?
                      WHERE id = ?";

        $updateStmt = mysqli_prepare(
            $conn,
            $updateSql
        );

        mysqli_stmt_bind_param(
            $updateStmt,
            "si",
            $new_role,
            $user_id
        );

        if (mysqli_stmt_execute($updateStmt)) {

            $message = "User role updated successfully.";

        } else {

            $message = "Failed to update user role.";

        }

        mysqli_stmt_close($updateStmt);
    }
}


// Get all users

$sql = "SELECT id, username, email, role
        FROM user
        ORDER BY id ASC";

$result = mysqli_query($conn, $sql);

?>

<main class="admin-page">

    <div class="admin-container">

        <div class="admin-header">

            <h1>Manage Users</h1>

            <p>
                View and manage NoteNest users.
            </p>

        </div>


        <?php if (!empty($message)): ?>

            <div class="view-message">

                <?php echo htmlspecialchars($message); ?>

            </div>

        <?php endif; ?>


        <div class="admin-table-container">

            <table class="admin-table">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Action</th>

                    </tr>

                </thead>


                <tbody>

                    <?php while ($user = mysqli_fetch_assoc($result)): ?>

                        <tr>

                            <td>
                                <?php echo $user["id"]; ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $user["username"]
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $user["email"]
                                );
                                ?>
                            </td>

                            <td>

                                <?php if ($user["role"] === "admin"): ?>

                                    <strong>
                                        Admin
                                    </strong>

                                <?php else: ?>

                                    User

                                <?php endif; ?>

                            </td>


                            <td>

                                <?php if (
                                    $user["id"] != $_SESSION["user_id"]
                                ): ?>

                                    <form
                                        method="POST"
                                        class="role-form"
                                    >
                                    <input
    type="hidden"
    name="csrf_token"
    value="<?php echo htmlspecialchars(generateCsrfToken()); ?>"
>

                                        <input
                                            type="hidden"
                                            name="user_id"
                                            value="<?php echo $user["id"]; ?>"
                                        >

                                        <select name="role">

                                            <option
                                                value="user"
                                                <?php
                                                if ($user["role"] === "user") {
                                                    echo "selected";
                                                }
                                                ?>
                                            >
                                                User
                                            </option>

                                            <option
                                                value="admin"
                                                <?php
                                                if ($user["role"] === "admin") {
                                                    echo "selected";
                                                }
                                                ?>
                                            >
                                                Admin
                                            </option>

                                        </select>


                                        <button type="submit">
                                            Update
                                        </button>

                                    </form>

                                <?php else: ?>

                                    <span>
                                        Current account
                                    </span>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>


        <a
            href="index.php"
            class="back-link"
        >
            ← Back to Dashboard
        </a>

    </div>

</main>

<?php require_once "../includes/footer.php"; ?>