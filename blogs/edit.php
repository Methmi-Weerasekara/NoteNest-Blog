<?php

require_once "../includes/auth_check.php";
require_once "../includes/csrf.php";
require_once "../config/database.php";
require_once "../includes/header.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Invalid note.");
}

$blog_id = (int) $_GET["id"];
$user_id = $_SESSION["user_id"];

$sql = "SELECT id, title, content, category_id
        FROM blogpost
        WHERE id = ? AND user_id = ?";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $blog_id, $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    die("You are not authorized to edit this note.");
}

$blog = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

$categories = mysqli_query(
    $conn,
    "SELECT id, name FROM category ORDER BY name"
);

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (
        !isset($_POST["csrf_token"]) ||
        !verifyCsrfToken($_POST["csrf_token"])
    ) {
        die("Invalid CSRF token.");
    }

    $title = trim($_POST["title"]);
    $content = trim($_POST["content"]);
    $category_id = $_POST["category_id"];

    if (empty($title) || empty($content) || empty($category_id)) {

        $message = "Please fill in all fields.";

    } else {

        $updateSql = "UPDATE blogpost
                      SET title = ?,
                          content = ?,
                          category_id = ?
                      WHERE id = ? AND user_id = ?";

        $updateStmt = mysqli_prepare($conn, $updateSql);

        mysqli_stmt_bind_param(
            $updateStmt,
            "ssiii",
            $title,
            $content,
            $category_id,
            $blog_id,
            $user_id
        );

        if (mysqli_stmt_execute($updateStmt)) {

            header("Location: view.php?id=" . $blog_id);
            exit();

        } else {

            $message = "Failed to update note.";
        }

        mysqli_stmt_close($updateStmt);
    }
}

?>

<main class="editor-page">

    <div class="editor-container">

        <div class="editor-header">

            <div>

                <span class="editor-label">
                    NOTE EDITOR
                </span>

                <h1>Edit Note</h1>

                <p class="editor-description">
                    Update your note and keep your knowledge up to date.
                </p>

            </div>

        </div>


        <?php if (!empty($message)): ?>

            <div class="editor-message error">
                <?php echo htmlspecialchars($message); ?>
            </div>

        <?php endif; ?>


        <form method="POST" class="note-form">

            <input
    type="hidden"
    name="csrf_token"
    value="<?php echo htmlspecialchars(generateCsrfToken()); ?>"
>
            <!-- Title -->

            <div class="form-group">

                <label for="title">
                    Note Title
                </label>

                <input
                    type="text"
                    id="title"
                    name="title"
                    value="<?php echo htmlspecialchars($blog["title"]); ?>"
                    placeholder="Give your note a clear title..."
                    required
                >

                <small>
                    Choose a title that helps others understand your note.
                </small>

            </div>


            <!-- Category -->

            <div class="form-group">

                <label for="category">
                    Category
                </label>

                <select
                    id="category"
                    name="category_id"
                    required
                >

                    <?php while ($category = mysqli_fetch_assoc($categories)): ?>

                        <option
                            value="<?php echo $category["id"]; ?>"
                            <?php
                            if ($category["id"] == $blog["category_id"]) {
                                echo "selected";
                            }
                            ?>
                        >
                            <?php echo htmlspecialchars($category["name"]); ?>
                        </option>

                    <?php endwhile; ?>

                </select>

            </div>


            <!-- Content -->

            <div class="form-group">

                <div class="content-label-row">

                    <label for="content">
                        Note Content
                    </label>

                    <span id="character-count">
                        <?php echo strlen($blog["content"]); ?> characters
                    </span>

                </div>

                <textarea
                    id="content"
                    name="content"
                    rows="18"
                    placeholder="Write your note here..."
                    required
                ><?php echo htmlspecialchars($blog["content"]); ?></textarea>

                <small>
                    Make changes to your note and save them when you're done.
                </small>

            </div>


            <!-- Actions -->

            <div class="editor-actions">

                <a
                    href="view.php?id=<?php echo $blog_id; ?>"
                    class="cancel-btn"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="publish-btn"
                >
                    Save Changes
                </button>

            </div>

        </form>

    </div>

</main>