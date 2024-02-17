<?php 
    include 'components/connection.php';
    session_start();

    if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    }else{
    $user_id='';
    }

    //register user
    if (isset($_POST['submit'])) {

    $firstname = $_POST['firstname'];
    $firstname = filter_var($firstname,FILTER_SANITIZE_STRING);

    $lastname = $_POST['lastname'];
    $lastname = filter_var($lastname,FILTER_SANITIZE_STRING);

    $username= $firstname . $lastname;
    
    $gender = $_POST['gender'];
    $gender = filter_var($gender,FILTER_SANITIZE_STRING);

    $phone = $_POST['phone'];
    $phone = filter_var($phone,FILTER_SANITIZE_STRING);

    $email = $_POST['email'];
    $email = filter_var($email,FILTER_SANITIZE_STRING);

    $pass = $_POST['pass'];
    $pass = filter_var($pass,FILTER_SANITIZE_STRING);

    $cpass = $_POST['cpass'];
    $cpass = filter_var($cpass,FILTER_SANITIZE_STRING);

    $country = $_POST['country'];
    $country = filter_var($country,FILTER_SANITIZE_STRING);

    $city = $_POST['city'];
    $city = filter_var($city,FILTER_SANITIZE_STRING);

    $adresse = $_POST['adresse'];
    $adresse = filter_var($adresse,FILTER_SANITIZE_STRING);

    $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
    $select_user->execute([$email]);
    $row = $select_user->fetch(PDO::FETCH_ASSOC);

    if ($select_user->rowCount()>0) {
        $message[]='email already exist';
        echo 'email already exist';
    }else{
        if ($pass != $cpass) {
            $warning_msg[]='confirm your password';
            echo 'confirm your password correctly';
        }else{
            $insert_user = $conn->prepare("INSERT INTO `users` (username,firstname,lastname,gender,phone,email,pass,country,city,adresse)VALUES (?,?,?,?,?,?,?,?,?,?)");
            $insert_user->execute([$username,$firstname,$lastname,$gender,$phone,$email,$pass,$country,$city,$adresse]);

            $real_id = $conn->lastInsertId();

            $constructed_username = $firstname . $lastname . $real_id;

            $update_username = $conn->prepare("UPDATE `users` SET username = ? WHERE id = ?");
            $update_username->execute([$constructed_username, $real_id]);

            $select_user = $conn->prepare("SELECT * FROM `users` WHERE id=?");
            $select_user->execute([$real_id]);
            $row = $select_user->fetch(PDO::FETCH_ASSOC);

            $select_user=$conn->prepare("SELECT * FROM `users` WHERE email=? AND pass=?");
            $select_user->execute([$email,$pass]);
            $row = $select_user->fetch(PDO::FETCH_ASSOC);
            if($select_user->rowCount()>0){
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['user_username'] = $constructed_username;
                $_SESSION['user_firstname'] = $row['firstname'];
                $_SESSION['user_lastname'] = $row['lastname'];
                $_SESSION['user_gender'] = $row['gender'];
                $_SESSION['user_phone'] = $row['phone'];
                $_SESSION['user_email'] = $row['email'];
                $_SESSION['user_country'] = $row['country'];
                $_SESSION['user_city'] = $row['city'];
                $_SESSION['user_adresse'] = $row['adresse'];

                header("Location: index");
    exit();
            }
        }
    }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dacrow - Join us</title>
    <link rel="icon" href="imgs/logo/ico2.ico" type="image/x-icon">
    <style>
        <?php include "style.css" ?>
    </style>
</head>
<body>
    <section class="register-section">
        <div class="register">
            <img src="imgs/bg/register-bg.jpg" alt="">
            <div class="register-content">
                <a href="index" class="close"><ion-icon name="close"></ion-icon></a>
                <div class="title">
                    <h3>Register</h3>
                    <p>Join us and get members reductions</p>
                </div>
                <form action="" method="post">
                    <div class="register-double-box">
                        <div class="register-box">
                            <span class="span-icon"><ion-icon name="person"></ion-icon></span>
                            <input type="text" id="register-firstname" name="firstname" required>
                            <label>First name</label>
                        </div>
                        <div class="register-box">
                            <span class="span-icon"><ion-icon name="person"></ion-icon></span>
                            <input type="text" id="register-lastname" name="lastname" required>
                            <label>Last name</label>
                        </div>
                    </div>
                    <div class="checkout-box">
                            <span class="span-icon"><ion-icon name="transgender"></ion-icon></span>
                            <select name="gender" id="gender" required>
                                <option value="none" selected>not specified</option>
                                <option value="male">male</option>
                                <option value="female">female</option>
                            </select>
                        </div>
                    <div class="register-box">
                    <span class="span-icon"><ion-icon name="call"></ion-icon></span>
                    <input type="tel" id="register-phone" name="phone"  required
                    oninput="this.value = this.value.replace(/\s/g, '')">
                    <label>Phone</label>
                    </div>
                    <div class="register-box">
                    <span class="span-icon"><ion-icon name="mail"></ion-icon></span>
                    <input type="email" id="register-mail" name="email" required
                    oninput="this.value = this.value.replace(/\s/g, '')">
                    <label>Email</label>
                    </div>
                    <div class="register-box">
                        <span class="span-icon"><ion-icon name="eye"></ion-icon></span>
                    <input type="password" id="register-pass" name="pass" required
                    oninput="this.value = this.value.replace(/\s/g, '')">
                    <label>Password</label>
                    </div>
                    <div class="register-box">
                    <span class="span-icon"><ion-icon name="eye"></ion-icon></span>
                    <input type="password" id="register-cpass" name="cpass" required
                    oninput="this.value = this.value.replace(/\s/g, '')">
                    <label>confirm Password</label>
                    </div>
                    <div class="register-double-box">
                    <div class="register-box">
                        <span class="span-icon"><ion-icon name="location"></ion-icon></span>
                        <input type="text" id="register-country" name="country" required>
                        <label>Country</label>
                    </div>
                    <div class="register-box">
                        <span class="span-icon"><ion-icon name="compass"></ion-icon></span>
                        <input type="text" id="register-city" name="city"  required>
                        <label>City</label>
                    </div>
                    </div>
                    <div class="register-box">
                    <span class="span-icon"><ion-icon name="home"></ion-icon></span>
                    <textarea id="register-textarea" name="adresse" required></textarea>
                    <label>Adresse</label>
                    </div>
                    <div class="register-check-box">
                        <input type="checkbox" name="conditions" required>
                        <label class="conditions">En cliquant sur <span>Register</span>  je confirme avoir lu et accepté Les <a href="">Conditions Générales</a> ainsi que les <a href="">Informations relatives à la protection des données</a> associée sont applicables à la participation à DACROW EXPERIENCE</label>
                    </div>
                    <button type="submit" name="submit" class="register-form-btn">Register</button>
                </form>
                <div class="loginlinks">
                    <span>you already have an account? <a href="login">Login</a></span>
                </div>
            </div>
        </div>
    </section>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script><?php include "script.js"?></script>
    <?php include "components/alert.php"?> 
</body>
</html>