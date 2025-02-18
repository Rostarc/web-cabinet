<?php
session_start();

if ($_POST['username'] == 'admin' && $_POST['password'] == 'password') {
    $_SESSION['logged_in'] = true;
    header('Location: index.php');
} else {
    echo "Неверный логин или пароль";
}
?>
