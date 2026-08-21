<?php

require_once "../includes/admin_check.php";
require_once "../includes/csrf.php";
require_once "../config/database.php";
require_once "../includes/header.php";

$message = "";


// Add category

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (
        !isset($_POST["csrf_token"]) ||
        !verifyCsrfToken($_POST["csrf_token"])
    ) {
        die("Invalid CSRF token.");
    }

    $name = trim($_POST["name"]);

    if (empty($name)) {

        $message = "Category name cannot be empty.";

    } else {

        $sql = "INSERT INTO category (name)
                VALUES (?)";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $name
        );

        if (mysqli_stmt_execute($stmt)) {

            $message = "Category added successfully.";

        } else {

            $message = "Failed to add category.";

        }

        mysqli_stmt_close($stmt);
    }
}


// Get categories

$result = mysqli_query(
    $conn,
    "SELECT id, name
     FROM category
     ORDER BY name"
);

?>

<main class="admin-page">

    <div class="admin-container">

        <div class="admin-header">

            <h1>Manage Categories</h1>

            <p>
                Add and manage categories for NoteNest notes.
            </p>

        </div>


        <?php if (!empty($message)): ?>

            <div class="view-message">

                <?php echo htmlspecialchars($message); ?>

            </div>

        <?php endif; ?>


        <form method="POST">
                <input
    type="hidden"
    name="csrf_token"
    value="<?php echo htmlspecialchars(generateCsrfToken()); ?>"
>
            <label for="name">
                Category Name
            </label>

            <input
                type="text"
                id="name"
                name="name"
                placeholder="Enter category name"
                required
            >

            <button type="submit">
                Add Category
            </button>

        </form>


        <br>


        <div class="admin-table-container">

            <table class="admin-table">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Category Name</th>

                    </tr>

                </thead>


                <tbody>

                    <?php while ($category = mysqli_fetch_assoc($result)): ?>

                        <tr>

                            <td>
                                <?php echo $category["id"]; ?>
                            </td>

                            <td>
                                <?php
                                echo htmlspecialchars(
                                    $category["name"]
                                );
                                ?>
                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>


        <br>

        <a
            href="index.php"
            class="back-link"
        >
            ← Back to Dashboard
        </a>

    </div>

</main>

<?php require_once "../includes/footer.php"; ?>