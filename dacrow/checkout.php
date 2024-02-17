<?php
include "components/connection.php";
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
}

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login");
    exit();
}
    //add item to wishlist
        if (isset($_POST['add-to-wishlist'])) {
            $product_id=$_POST['product_id'];
            
                    $varify_wishlist = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id = ? AND product_id = ?");
                    $varify_wishlist->execute([$user_id,$product_id]);
            
                    $cart_num = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ? AND product_id = ?");
                    $cart_num->execute([$user_id,$product_id]);
                    if ($user_id) {
                        if($varify_wishlist->rowCount()>0){
                            $warning_msg[]='product already exist in your wishlist';
                        }else if($cart_num->rowCount()>0){
                            $warning_msg[]='product already exist in your cart';
                        }else{
                            $select_price = $conn->prepare("SELECT * FROM `products` WHERE id=? LIMIT 1");
                            $select_price->execute([$product_id]);
                            $fetch_price = $select_price->fetch(PDO::FETCH_ASSOC);
            
                            $insert_wishlist = $conn->prepare("INSERT INTO `wishlist` (user_id , product_id ,price) VALUES (?,?,?)");
                            $insert_wishlist->execute([$user_id, $product_id, $fetch_price["price"] ]);
                            $success_msg[]='product added to wishlist successfully';
                        }
                    }else{
                        $info_msg[]='your are not a member , LOGIN';
                    }
            }

        //add item to cart
        if (isset($_POST['add-to-cart'])) {
            $product_id=$_POST['product_id'];
            $select_stock = $conn->prepare("SELECT * FROM `products` WHERE id=?");
            $select_stock->execute([$product_id]);
            $fetch_stock = $select_stock->fetch(PDO::FETCH_ASSOC);
            if ($fetch_stock['enstock']===1) {
                $qty= $_POST['qty'];
                $qty = filter_var($qty,FILTER_SANITIZE_STRING);
    
                $varify_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ? AND product_id = ?");
                $varify_cart->execute([$user_id,$product_id]);
    
                $max_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id=?");
                $max_cart_items->execute([$user_id]);
    
                if ($user_id) {
                    if($varify_cart->rowCount()>0){
                        $warning_msg[]='product already exist in your cart';
                    }else if($max_cart_items->rowCount()>20){
                        $warning_msg[]='cart is full';
                    }else{
                        $select_price = $conn->prepare("SELECT * FROM `products` WHERE id=? LIMIT 1");
                        $select_price->execute([$product_id]);
                        $fetch_price = $select_price->fetch(PDO::FETCH_ASSOC);
        
                        $insert_cart = $conn->prepare("INSERT INTO `cart` (user_id , product_id , price , qty) VALUES (?,?,?,?)");
                        $insert_cart->execute([$user_id, $product_id, $fetch_price["price"], $qty ]);
                        $success_msg[]='product added to cart successfully';
                    }
                }else{
                    $info_msg[]='your are not a member , LOGIN';
                }
            }else{
                $info_msg[]='product rupture from stock';
            }
        }

