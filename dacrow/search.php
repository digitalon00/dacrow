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
    <title>Dacrow - search</title>
    <link rel="icon" href="imgs/logo/ico2.ico" type="image/x-icon">
    <style>
        <?php include "style.css"?>
    </style>
</head>
<body>
    <?php include "components/header.php"?>
    <div class="search-section">
        <a onclick="goBack()"><span class="back"><ion-icon name="arrow-back-outline"></ion-icon>Back</span></a>
        <h1 class="title">search product</h1>
        <form class="search-bar" method="post" onsubmit="submitForm(e)">
            <input type="text" placeholder="search product" name="product_name">
            <button class="search-submit" type="submit" name="search_product"><ion-icon name="search"></ion-icon></button>
    </form>
        <div class="search-boxes">
            <?php
            if (isset($_POST['search_product'])) {
                $product_name = $_POST['product_name'];
                $product_name = filter_var($product_name,FILTER_SANITIZE_STRING);
                $select_products = $conn->prepare("SELECT * FROM `products` WHERE marque LIKE ? OR name LIKE ?");
                $select_products->execute(['%' . $product_name . '%' , '%' . $product_name . '%']);
                if($select_products->rowCount()>0){
                    while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){
                        ?>
                        <a href="view_products?pid=<?php echo $fetch_products["id"];?>" class="product-link">
                            <div class="search-box">
                                <div class="search-box-left">
                                    <img src="imgs/products/<?=$fetch_products["image"]?>" alt="">
                                </div>
                                <div class="search-box-right">
                                    <h4><?=$fetch_products["marque"]?></h4>
                                    <h1><?=$fetch_products["name"]?></h1>
                                    <p>price : <?=$fetch_products["price"]?>$</p>
                                </div>
                            </div>
                        </a>
                        <?php
                    }
                }else{
                    echo '<p class="empty">no product match!</p>';
                }
            }
            ?>
        </div>
</div>    
    <?php include "components/footer.php" ?>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script><?php include "script.js"?></script>
    <?php include "components/alert.php"?> 
</body>
</html>