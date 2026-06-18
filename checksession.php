<?php
session_start();

function checkUser() {
    if (isset($_SESSION['loggedin']) and $_SESSION['loggedin'] == 1) {
        return TRUE;
    } else {
        header('Location: login.php', true, 303);
        exit();
    }
}

function loginStatus() {
    if (isset($_SESSION['loggedin']) and $_SESSION['loggedin'] == 1) {
        echo "<h6>Logged in as " . $_SESSION['username'] . "</h6>";
    } else {
        echo "<h6>Logged out</h6>";
    }
}

function login($id, $username) {
    $_SESSION['loggedin'] = 1;
    $_SESSION['userid'] = $id;
    $_SESSION['username'] = $username;

    header('Location: bookings.php', true, 303);
}

function logout() {
    $_SESSION['loggedin'] = 0;
    $_SESSION['userid'] = -1;
    $_SESSION['username'] = '';

    header('Location: login.php', true, 303);
}
?>