//place order
if (isset($_POST['place_order'])) {

    $firstname = $_POST['firstname'];
    $firstname = filter_var($firstname,FILTER_SANITIZE_STRING);

    $lastname = $_POST['lastname'];
    $lastname = filter_var($lastname,FILTER_SANITIZE_STRING);
    
    $phone = $_POST['phone'];
    $phone = filter_var($phone,FILTER_SANITIZE_STRING);

    $email = $_POST['email'];
    $email = filter_var($email,FILTER_SANITIZE_STRING);

    $payement = $_POST['payement'];
    $payement = filter_var($payement,FILTER_SANITIZE_STRING);

    $adresse_type = $_POST['adresse_type'];
    $adresse_type = filter_var($adresse_type,FILTER_SANITIZE_STRING);

    $country = $_POST['country'];
    $country = filter_var($country,FILTER_SANITIZE_STRING);

    $city = $_POST['city'];
    $city = filter_var($city,FILTER_SANITIZE_STRING);

    $adresse = $_POST['adresse'];
    $adresse = filter_var($adresse,FILTER_SANITIZE_STRING);

    $varify_cart=$conn->prepare("SELECT * FROM `cart` WHERE user_id=?");
    $varify_cart->execute([$user_id]);

if (isset($_GET['get_id'])) {
    $get_product = $conn->prepare("SELECT * FROM `products` WHERE id=?  LIMIT 1");
    $get_product->execute([$_GET['get_id']]);
    if ($get_product->rowCount() > 0) {
        $fetch_p = $get_product->fetch(PDO::FETCH_ASSOC);
        $insert_order = $conn->prepare("INSERT INTO `orders` (user_id, firstname, lastname, phone, email, payement, adresse_type, country, city, adresse, product_id, price, qty) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
        $insert_order->execute([$user_id, $firstname, $lastname, $phone, $email, $payement, $adresse_type, $country, $city, $adresse, $fetch_p["id"], $fetch_p["price"], 1]);

        header("Location: orders");
        exit();
    } else {
        $warning_msg[] = 'Product not found';
    }
} elseif ($varify_cart->rowCount() > 0) {
    while ($f_cart = $varify_cart->fetch(PDO::FETCH_ASSOC)) {
        $get_product = $conn->prepare("SELECT * FROM `products` WHERE id=?  LIMIT 1");
        $get_product->execute([$f_cart['product_id']]);
        if ($get_product->rowCount() > 0) {
            $fetch_p = $get_product->fetch(PDO::FETCH_ASSOC);
            $insert_order = $conn->prepare("INSERT INTO `orders` (user_id, firstname, lastname, phone, email, payement, adresse_type, country, city, adresse, product_id, price, qty) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
            $insert_order->execute([$user_id, $firstname, $lastname, $phone, $email, $payement, $adresse_type, $country, $city, $adresse, $fetch_p['id'], $f_cart["price"], $f_cart['qty']]);
        } else {
            $warning_msg[] = 'Product not found';
        }
    }

    if ($insert_order) {
        $delete_cart_id = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
        $delete_cart_id->execute([$user_id]);
        header("Location: orders");
        exit();
    }
} else {
    $warning_msg[] = "Something went wrong";
}
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dacrow - checkout</title>
    <link rel="icon" href="imgs/logo/ico2.ico" type="image/x-icon">
    <style>
        <?php include "style.css"?>
    </style>
</head>
<body>
    <?php include "components/header.php"?>
    <section class="checkout">
            <div class="checkout-title">
                <img src="imgs/ico1.png" alt="">
                <h1>Checkout summary</h1>
            </div>
            <div class="summary">
                    <h3>my bag</h3>
                    <div class="box-container">
                        <?php
                        $grand_total=0;
                        if (isset($_GET['get_id'])) {
                            $select_get = $conn->prepare("SELECT * FROM `products` WHERE id=?");
                            $select_get->execute([$_GET['get_id']]);
                            while($fetch_get = $select_get->fetch(PDO::FETCH_ASSOC)){
                                $sub_total = $fetch_get['price'];
                                $grand_total+=$sub_total;
                                ?>
                                <div class="summary-box">
                                    <h1><?=$fetch_get['name'];?></h1>
                                    <img src="imgs/products/<?=$fetch_get['image'] ?>" alt="">
                                    <p>price : <?=$fetch_get['price'];?>$</p>
                                </div>
                                <?php
                            }
                        }else {
                            $select_cart =  $conn->prepare("SELECT *  FROM `cart` WHERE user_id=?");
                            $select_cart->execute([$user_id]);
                            if ($select_cart->rowCount()>0) {
                                while ($fetch_cart=$select_cart->fetch(PDO::FETCH_ASSOC)) {
                                    $select_products=$conn->prepare("SELECT * FROM `products` WHERE id=?");
                                    $select_products->execute([$fetch_cart['product_id']]);
                                    $fetch_product = $select_products->fetch(PDO::FETCH_ASSOC);
                                    $sub_total=($fetch_cart['qty']*$fetch_product['price']);
                                    $grand_total +=$sub_total;

                                    ?>

                                    <div class="summary-box">
                                    <h1><?=$fetch_product['name'];?></h1>
                                    <img src="imgs/products/<?=$fetch_product['image'] ?>" alt="">
                                    <p>price : <?=$fetch_product['price']?>$ x <?=$fetch_cart['qty'];?></p>

                                </div>

                                    <?php
                                }
                            }else{
                                echo '<p class="empty">your cart is empty</p>';
                            }
                        }
                        ?>
                    </div>
                </div>
                <div class="cart-total">
                        <p>Total amount payable : <span><?=$grand_total;?>$</span></p>
                    </div>
            <div class="checkout-container">
                <h3>Billing details</h3>
                <?php 
                $select_products =$conn -> prepare("SELECT * FROM `users` WHERE id=?");
                $select_products->execute([$user_id]);
                $fetch_products=$select_products->fetch(PDO::FETCH_ASSOC);
                ?>
                <form action="" method="post">
                    <div class="checkout-box-container">
                    <div class="double-checkout-box">
                    
                        <div class="checkout-box">
                            <span class="span-icon"><ion-icon name="person"></ion-icon></span>
                            <input type="text" id="firstname" name="firstname" class="checkout-firstname" required value="<?=$fetch_products["firstname"] ?>">
                            <label>first name</label>
                        </div>

                        <div class="checkout-box">
                            <span class="span-icon"><ion-icon name="person"></ion-icon></span>
                            <input type="text" id="lastname" name="lastname" class="checkout-lastname" required value="<?=$fetch_products["lastname"] ?>">
                            <label>last name</label>
                        </div>
                    </div>

                        <div class="checkout-box">
                            <span class="span-icon"><ion-icon name="call"></ion-icon></span>
                            <input type="text" id="phone" name="phone" class="checkout-phone" required value="<?=$fetch_products["phone"] ?>">
                            <label>phone</label>
                        </div>

                        <div class="checkout-box">
                            <span class="span-icon"><ion-icon name="mail"></ion-icon></span>
                            <input type="text" id="email" name="email" class="checkout-email" required value="<?=$fetch_products["email"] ?>">
                            <label>email</label>
                        </div>

                        <div class="checkout-box">
                            <span class="span-icon"><ion-icon name="card"></ion-icon></span>
                            <select name="payement" id="payement" required>
                                <option value="cash" selected>cash on delivery</option>
                                <option value="credit-debit">Credit or Debit</option>
                            </select>
                        </div>

                        <div class="double-checkout-box">
                            <div class="checkout-box">
                                <span class="span-icon"><ion-icon name="location"></ion-icon></span>
                                <input type="text" id="country" name="country" class="checkout-country" required value="<?=$fetch_products["country"] ?>">
                                <label>country</label>
                            </div>

                            <div class="checkout-box">
                                <span class="span-icon"><ion-icon name="compass"></ion-icon></span>
                                <input type="text" id="city" name="city" class="checkout-city" required  value="<?=$fetch_products["city"] ?>">
                                <label>city</label>
                            </div>
                        </div>
                        
                        <div class="checkout-box">
                            <span class="span-icon"><ion-icon name="trail-sign"></ion-icon></span>
                            <select name="adresse_type" id="adresse-type" required>
                                <option value="Office" selected>office</option>
                                <option value="Home">Home</option>
                            </select>
                        </div>

                        <div class="checkout-box">
                            <span class="span-icon"><ion-icon name="home"></ion-icon></span>
                            <textarea id="adresse" name="adresse" class="checkout-adresse" required><?=$fetch_products["adresse"] ?></textarea>
                            <label>adresse</label>
                        </div>
                        <button type="submit" name="place_order">Place order<ion-icon name="basket"></ion-icon></button>
                    </div>
                </form>
            </div>
    </section>
    <span class="scrolltotop"><ion-icon name="arrow-up"></ion-icon></span>
    <?php include "components/footer.php" ?>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script><?php include "script.js"?></script>
    <?php include "components/alert.php"?> 
</body>
</html>