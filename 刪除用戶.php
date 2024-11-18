<?php
include 'db.php';
$id = $_GET['id'];
$sql = "DELETE FROM user WHERE id=$id";
if ($link->query($sql) === TRUE) {
    header("Location: c_user.php");
} else {
    echo "Error deleting record: " . $link->error;
}
?>