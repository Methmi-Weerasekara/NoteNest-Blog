<?php

session_start();


require_once "../includes/csrf.php";
require_once "../config/database.php";
require_once "../includes/header.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Invalid note.");
}

$blog_id = (int) $_GET["id"];

$sql = "SELECT
    blogpost.id,
    blogpost.user_id,
    blogpost.title,
    blogpost.content,
    blogpost.created_at,
    blogpost.updated_at,
    user.username,
    category.name AS category_name
        FROM blogpost
        JOIN user ON blogpost.user_id = user.id
        JOIN category ON blogpost.category_id = category.id
        WHERE blogpost.id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $blog_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    die("Note not found.");
}

$blog = mysqli_fetch_assoc($result);
$message = "";
$likeMessage = "";

if (isset($_POST["like"])) {

    if (
        !isset($_POST["csrf_token"]) ||
        !verifyCsrfToken($_POST["csrf_token"])
    ) {
        die("Invalid CSRF token.");
    }

    if (!isset($_SESSION["user_id"])) {

        $likeMessage = "You must be logged in to like a note.";

    } else {

        $user_id = $_SESSION["user_id"];

        // Check whether the user already liked this note
        $checkLikeSql = "SELECT id
                         FROM bloglike
                         WHERE user_id = ?
                         AND blog_id = ?";

        $checkLikeStmt = mysqli_prepare($conn, $checkLikeSql);

        mysqli_stmt_bind_param(
            $checkLikeStmt,
            "ii",
            $user_id,
            $blog_id
        );

        mysqli_stmt_execute($checkLikeStmt);

        $checkLikeResult = mysqli_stmt_get_result($checkLikeStmt);

        // If already liked → unlike
        if (mysqli_num_rows($checkLikeResult) > 0) {

            $likeData = mysqli_fetch_assoc($checkLikeResult);
            $like_id = $likeData["id"];

            $deleteLikeSql = "DELETE FROM bloglike
                              WHERE id = ?";

            $deleteLikeStmt = mysqli_prepare(
                $conn,
                $deleteLikeSql
            );

            mysqli_stmt_bind_param(
                $deleteLikeStmt,
                "i",
                $like_id
            );

            mysqli_stmt_execute($deleteLikeStmt);

            mysqli_stmt_close($deleteLikeStmt);

        } else {

            // If not liked → like
            $insertLikeSql = "INSERT INTO bloglike
                              (user_id, blog_id)
                              VALUES (?, ?)";

            $insertLikeStmt = mysqli_prepare(
                $conn,
                $insertLikeSql
            );

            mysqli_stmt_bind_param(
                $insertLikeStmt,
                "ii",
                $user_id,
                $blog_id
            );

            mysqli_stmt_execute($insertLikeStmt);

            mysqli_stmt_close($insertLikeStmt);
        }

        mysqli_stmt_close($checkLikeStmt);

        // Refresh the page
        header("Location: view.php?id=" . $blog_id);
        exit();
    }
}
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["comment"])) {

    if (
        !isset($_POST["csrf_token"]) ||
        !verifyCsrfToken($_POST["csrf_token"])
    ) {
        die("Invalid CSRF token.");
    }

    if (!isset($_SESSION["user_id"])) {
        $message = "You must be logged in to comment.";
    } else {

        $comment = trim($_POST["comment"]);
        $user_id = $_SESSION["user_id"];

        if (empty($comment)) {

            $message = "Comment cannot be empty.";

        } else {

            $sql = "INSERT INTO comment (user_id, blog_id, content)
                    VALUES (?, ?, ?)";

            $stmt = mysqli_prepare($conn, $sql);

            mysqli_stmt_bind_param(
                $stmt,
                "iis",
                $user_id,
                $blog_id,
                $comment
            );

            if (mysqli_stmt_execute($stmt)) {
                header("Location: view.php?id=" . $blog_id);
                exit();
            }

            mysqli_stmt_close($stmt);
        }
    }
}
$commentSql = "SELECT
                    comment.content,
                    comment.created_at,
                    user.username
               FROM comment
               JOIN user ON comment.user_id = user.id
               WHERE comment.blog_id = ?
               ORDER BY comment.created_at DESC";

$commentStmt = mysqli_prepare($conn, $commentSql);

mysqli_stmt_bind_param(
    $commentStmt,
    "i",
    $blog_id
);

mysqli_stmt_execute($commentStmt);

$comments = mysqli_stmt_get_result($commentStmt);
$likeSql = "SELECT COUNT(*) AS like_count
            FROM bloglike
            WHERE blog_id = ?";

$likeStmt = mysqli_prepare($conn, $likeSql);

mysqli_stmt_bind_param(
    $likeStmt,
    "i",
    $blog_id
);

mysqli_stmt_execute($likeStmt);

$likeResult = mysqli_stmt_get_result($likeStmt);

$likeData = mysqli_fetch_assoc($likeResult);

$likeCount = $likeData["like_count"];


// Check whether current user has already liked this note

$userLiked = false;

if (isset($_SESSION["user_id"])) {

    $userLikeSql = "SELECT id
                    FROM bloglike
                    WHERE user_id = ?
                    AND blog_id = ?";

    $userLikeStmt = mysqli_prepare($conn, $userLikeSql);

    mysqli_stmt_bind_param(
        $userLikeStmt,
        "ii",
        $_SESSION["user_id"],
        $blog_id
    );

    mysqli_stmt_execute($userLikeStmt);

    $userLikeResult = mysqli_stmt_get_result($userLikeStmt);

    if (mysqli_num_rows($userLikeResult) > 0) {
        $userLiked = true;
    }

    mysqli_stmt_close($userLikeStmt);
}

