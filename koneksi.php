<?php
$host = "localhost";
$user = "root"; // Default bawaan XAMPP
$pass = "";     // Default XAMPP emang kosong
$db   = "db_faroki";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}
?>