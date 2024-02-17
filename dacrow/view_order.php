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

if ($_GET['get_id']) {
    $get_id = $_GET['get_id'];
}else{
    $get_id="";
    header("Location: orders");
    exit();
}
//cancel item
if (isset($_POST['cancel'])) {
    $update_order = $conn->prepare("UPDATE `orders` SET status=? WHERE id=?");
    $update_order->execute(['canceled',$get_id]);
    header("Location: orders");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dacrow - Order details</title>
    <link rel="icon" href="imgs/logo/ico2.ico" type="image/x-icon">
    <style>
        <?php include "style.css"?>
    </style>
</head>
<body>
    <?php include "components/header.php"?>

    <section class="order-details">
        <div class="order-details-title">
                <h1>Order Details</h1>
        </div>
        <a onclick="goBack()"><span class="back"><ion-icon name="arrow-back-outline"></ion-icon>Back</span></a>
        <div class="order-details-container">
            <?php
                $grand_total=0;
                $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE id=? LIMIT 1");
                $select_orders->execute([$get_id]);
                if ($select_orders->rowCount()>0) {
                    while ($fetch_order =$select_orders->fetch(PDO::FETCH_ASSOC)) {
                        $select_product = $conn->prepare("SELECT * FROM `products` WHERE id=? LIMIT 1");
                        $select_product->execute([$fetch_order['product_id']]);
                        if($select_product->rowCount()>0){
                            while ($fetch_product = $select_product->fetch(PDO::FETCH_ASSOC)) {
                                $sub_total=($fetch_order['price']*$fetch_order['qty']);
                                $grand_total += $sub_total;
                    ?>
                        <div class="order-details-box">
                            <div class="order-box-left">
                                <img src="imgs/products/<?=$fetch_product["image"]?>" alt="">
                            </div>
                            <div class="order-box-right">
                                <p class="date-p"><ion-icon name="calendar"></ion-icon><span><?=date('Y-m-d',strtotime($fetch_order['date']))?></span></p>
                                <h1><?=$fetch_product['name'];?></h1>
                                <div class="order-price">
                                    <p >Price : <?=$fetch_product['price'];?>$ x <?=$fetch_order['qty']?></p>
                                    <p class="price">Total amount payable : <span><?= $grand_total;?>$</span></p>
                                </div>
                                <div class="order-infos">
                                    <label>Billing Infos : </label>
                                    <p><ion-icon name="person-outline"></ion-icon><span><?=$fetch_order['firstname']?> <?=$fetch_order['lastname']?></span></p>
                                    <p><ion-icon name="call-outline"></ion-icon><span><?=$fetch_order['phone']?></span></p>
                                    <p><ion-icon name="mail-outline"></ion-icon><span><?=$fetch_order['email']?></span></p>
                                    <p><ion-icon name="home-outline"></ion-icon><span><?=$fetch_order['adresse']?> , <?=$fetch_order['city']?> , <?=$fetch_order['country']?>, '<?=$fetch_order['adresse_type']?>'</span></p>
                                </div>
                                <div class="order-statut">
                                    <p class="status" style="color:<?php                                     
                                    if($fetch_order['status']=='accepted'){echo 'rgb(110, 221, 240)';}
                                    elseif($fetch_order['status']=='delivered'){echo 'rgb(29, 184, 29)';}
                                    elseif($fetch_order['status']=='canceled'){echo 'red';}
                                    else{echo 'rgb(247, 116, 0)';}?>"><?=$fetch_order["status"]?>
                                    </p>
                                    <?php if($fetch_order['status']=='canceled'){?>
                                    <a href="checkout?get_id=<?=$fetch_product['id'];?>">order again</a>
                                    <?php }else{?>
                                        <form action="" method="post">
                                            <button type="submit" name="cancel" onclick="return confirm('your order will canceled!!')">cancel order</button>
                                        </form>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php
                            }
                        }
                        else {
                            echo '<p class="empty">product not found!</p>';
                        }
                    }
                }else {
                    echo '<p class="empty">no order found!</p>';
                }
            ?>
        </div>
    </section>
    <?php include "components/footer.php" ?>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script><?php include "script.js"?></script>
    <?php include "components/alert.php"?> 
</body>
</html>
