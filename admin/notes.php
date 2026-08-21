<?php

require_once "../includes/admin_check.php";
require_once "../includes/csrf.php";
require_once "../config/database.php";
require_once "../includes/header.php";

// Get all notes

$sql = "SELECT
            blogpost.id,
            blogpost.title,
            blogpost.created_at,
            user.username,
            category.name AS category_name
        FROM blogpost
        JOIN user
            ON blogpost.user_id = user.id
        JOIN category
            ON blogpost.category_id = category.id
        ORDER BY blogpost.created_at DESC";

$result = mysqli_query($conn, $sql);

?>

<main class="admin-page">

    <div class="admin-container">

        <div class="admin-header">

            <h1>Manage Notes</h1>

            <p>
                View and moderate all notes published on NoteNest.
            </p>

        </div>


        <div class="admin-table-container">

            <table class="admin-table">

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Published</th>
                        <th>Action</th>

                    </tr>

                </thead>


                <tbody>

                    <?php if (mysqli_num_rows($result) > 0): ?>

                        <?php while ($blog = mysqli_fetch_assoc($result)): ?>

                            <tr>

                                <td>
                                    <?php echo $blog["id"]; ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $blog["title"]
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $blog["username"]
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $blog["category_name"]
                                    );
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    echo htmlspecialchars(
                                        $blog["created_at"]
                                    );
                                    ?>
                                </td>

                                <td>

                                    <a
                                        href="../blogs/view.php?id=<?php echo $blog["id"]; ?>"
                                    >
                                        View
                                    </a>

                                  <form
    method="POST"
    action="delete_note.php"
    onsubmit="return confirm('Are you sure you want to delete this note?');"
    style="display: inline;"
>

    <input
        type="hidden"
        name="id"
        value="<?php echo $blog["id"]; ?>"
    >

    <input
        type="hidden"
        name="csrf_token"
        value="<?php echo htmlspecialchars(generateCsrfToken()); ?>"
    >

    <button type="submit">
        Delete
    </button>

</form>

                                </td>

                            </tr>

                        <?php endwhile; ?>

                    <?php else: ?>

                        <tr>

                            <td colspan="6">
                                No notes found.
                            </td>

                        </tr>

                    <?php endif; ?>

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