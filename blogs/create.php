<?php

require_once "../includes/auth_check.php";
require_once "../includes/csrf.php";
require_once "../config/database.php";
require_once "../includes/header.php";

$message = "";

$categories = mysqli_query($conn, "SELECT id, name FROM category ORDER BY name");

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
    $user_id = $_SESSION["user_id"];

    if (empty($title) || empty($content) || empty($category_id)) {

        $message = "Please fill in all fields.";

    } else {

        $sql = "INSERT INTO blogpost 
                (user_id, category_id, title, content)
                VALUES (?, ?, ?, ?)";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "iiss",
            $user_id,
            $category_id,
            $title,
            $content
        );

        if (mysqli_stmt_execute($stmt)) {
            $message = "Note created successfully!";
        } else {
            $message = "Failed to create note.";
        }

        mysqli_stmt_close($stmt);
    }
}

?>

<main class="editor-page">

    <div class="editor-container">

        <div class="editor-header">

            <div>
                <span class="editor-label">NOTE EDITOR</span>

                <h1>Create a New Note</h1>

                <p class="editor-description">
                    Share your knowledge with the NoteNest community.
                </p>
            </div>

        </div>


        <?php if (!empty($message)): ?>

            <div class="editor-message
                <?php
                    echo ($message === "Note created successfully!")
                        ? "success"
                        : "error";
                ?>"
            >
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

                    <option value="">
                        Select a category
                    </option>

                    <?php while ($category = mysqli_fetch_assoc($categories)): ?>

                        <option
                            value="<?php echo $category["id"]; ?>"
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
                        0 characters
                    </span>

                </div>

                <textarea
                    id="content"
                    name="content"
                    rows="18"
                    placeholder="Start writing your note here..."
                    required
                ></textarea>

                <small>
                    Share clear and useful information that other students
                    can learn from.
                </small>

            </div>


            <!-- Actions -->

            <div class="editor-actions">

                <a
                    href="<?php echo BASE_URL; ?>/index.php"
                    class="cancel-btn"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="publish-btn"
                >
                    Publish Note
                </button>

            </div>

        </form>

    </div>

</main>