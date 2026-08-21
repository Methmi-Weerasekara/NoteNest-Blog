<?php

require_once "../includes/admin_check.php";
require_once "../config/database.php";
require_once "../includes/header.php";


// Count users

$userResult = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM user"
);

$userData = mysqli_fetch_assoc($userResult);
$totalUsers = $userData["total"];


// Count notes

$noteResult = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM blogpost"
);

$noteData = mysqli_fetch_assoc($noteResult);
$totalNotes = $noteData["total"];


// Count comments

$commentResult = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM comment"
);

$commentData = mysqli_fetch_assoc($commentResult);
$totalComments = $commentData["total"];


// Count likes

$likeResult = mysqli_query(
    $conn,
    "SELECT COUNT(*) AS total FROM bloglike"
);

$likeData = mysqli_fetch_assoc($likeResult);
$totalLikes = $likeData["total"];

?>

<main class="admin-page">

    <div class="admin-container">

        <div class="admin-header">

            <h1>Admin Dashboard</h1>

            <p>
                Welcome back,
                <?php echo htmlspecialchars($_SESSION["username"]); ?>.
            </p>

        </div>


        <div class="admin-stats">

            <div class="admin-stat-card">

                <span>Users</span>

                <strong>
                    <?php echo $totalUsers; ?>
                </strong>

            </div>


            <div class="admin-stat-card">

                <span>Notes</span>

                <strong>
                    <?php echo $totalNotes; ?>
                </strong>

            </div>


            <div class="admin-stat-card">

                <span>Comments</span>

                <strong>
                    <?php echo $totalComments; ?>
                </strong>

            </div>


            <div class="admin-stat-card">

                <span>Likes</span>

                <strong>
                    <?php echo $totalLikes; ?>
                </strong>

            </div>

        </div>


        <div class="admin-actions">

            <h2>Management</h2>

            <a href="users.php">
                Manage Users
            </a>

            <a href="notes.php">
                Manage Notes
            </a>

            <a href="categories.php">
                Manage Categories
            </a>

        </div>

    </div>

</main>

<?php require_once "../includes/footer.php"; ?>