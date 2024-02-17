<?php
if (isset($_POST['contact-submit'])) {
    $username = $_POST['username'];
    $username = filter_var($username,FILTER_SANITIZE_STRING);

    $subject = $_POST['subject'];
    $subject = filter_var($subject,FILTER_SANITIZE_STRING);
    
    $message = $_POST['message'];
    $message = filter_var($message,FILTER_SANITIZE_STRING);

    $email = $_POST['email'];
    $email = filter_var($email,FILTER_SANITIZE_STRING);

    $insert_contact = $conn->prepare("INSERT INTO `contact` (username , email ,subject,message) VALUES (?,?,?,?)");
    $insert_contact->execute([$username, $email, $subject,$message ]);
    $success_msg[]='thank you for your message';
}

?>
<section class="contact" id="contact">
    <div class="contact-content">
        <div class="contact-content-title">
            <h1>Contact us</h1>
            <p>for any question or claim </p>
        </div>
            <?php 
                $select_products =$conn -> prepare("SELECT * FROM `users` WHERE id=?");
                $select_products->execute([$user_id]);
                $fetch_products=$select_products->fetch(PDO::FETCH_ASSOC);
            ?>
            <form action="" method="post" class="contact-form">
                <div class="form-box">
                    <span class="span-icon"><ion-icon name="person"></ion-icon></span>
                    <input type="text" id="username" class="contact-username" name="username" required <?php if(isset($_SESSION['user_username'])){?> value="<?=$fetch_products["username"]?>" <?php } ?>>
                    <label>Username</label>
                </div>
                <div class="form-box">
                    <span class="span-icon"><ion-icon name="mail"></ion-icon></span>
                    <input type="email" id="email" class="contact-email" required name="email" <?php if(isset($_SESSION['user_email'])){?> value="<?=$fetch_products["email"]?>" <?php } ?>>
                    <label>E-mail</label>
                </div>
                <div class="form-box">
                    <span class="span-icon"><ion-icon name="paper-plane"></ion-icon></span>
                    <input type="text" id="subject" class="contact-subject" name="subject" required>
                    <label>Subject</label>
                </div>
                <div class="form-box">
                    <span class="span-icon"><ion-icon name="chatbox-ellipses"></ion-icon></span>
                    <textarea id="texterea" class="contact-texterea" name="message" required></textarea>
                    <label>Message</label>
                </div>
                    <button type="submit" class="form-btn" name="contact-submit">Submit</button>
            </form>
    </div>
</section>