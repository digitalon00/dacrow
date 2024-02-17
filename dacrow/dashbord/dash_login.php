<?php 
    include '../components/connection.php';
    session_start();

    if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    }else{
    $user_id='';
    }

    //login user
    if (isset($_POST['login'])) {

    $email = $_POST['email'];
    $email = filter_var($email,FILTER_SANITIZE_STRING);

    $pass = $_POST['pass'];
    $pass = filter_var($pass,FILTER_SANITIZE_STRING);

    $select_admin = $conn->prepare("SELECT * FROM `admins` WHERE email = ? OR username = ? AND password = ?");
    $select_admin->execute([$email , $email , $pass]);
    $row = $select_admin->fetch(PDO::FETCH_ASSOC);

    if ($select_admin->rowCount()>0) {
        session_start();
        $_SESSION['logged_in'] = true;
        $_SESSION['admin_id']=$row['id'];
        $_SESSION['admin_username']=$row['username'];
        $_SESSION['admin_email']=$row['email'];
        $_SESSION['admin_role']=$row['role'];
        header('Location: dash_root');
        exit();
    }
    else{
        $warning_msg[]='incorrect username or password';
    }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dacrow - connexion to dashboard</title>
    <link rel="icon" href="imgs/logo/ico2.ico" type="image/x-icon">
    <style>
        <?php include "dash_style.css" ?>
    </style>
</head>
<body>
    <section class="dash_login">
        <div class="dash_login_container">
            <img src="imgs/bg/login-board.jpg" alt="">
            <div class="dash_login-content">
            <a href="index" class="close"><ion-icon name="close"></ion-icon></a>
                <div class="title">
                    <h3>Login</h3>
                    <p>Welcome Admin</p>
                </div>
                <form action="" method="post">
                    <div class="login-box">
                    <span class="span-icon"><ion-icon name="person"></ion-icon></span>
                    <input type="text" id="login-user-mail" name="email" required>
                    <label>Email</label>
                    </div>
                    <div class="login-box">
                        <span class="span-icon"><ion-icon name="eye"></ion-icon></span>
                    <input type="password" id="login-pass" name="pass" required>
                    <label>Password</label>
                    </div>
                    <button type="submit" name="login" class="login-form-btn">Login</button>
                </form>
            </div>
        </div>
    </section>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script><?php include "script.js"?></script>
    <?php include "../components/alert.php"?> 
</body>
</html>