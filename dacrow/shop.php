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
        if (isset($_POST['add-to-wishlist'])) {
    $product_id=$_POST['product_id'];

    $varify_wishlist = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id = ? AND product_id = ?");
    $varify_wishlist->execute([$user_id,$product_id]);

    $cart_num = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ? AND product_id = ?");
    $cart_num->execute([$user_id,$product_id]);
    if ($user_id) {
    if($varify_wishlist->rowCount()>0){
        $warning_msg[]='product already exist in your wishlist';
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
        //delete from cart
        if (isset($_POST['delete-item'])) {
            $cart_id = $_POST['cart_id'];
            $cart_id = filter_var($cart_id,FILTER_SANITIZE_STRING);

            $varify_delete_items=$conn->prepare("SELECT * FROM `cart` WHERE id=? ");
            $varify_delete_items->execute([$cart_id]);
            
            if($varify_delete_items->rowCount()>0){
                $delete_cart_id = $conn ->prepare("DELETE FROM `cart` WHERE id=?");
                $delete_cart_id->execute([$cart_id]);
                $success_msg[]="cart item delete successfully";
            }else{
                $warning_msg[]="cart item already deleted";
            }
        }
        //clear cart
        if (isset($_POST['empty_cart'])) {
            $varify_empty_item=$conn->prepare("SELECT * FROM `cart` WHERE user_id=? ");
            $varify_empty_item->execute([$user_id]);
            
            if($varify_empty_item->rowCount() > 0){
                $delete_cart_id = $conn ->prepare("DELETE FROM `cart` WHERE user_id=?");
                $delete_cart_id->execute([$user_id]);
                $success_msg[]="empty successfully";
            }else{
                $warning_msg[]="cart item already deleted";
            }
        }

        //update qty
        if (isset($_POST['update_cart'])) {
            $cart_id = $_POST['cart_id'];
            $cart_id = filter_var($cart_id,FILTER_SANITIZE_STRING);
            $qty=$_POST['qty'];
            $qty = filter_var($qty,FILTER_SANITIZE_STRING);

            $update_qty = $conn->prepare("UPDATE `cart` SET qty = ? WHERE id=?");
            $update_qty->execute([$qty , $cart_id]);

            $sccess_msg[]='cart quantity updated successfully';
        }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dacrow - your cart shopping</title>
    <link rel="icon" href="imgs/logo/ico2.ico" type="image/x-icon">
    <style>
        <?php include "style.css"?>
    </style>
</head>
<body>
    <?php include "components/header.php"?>

    <section class="user-choices">
        <div class="choice-container">
            <h1>Your cart</h1>
            <a onclick="goBack()"><span class="back"><ion-icon name="arrow-back-outline"></ion-icon>Back</span></a>
            <div class="boxes-container">
                <?php
                    $grand_total = 0;
                    $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id=?");
                    $select_cart->execute([$user_id]);
                    if ($select_cart->rowCount()>0) {
                        while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
                            $select_products = $conn->prepare("SELECT * FROM `products`WHERE id=?");
                            $select_products->execute([$fetch_cart["product_id"]]);
                            if($select_products->rowCount()>0){
                                $fetch_products = $select_products->fetch(PDO::FETCH_ASSOC);
                ?>
                <form action="" method="post" class="products-box">
                    <input type="hidden" name="cart_id" value="<?=$fetch_cart['id'];?>">
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
                                <button type="submit" name="delete-item" onclick="return confirm('are you sure ?')"><ion-icon name="trash"></ion-icon></button>
                                <button type="submit" name="add-to-wishlist" ><ion-icon name="heart"></ion-icon></button>
                                <a href="view_products?pid=<?php echo $fetch_products["id"];?>"><ion-icon name="eye"></ion-icon></a>
                            </div>
                            <input type="hidden" name="product_id" value="<?=$fetch_products["id"];?>">
                            <div class="qty-container">
                            <input type="number" value="<?=$fetch_cart["qty"]?>" min="1" max="5" maxlength="1" name="qty" class="qty" required>
                            <button type="submit" name="update_cart" class="modify-qty"><ion-icon name="create-outline"></ion-icon></button>
                            </div>
                        </div>
                        <div class="products-box-footer">
                            <h5 class="marque-products"><?=$fetch_products['marque'];?></h5>
                            <p class="price-products">Total Price : <span style="color:var(--mainorange);font-family:var(--mainfont)"><?=$sub_total=($fetch_cart['price']*$fetch_cart['qty']);?>$</span></p>
                        </div>
                </form>
                <?php
                $grand_total+=$sub_total;
                            }else{
                                echo '<p class="empty">product was not found</p>';
                            }
                        }
                    }else{
                        echo '<p class="empty">no products added yet</p>';
                    }
                ?>
            </div>
        </div>
        <?php
            if($grand_total != 0){
        ?>
        <div class="cart-total">
            <p>Total amount payable : <span><?=$grand_total;?>$</span></p>
            <div class="cart-button">
                <form action="" method="post">
                    <button type="submit" name="empty_cart" class="btn" onclick="return confirm('your cart will be empty , are your sure?!!')">clear cart</button>
                </form>
                <a href="checkout" class="checkout-btn">proceed to checkout</a>
            </div>
        </div>
        <?php } ?>
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
