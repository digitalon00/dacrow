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
        //delete from wishlist
        if (isset($_POST['delete-item'])) {
            $wishlist_id = $_POST['wishlist_id'];
            $wishlist_id = filter_var($wishlist_id,FILTER_SANITIZE_STRING);

            $varify_delete_items=$conn->prepare("SELECT * FROM `wishlist` WHERE id=? ");
            $varify_delete_items->execute([$wishlist_id]);
            
            if($varify_delete_items->rowCount()>0){
                $delete_wishlist_id = $conn ->prepare("DELETE FROM `wishlist` WHERE id=?");
                $delete_wishlist_id->execute([$wishlist_id]);
                $success_msg[]="wishlist item delete successfully";
            }else{
                $warning_msg[]="wishlist item already deleted";
            }
        } 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dacrow - your wishlist</title>
    <link rel="icon" href="imgs/logo/ico2.ico" type="image/x-icon">
    <style>
        <?php include "style.css"?>
    </style>
</head>
<body>
    <?php include "components/header.php"?>

    <section class="user-choices">
        <div class="choice-container">
            <h1>Your Wishlist</h1>
            <a onclick="goBack()"><span class="back"><ion-icon name="arrow-back-outline"></ion-icon>Back</span></a>
            <div class="boxes-container">
                <?php
                    $grand_total = 0;
                    $select_wishlist = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id=?");
                    $select_wishlist->execute([$user_id]);
                    if ($select_wishlist->rowCount()>0) {
                        while ($fetch_wishlist = $select_wishlist->fetch(PDO::FETCH_ASSOC)) {
                            $select_products = $conn->prepare("SELECT * FROM `products`WHERE id=?");
                            $select_products->execute([$fetch_wishlist["product_id"]]);
                            if($select_products->rowCount()>0){
                                $fetch_products = $select_products->fetch(PDO::FETCH_ASSOC);
                ?>
                <form action="" method="post" class="products-box">
                    <input type="hidden" name="wishlist_id" value="<?=$fetch_wishlist['id'];?>">
                        <div class="products-box-header">
                            <h3 class="name-products"><?=$fetch_products['name'];?></h3>
                            <div class="enstock-products">
                                <?php
                                    if ($fetch_products['enstock']===1) {
                                ?>
                                <ion-icon name="checkmark" style="color:rgb(2,200,2)"></ion-icon><p>in stock</p>
                                <?php
                                    }else {
                                ?>
                                <ion-icon name="close" style="color:rgb(200,2,2)"></ion-icon><p>not in stock</p>
                                <?php
                                    }
                                ?>
                            </div>
                        </div>
                        <div class="div-img" style="width:auto;height:470px; display:flex; align-items:center;text-align:center"><img src="imgs/products/<?=$fetch_products['image'];?>" style="width:300px;height:auto;postion:relative;margin:auto"  alt=""></div>
                        <div class="products-box-middle">
                            <div class="button">
                                <button type="submit" name="add-to-cart"><ion-icon name="cart"></ion-icon></button>
                                <button type="submit" name="delete-item" onclick="return confirm('are you sure ?')"><ion-icon name="heart-dislike"></ion-icon></button>
                                <a href="view_products?pid=<?php echo $fetch_products["id"];?>"><ion-icon name="eye"></ion-icon></a>
                            </div>
                            <input type="hidden" name="product_id" value="<?=$fetch_products["id"];?>">
                            <input type="number" value="1" min="1" max="5" maxlength="1" name="qty" class="qty" required>
                        </div>
                        <div class="products-box-footer">
                            <h5 class="marque-products"><?=$fetch_products['marque'];?></h5>
                            <p class="price-products">Price : <?=$fetch_products['price'];?>$</p>
                        </div>
                </form>
                <?php
                $grand_total+=$fetch_wishlist["price"];
                            }
                        }
                    }else{
                        echo '<p class="empty">no products added yet</p>';
                    }
                ?>
            </div>
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
