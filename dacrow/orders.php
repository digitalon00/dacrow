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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dacrow - your orders</title>
    <link rel="icon" href="imgs/logo/ico2.ico" type="image/x-icon">
    <style>
        <?php include "style.css"?>
    </style>
</head>
<body>
    <?php include "components/header.php"?>

    <section class="orders" id="orders">
            <div class="orders-title">
            <h1>My Orders</h1>
            </div>
            <a onclick="goBack()"><span class="back"><ion-icon name="arrow-back-outline"></ion-icon>Back</span></a>
            <div class="orders-products">
                <?php
                $select_orders = $conn->prepare("SELECT *  FROM `orders` WHERE user_id = ? ORDER BY date DESC");
                $select_orders->execute([$user_id]);
                if($select_orders->rowCount()>0){
                    while($fetch_order = $select_orders->fetch(PDO::FETCH_ASSOC)){
                        $select_products=$conn->prepare("SELECT * FROM `products` WHERE id=?");
                        $select_products->execute([$fetch_order['product_id']]);
                        if($select_products->rowCount()>0){
                            while($fetch_product=$select_products->fetch(PDO::FETCH_ASSOC)){
                ?>
                    <div class="orders-box" <?php if($fetch_order['status']=='canceled'){echo 'style="border:2px solid red";';}?>>
                            <a><p class="date"><span><ion-icon name="calendar"></ion-icon><span><?=date('Y-m-d',strtotime($fetch_order['date']))?></span></span></p></a>    
                            <div class="div-img">
                            <img src="imgs/products/<?= $fetch_product['image'];?>" alt="">
                            </div>
                            <div class="orders-box-container">
                                <h3><?= $fetch_product['name'];?></h3>
                                <p>Price : <?= $fetch_order['price'];?>$ x <?=$fetch_order["qty"] ?></p>
                                <p class="status" style="font-family:var(--mainfont);color:<?php 
                                if($fetch_order['status']=='accepted'){echo 'rgb(110, 221, 240)';}
                                elseif($fetch_order['status']=='delivered'){echo 'rgb(29, 184, 29)';}
                                elseif($fetch_order['status']=='canceled'){echo 'red';}
                                else{echo 'rgb(247, 116, 0)';}?>"><?=$fetch_order["status"]?>
                                </p>
                            </div>
                            <a href="view_order?get_id=<?=$fetch_order['id'];?>">view details</a>
                    </div>
                <?php
                            }
                        }
                    }
                }else {
                    echo '<p class="empty">no order takes placed yet!</p>';
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