?>

<main class="view-page">

    <div class="note-view-container">

        <!-- Note Header -->

        <div class="note-header">

            <span class="note-category">
                <?php echo htmlspecialchars($blog["category_name"]); ?>
            </span>

            <h1>
                <?php echo htmlspecialchars($blog["title"]); ?>
            </h1>

            <div class="note-meta">

                <div class="author-info">

                    <div class="author-avatar">
                        <?php
                        echo strtoupper(
                            substr($blog["username"], 0, 1)
                        );
                        ?>
                    </div>

                    <div>

                        <strong>
                            <?php echo htmlspecialchars($blog["username"]); ?>
                        </strong>

                        <div class="note-date">

                            Published
                            <?php echo htmlspecialchars($blog["created_at"]); ?>

                            <?php if ($blog["updated_at"] !== $blog["created_at"]): ?>

                                · Updated
                                <?php echo htmlspecialchars($blog["updated_at"]); ?>

                            <?php endif; ?>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        <!-- Note Content -->

        <article class="note-content">

            <?php echo nl2br(htmlspecialchars($blog["content"])); ?>

        </article>


        <!-- Actions -->

<!-- Actions -->

<div class="note-actions">

    <div class="like-section">

        <form method="POST" class="like-form">
                                <input
    type="hidden"
    name="csrf_token"
    value="<?php echo htmlspecialchars(generateCsrfToken()); ?>"
>
            <button
                type="submit"
                name="like"
                class="like-btn <?php echo $userLiked ? 'liked' : ''; ?>"
            >

                <?php if ($userLiked): ?>

                    ❤️ Liked

                <?php else: ?>

                    ♡ Like

                <?php endif; ?>

            </button>

            <span class="like-count">

                <?php echo $likeCount; ?>

                <?php echo ($likeCount == 1) ? "like" : "likes"; ?>

            </span>

        </form>

    </div>


    <?php if (
        isset($_SESSION["user_id"]) &&
        $_SESSION["user_id"] == $blog["user_id"]
    ): ?>

        <div class="owner-actions">

            <a
                href="edit.php?id=<?php echo $blog["id"]; ?>"
                class="edit-btn"
            >
                Edit Note
            </a>

            <a
                href="delete.php?id=<?php echo $blog["id"]; ?>"
                class="delete-btn"
                onclick="return confirm('Are you sure you want to delete this note?');"
            >
                Delete Note
            </a>

        </div>

    <?php endif; ?>

</div>

        <!-- Comments -->

        <section class="comments-section">

            <div class="comments-header">

                <h2>Comments</h2>

                <span>
                    <?php echo mysqli_num_rows($comments); ?>
                </span>

            </div>


            <!-- Comment Form -->

            <?php if (isset($_SESSION["user_id"])): ?>

                <form
                    method="POST"
                    class="comment-form"
                >
                    <input
    type="hidden"
    name="csrf_token"
    value="<?php echo htmlspecialchars(generateCsrfToken()); ?>"
>
                    <textarea
                        name="comment"
                        rows="4"
                        placeholder="Share your thoughts about this note..."
                        required
                    ></textarea>

                    <div class="comment-form-footer">

                        <span>
                            Be respectful and constructive.
                        </span>

                        <button
                            type="submit"
                            class="comment-btn"
                        >
                            Post Comment
                        </button>

                    </div>

                </form>

            <?php else: ?>

                <div class="login-comment-message">

                    <p>
                        <a href="../auth/login.php">
                            Log in
                        </a>
                        to join the discussion.
                    </p>

                </div>

            <?php endif; ?>


            <!-- Comments List -->

            <div class="comments-list">

                <?php if (mysqli_num_rows($comments) > 0): ?>

                    <?php while ($comment = mysqli_fetch_assoc($comments)): ?>

                        <div class="comment-card">

                            <div class="comment-avatar">

                                <?php
                                echo strtoupper(
                                    substr(
                                        $comment["username"],
                                        0,
                                        1
                                    )
                                );
                                ?>

                            </div>

                            <div class="comment-body">

                                <div class="comment-top">

                                    <strong>
                                        <?php
                                        echo htmlspecialchars(
                                            $comment["username"]
                                        );
                                        ?>
                                    </strong>

                                    <small>
                                        <?php
                                        echo htmlspecialchars(
                                            $comment["created_at"]
                                        );
                                        ?>
                                    </small>

                                </div>

                                <p>
                                    <?php
                                    echo nl2br(
                                        htmlspecialchars(
                                            $comment["content"]
                                        )
                                    );
                                    ?>
                                </p>

                            </div>

                        </div>

                    <?php endwhile; ?>

                <?php else: ?>

                    <div class="no-comments">

                        <div>💬</div>

                        <h3>No comments yet</h3>

                        <p>
                            Be the first to share your thoughts.
                        </p>

                    </div>

                <?php endif; ?>

            </div>

        </section>


        <!-- Back -->

        <a
            href="../index.php"
            class="back-link"
        >
            ← Back to Notes
        </a>

    </div>

</main>
<?php require_once "../includes/footer.php"; ?>