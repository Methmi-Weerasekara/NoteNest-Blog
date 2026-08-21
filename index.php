<?php

session_start();

require_once "config/database.php";
require_once "includes/header.php";

$search = isset($_GET["search"]) ? trim($_GET["search"]) : "";
$category_id = isset($_GET["category"]) && is_numeric($_GET["category"])
    ? (int) $_GET["category"]
    : null;

$sql = "SELECT 
            blogpost.id,
            blogpost.title,
            blogpost.content,
            blogpost.created_at,
            user.username,
            category.name AS category_name
        FROM blogpost
        JOIN user ON blogpost.user_id = user.id
        JOIN category ON blogpost.category_id = category.id
        WHERE 1=1";

$params = [];
$types = "";

if ($search !== "") {

    $sql .= " AND (
        blogpost.title LIKE ?
        OR blogpost.content LIKE ?
        OR user.username LIKE ?
    )";

    $searchTerm = "%" . $search . "%";

    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;

    $types .= "sss";
}

if ($category_id !== null) {

    $sql .= " AND blogpost.category_id = ?";

    $params[] = $category_id;
    $types .= "i";
}

$sql .= " ORDER BY blogpost.created_at DESC";

$stmt = mysqli_prepare($conn, $sql);

if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
}

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$categoryResult = mysqli_query(
    $conn,
    "SELECT id, name FROM category ORDER BY name"
);
?>

<main class="home-page">

    <!-- Hero -->

    <section class="home-hero">

        <div class="hero-content">

            <span class="hero-label">
                WELCOME TO NOTENEST
            </span>

            <h1>
                Discover knowledge.<br>
                Share what you know.
            </h1>

            <p>
                Discover notes created by students and
                share your own knowledge with the community.
            </p>

        </div>

    </section>


    <!-- Search -->

    <section class="search-section">

        <form method="GET" class="search-form">

            <div class="search-input-wrapper">

                <span class="search-icon">
                    
                </span>

                <input
                    type="text"
                    name="search"
                    placeholder="Search notes..."
                    value="<?php echo htmlspecialchars($search); ?>"
                >

            </div>


            <?php if ($category_id !== null): ?>

                <input
                    type="hidden"
                    name="category"
                    value="<?php echo $category_id; ?>"
                >

            <?php endif; ?>


            <button type="submit">
                Search
            </button>

        </form>

    </section>


    <!-- Categories -->

    <section class="categories-section">

        <div class="section-heading">

            <h2>
                Explore Categories
            </h2>

        </div>


        <div class="categories">

            <a href="index.php">
                All Notes
            </a>

            <?php while ($category = mysqli_fetch_assoc($categoryResult)): ?>

                <a
                    href="index.php?category=<?php echo $category["id"]; ?>"
                >
                    <?php echo htmlspecialchars($category["name"]); ?>
                </a>

            <?php endwhile; ?>

        </div>

    </section>


    <!-- Latest Notes -->

    <section class="latest-section">

        <div class="section-heading">

            <div>

                <h2>
                    Latest Notes
                </h2>

                <p>
                    Fresh knowledge shared by the NoteNest community.
                </p>

            </div>

        </div>


        <div class="note-grid">

            <?php if (mysqli_num_rows($result) > 0): ?>

                <?php while ($blog = mysqli_fetch_assoc($result)): ?>

                    <article class="note-card">

                        <span class="category">
                            <?php echo htmlspecialchars($blog["category_name"]); ?>
                        </span>


                        <h3>
                            <?php echo htmlspecialchars($blog["title"]); ?>
                        </h3>


                        <p class="author">
                            By <?php echo htmlspecialchars($blog["username"]); ?>
                        </p>


                        <p class="date">
                            <?php echo htmlspecialchars($blog["created_at"]); ?>
                        </p>


                        <p class="preview">
                            <?php
                            echo htmlspecialchars(
                                substr($blog["content"], 0, 150)
                            );
                            ?>...
                        </p>


                        <a
                            class="read-more"
                            href="blogs/view.php?id=<?php echo $blog["id"]; ?>"
                        >
                            Read Note →
                        </a>

                    </article>

                <?php endwhile; ?>


            <?php else: ?>

                <div class="empty-notes">

                    <div class="empty-icon">
                        📚
                    </div>

                    <h3>
                        No notes found
                    </h3>

                    <p>
                        Try another search or explore a different category.
                    </p>

                </div>

            <?php endif; ?>

        </div>

    </section>

</main>
<?php require_once "includes/footer.php"; ?>