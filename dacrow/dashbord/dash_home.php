<?php
    include "../components/connection.php";
    session_start();
    if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    } else {
    $user_id = '';
    }

    if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        header('Location: dash_login');
        exit();
    }

    if (isset($_POST['logout'])) {
        session_destroy();
        header('Location: dash_login');
        exit();
    }

    if (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'];
        $user_id = filter_var($user_id,FILTER_SANITIZE_STRING);
                                        
        $varify_delete_items=$conn->prepare("SELECT * FROM `users` WHERE id=? ");
        $varify_delete_items->execute([$user_id]);
                                                    
        if($varify_delete_items->rowCount()>0){
            $delete_user_id = $conn ->prepare("DELETE FROM `users` WHERE id=?");
            $delete_user_id->execute([$user_id]);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dacrow - Dashbord</title>
    <link rel="icon" href="imgs/logo/ico2.ico" type="image/x-icon">
    <style><?php include "dash_style.css" ?></style>
</head>
<body>
        <section class="dashboard">
            <div class="menu">
                <div class="title">
                    <h1>menu</h1>
                    <a href="index"><img src="imgs/logo/ico2.ico" alt=""></a>
                </div>
                <div class="menu-grids">
                    <a class="menu-home" href="dash_root">Home</a>
                    <a class="menu-users" href="dash_root_users">Manage users</a>
                    <a class="menu-products" onclick="showproducts()">Manage products</a>
                        <div class="products-grids">
                            <a class="menu-products-stock" href="dash_root_stock">Manage stock</a>
                            <a class="menu-products-manage" href="dash_root_products">Manage products</a>
                            <a class="menu-add-product" href="dash_root_add_product">Add product</a>
                        </div>
                    <a class="menu-orders" href="dash_root_orders">Manage orders</a>
                    <a class="menu-contact" href="dash_root_contact_mssg">Contact messages</a>
                    <a class="menu-admins" onclick="showadmins()">Manage Admins</a>
                    <div class="admins-grids">
                            <a class="menu-admins-manage" href="dash_root_admins">Manage admins</a>
                            <a class="menu-add-admin" href="dash_root_add_admin">Add Admin</a>
                        </div>
                </div>
                <form action="" method="post" class="menu_logout">
                    <p>username : <span><?php echo $_SESSION['admin_username'];?></span></p>
                    <p>rôle : <span><?php echo $_SESSION['admin_role'];?></span></p>
                    <button type="submit" name="logout">Logout</button>
                </form>
            </div>
            <div class="border"></div>
            <div class="dashboard-container">
                <a><ion-icon name="caret-back" class="menu-dash-icon" onclick="hidemenu()"></ion-icon> </a>
                <h2>Welcome <span><?php echo $_SESSION['admin_username'];?></span></h2>
                <div class="dashroot">
                    <p>My Dashboard / <span>Home</span></p>
                </div>
                <div class="dash-box">
                    <div class="home_dash">
                        <div class="home_header">
                            <div class="home_header_income">
                                <h4>My Income :</h3>
                                <?php
                                $thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));
                                $select_income = $conn -> prepare("SELECT SUM(price*qty) AS income FROM `orders` WHERE status='delivered'OR status='done' AND date>= :thirtyDaysAgo");
                                $select_income->bindParam(':thirtyDaysAgo', $thirtyDaysAgo, PDO::PARAM_STR);
                                $select_income->execute();
                                $fetch_income = $select_income->fetch(PDO::FETCH_ASSOC);
                                ?>
                                <h1><span><?=$fetch_income["income"] ?></span> $</h1>
                                <?php
                                ?>
                                <p>since <span>last mounth</span></p>
                            </div>
                            <div class="home_header_orders">
                                <h1>Orders :</h1>
                                <div class="orders_statut">
                                    <?php
                                    $select_inprogress = $conn -> prepare("SELECT COUNT(*) AS or_p FROM `orders` WHERE status='in progress'");
                                    $select_inprogress ->execute();
                                    $fetch_inprogress = $select_inprogress->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    <div class="inprogress">
                                        <h2><?=$fetch_inprogress["or_p"]?></h2>
                                        <p>in progress</p>
                                    </div>
                                    <?php
                                    $select_accepted = $conn -> prepare("SELECT COUNT(*) AS or_a FROM `orders` WHERE status='accepted'");
                                    $select_accepted ->execute();
                                    $fetch_accepted = $select_accepted->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    <div class="accepted">
                                        <h2><?=$fetch_accepted["or_a"]?></h2>
                                        <p>accepted</p>
                                    </div>
                                    <?php
                                    $select_delivered = $conn -> prepare("SELECT COUNT(*) AS or_d FROM `orders` WHERE status='delivered'");
                                    $select_delivered ->execute();
                                    $fetch_delivered = $select_delivered->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    <div class="delivered">
                                        <h2><?=$fetch_delivered["or_d"]?></h2>
                                        <p>delivered</p>
                                    </div>
                                </div>
                            </div>
                            <div class="home_header_contact">
                                <h1>Disclaim :</h1>
                                <div class="contact_num">
                                <?php
                                    $select_contact = $conn -> prepare("SELECT COUNT(*) AS con_msg FROM `contact`");
                                    $select_contact ->execute();
                                    $fetch_contact = $select_contact->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    <span><?=$fetch_contact["con_msg"]?></span>
                                    <?php
                                        if ($fetch_contact["con_msg"] > 0) {
                                    ?>
                                    <ion-icon name="notifications"></ion-icon>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="home_middle">
                        <div class="home_middle_users_top3">
                            <div class="home_middle_users">
                                <div class="home_middle_users_num">
                                    <?php
                                        $select_users = $conn -> prepare("SELECT COUNT(*) AS users_num FROM `users`");
                                        $select_users ->execute();
                                        $fetch_users = $select_users->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                        <h4>Users : </h4>
                                        <h1><span><?=$fetch_users["users_num"]?></span> <ion-icon name="person"></ion-icon></h1>
                                        <p>since <span>the beginning</span></p>
                                </div>
                                <div class="home_middle_users_gender">
                                        <div class="gender_key">
                                            <h4>Users gender :</h4>
                                            <div class="male">
                                                <sup></sup>
                                                <p>Male</p>
                                            </div>
                                            <div class="female">
                                                <sup></sup>
                                                <p>Female</p>
                                            </div>
                                            <div class="nospecified">
                                                <sup></sup>
                                                <p>No specified</p>
                                            </div>
                                        </div>
                                        <div class="gender_apparent">
                                            <?php
                                                $select_male = $conn -> prepare("SELECT COUNT(*) AS male FROM `users` WHERE gender='male'");
                                                $select_male ->execute();
                                                $fetch_male = $select_male->fetch(PDO::FETCH_ASSOC);

                                                $select_female = $conn -> prepare("SELECT COUNT(*) AS female FROM `users` WHERE gender='female'");
                                                $select_female ->execute();
                                                $fetch_female = $select_female->fetch(PDO::FETCH_ASSOC);

                                                $select_none = $conn -> prepare("SELECT COUNT(*) AS non_s FROM `users` WHERE gender='none'");
                                                $select_none ->execute();
                                                $fetch_none = $select_none->fetch(PDO::FETCH_ASSOC);

                                                $total_users = $fetch_male['male'] + $fetch_female['female'] + $fetch_none['non_s'];

                                                $percentage_male = ($fetch_male['male'] / $total_users) * 100;
                                                $percentage_female = ($fetch_female['female'] / $total_users) * 100;
                                                $percentage_none = ($fetch_none['non_s'] / $total_users) * 100;
                                            ?>
                                            <div class="appa-circle">
                                                <style>
                                                    .appa-circle{
                                                        background:conic-gradient(from 0deg,rgb(255, 255, 255),
                                                        rgb(9, 99, 115)0deg <?= $percentage_male ?>%,
                                                        rgb(217, 1, 76) <?= $percentage_male?>% <?=$percentage_male+$percentage_female ?>%,
                                                        rgb(255, 255, 255) <?=$percentage_male+$percentage_female?>% 100%
                                                        );
                                                    }
                                                </style>
                                            </div>
                                            <div class="sec-circle"></div>
                                        </div>
                                </div>
                            </div>
                            <div class="home_middle_top3">
                                <h4>Top 3 products selled : </h4>
                                <div class="top3-container">
                                <?php
                                $thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));
                                $select_top3 = $conn -> prepare("SELECT product_id, SUM(qty) as total_quantity FROM `orders` WHERE status='delivered' OR status='done' AND date>= :thirtyDaysAgo GROUP BY product_id ORDER BY total_quantity DESC LIMIT 3");
                                $select_top3->bindParam(':thirtyDaysAgo', $thirtyDaysAgo, PDO::PARAM_STR);
                                $select_top3->execute();
                                if ($select_top3 -> rowCount()>0) {
                                    while ($fetch_top3 = $select_top3->fetch(PDO::FETCH_ASSOC)) {
                                        $product_top3 = $conn -> prepare("SELECT * FROM `products` WHERE id=?");
                                        $product_top3->execute([$fetch_top3["product_id"]]);
                                        while ($fetch_product_top3= $product_top3->fetch(PDO::FETCH_ASSOC)){
                                ?>
                                <div class="top3-box">
                                    <div class="top3-box-left">
                                        <img src="imgs/products/<?=$fetch_product_top3["image"]?>" alt="">
                                    </div>
                                    <div class="top3-box-right">
                                        <h5><?=$fetch_product_top3["name"]?></h5>
                                        <h2><?=$fetch_top3["total_quantity"]?> sell</h2>
                                        <p>income : <?=$fetch_product_top3["price"]*$fetch_top3["total_quantity"]?>$</p>
                                    </div>
                                    
                                </div>
                                <?php
                                        }
                                    }
                                }
                                
                                ?>
                                </div>
                                <p>since <span>last mounth</span></p>
                            </div>
                        </div>
                        <div class="home_middle_products">
                            <div class="home_middle_products_num">
                                    <h4>Products :</h3>
                                    <?php
                                    $select_products = $conn -> prepare("SELECT COUNT(*) AS pro_num FROM `products`");
                                    $select_products ->execute();
                                    $fetch_products = $select_products->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                <h1><span><?=$fetch_products["pro_num"]?></span> <ion-icon name="pricetags"></ion-icon></h1>
                                <p>proposed <span>in store</span></p>
                            </div>
                            <div class="home_middle_products_selled">
                                <h4>Products selled :</h3>
                                <?php
                                $thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));
                                $select_selled = $conn -> prepare("SELECT count(*) AS selled FROM `orders` WHERE status='delivered' OR status='done' AND date>= :thirtyDaysAgo");
                                $select_selled->bindParam(':thirtyDaysAgo', $thirtyDaysAgo, PDO::PARAM_STR);
                                $select_selled->execute();
                                $fetch_selled = $select_selled->fetch(PDO::FETCH_ASSOC);
                                ?>
                                <h1><span><?=$fetch_selled["selled"]?></span> <ion-icon name="bag-handle"></ion-icon></h1>
                                <p>since<span> last mounth</span></p>
                            </div>
                        </div>
                        </div>
                        <div class="home_footer">
                        <h4>products ruptured on stock</h4>
                        <div class="products-ruptured">
                            <?php
                                $select_rupture = $conn -> prepare("SELECT * FROM `products` WHERE enstock = 0");
                                $select_rupture->execute();
                                if($select_products->rowCount()>0){
                                    while ($fetch_rupture = $select_rupture->fetch(PDO::FETCH_ASSOC)){
                                ?>
                                <div class="rup_container">
                                    <div class="rup_container_left">
                                        <img src="imgs/products/<?=$fetch_rupture["image"]?>" alt="">
                                    </div>
                                    <div class="rup_container_right">
                                        <h6><?=$fetch_rupture["category"]?></h6>
                                        <h4><?=$fetch_rupture["name"]?></h4>
                                        <p>price :<?=$fetch_rupture["price"]?> $</p>
                                    </div>
                                </div>
                                
                                <?php
                                    }
                                }
                                ?>
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    <script defer src="dashbord/dash_script.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>    
<?php include "../components/alert.php"?> 
</body>
</html>