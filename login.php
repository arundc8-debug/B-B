<!DOCTYPE HTML>
<html>
<head>
    <title>Login</title>
</head>
<body>

<?php
include "checksession.php";

if (isset($_POST['logout'])) {
    logout();
}

if (isset($_POST['login']) and !empty($_POST['login']) and ($_POST['login'] == 'Login')) {
    include "config.php";
    $DBC = mysqli_connect("127.0.0.1", DBUSER, DBPASSWORD, DBDATABASE);

    if (mysqli_connect_errno()) {
        echo "Error: Unable to connect to MySQL. " . mysqli_connect_error();
        exit;
    }

    $error = 0;
    $msg = 'Error: ';

    if (isset($_POST['username']) and !empty($_POST['username']) and is_string($_POST['username'])) {
        $un = htmlspecialchars(stripslashes(trim($_POST['username'])));
        $username = (strlen($un) > 100) ? substr($un, 1, 100) : $un;
    } else {
        $error++;
        $msg .= 'Invalid username ';
        $username = '';
    }

    $password = trim($_POST['password']);

    if ($error == 0) {
        $query = "SELECT customerID,password FROM customer 
                  WHERE email = '$username' AND password = '$password'";

        $result = mysqli_query($DBC, $query);

        if (mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            mysqli_free_result($result);
            mysqli_close($DBC);

            if ($password === $row['password']) {
                login($row['customerID'], $username);
            }
        }

        echo "<h6>Login fail</h6>" . PHP_EOL;
    } else {
        echo "<h6>$msg</h6>" . PHP_EOL;
    }
}
?>

<h1>Login</h1>

<h2>
    <a href="registercustomer.php">[Create new customer]</a>
    <a href="index.php">[Return to main page]</a>
</h2>

<form method="POST" action="login.php">
    <p>
        <label for="username">Email:</label>
        <input type="text" id="username" name="username" maxlength="100" autocomplete="off">
    </p>

    <p>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" maxlength="40" autocomplete="off">
    </p>

    <input type="submit" name="login" value="Login">
    <input type="submit" name="logout" value="Logout">
</form>

</body>
</html>