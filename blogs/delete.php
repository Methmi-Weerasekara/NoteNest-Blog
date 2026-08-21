<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";

if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    die("Invalid note.");
}

$blog_id = (int) $_GET["id"];
$user_id = $_SESSION["user_id"];

$sql = "DELETE FROM blogpost
        WHERE id = ? AND user_id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $blog_id,
    $user_id
);

mysqli_stmt_execute($stmt);

if (mysqli_stmt_affected_rows($stmt) > 0) {
    header("Location: ../index.php");
    exit();
}

die("You are not authorized to delete this note.");

?>