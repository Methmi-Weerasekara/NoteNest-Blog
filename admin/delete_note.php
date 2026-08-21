<?php

require_once "../includes/admin_check.php";
require_once "../includes/csrf.php";
require_once "../config/database.php";

if (
    $_SERVER["REQUEST_METHOD"] !== "POST" ||
    !isset($_POST["id"]) ||
    !is_numeric($_POST["id"])
) {
    die("Invalid request.");
}

if (
    !isset($_POST["csrf_token"]) ||
    !verifyCsrfToken($_POST["csrf_token"])
) {
    die("Invalid CSRF token.");
}

$blog_id = (int) $_POST["id"];

//$blog_id = (int) $_GET["id"];


// Delete likes belonging to this note

$likeSql = "DELETE FROM bloglike
            WHERE blog_id = ?";

$likeStmt = mysqli_prepare($conn, $likeSql);

mysqli_stmt_bind_param(
    $likeStmt,
    "i",
    $blog_id
);

mysqli_stmt_execute($likeStmt);

mysqli_stmt_close($likeStmt);


// Delete comments belonging to this note

$commentSql = "DELETE FROM comment
               WHERE blog_id = ?";

$commentStmt = mysqli_prepare($conn, $commentSql);

mysqli_stmt_bind_param(
    $commentStmt,
    "i",
    $blog_id
);

mysqli_stmt_execute($commentStmt);

mysqli_stmt_close($commentStmt);


// Delete the note

$deleteSql = "DELETE FROM blogpost
              WHERE id = ?";

$deleteStmt = mysqli_prepare(
    $conn,
    $deleteSql
);

mysqli_stmt_bind_param(
    $deleteStmt,
    "i",
    $blog_id
);

if (mysqli_stmt_execute($deleteStmt)) {

    mysqli_stmt_close($deleteStmt);

    header("Location: notes.php");
    exit();

} else {

    mysqli_stmt_close($deleteStmt);

    die("Failed to delete note.");
}