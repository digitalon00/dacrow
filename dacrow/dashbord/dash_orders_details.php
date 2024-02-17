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
    
    if (isset($_POST['delete'])) {
        $order_id = $_POST['order_id'];
        $order_id = filter_var($order_id,FILTER_SANITIZE_STRING);

        $varify_delete_items=$conn->prepare("SELECT * FROM `orders` WHERE id=? ");
        $varify_delete_items->execute([$order_id]);

        if($varify_delete_items->rowCount()>0){
            $delete_order_id = $conn ->prepare("DELETE FROM `orders` WHERE id=?");
            $delete_order_id->execute([$order_id]);
        }
        header("Location: dash_root_orders");
        exit();
    }

    if(isset($_POST['accept'])){
        $order_id = $_POST['order_id'];
        $order_id = filter_var($order_id,FILTER_SANITIZE_STRING);

            $update_order = $conn->prepare("UPDATE `orders` SET status='accepted' WHERE id=?");
            $update_order -> execute([$order_id]);

            header("Location: dash_root_orders");
        exit();
        }
    if(isset($_POST['delivered'])){
        $order_id = $_POST['order_id'];
        $order_id = filter_var($order_id,FILTER_SANITIZE_STRING);

            $update_order = $conn->prepare("UPDATE `orders` SET status='delivered' WHERE id=?");
            $update_order -> execute([$order_id]);

            header("Location: dash_root_orders");
        exit();
        }
    if(isset($_POST['done'])){
        $order_id = $_POST['order_id'];
        $order_id = filter_var($order_id,FILTER_SANITIZE_STRING);

            $update_order = $conn->prepare("UPDATE `orders` SET status='done' WHERE id=?");
            $update_order -> execute([$order_id]);

            header("Location: dash_root_orders");
        exit();
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
            </div>
            <div class="border"></div>
            <div class="dashboard-container">
                <a><ion-icon name="caret-back" class="menu-dash-icon" onclick="hidemenu()"></ion-icon> </a>
                <h2>Welcome <span><?php echo $_SESSION['admin_username'];?></span></h2>
                <div class="dashroot">
                    <p>My Dashboard / <span class="dash-root">Orders / Manage / Order details</span></p>
                </div>
                <div class="dash-box">
                    <span class="back" onclick="goBack()"><ion-icon name="arrow-back-outline" ></ion-icon>Back</span>
                    <script>
                        function goBack() {
                        window.history.back();
                            }
                    </script>
                    <div class="order_details">
                        <?php
                            if(isset($_GET['pid'])){
                            $pid = $_GET['pid'];
                            $select_order = $conn  -> prepare("SELECT * FROM `orders` WHERE id='$pid'");
                            $select_order ->execute();
                            if ($select_order->rowCount()>0) {
                                while($fetch_order = $select_order->fetch(PDO::FETCH_ASSOC)){
                        ?>
                        <h3 class="order_id"> Order ID : <span><?=$fetch_order["id"]?></span></h3>
                        <div class="informations">
                            <div class="user_infos">
                                <h1>Custumer Informations</h1>
                                <h3>ID : <span><?=$fetch_order["user_id"]?></span></h3>
                                <h3>First name : <span><?=$fetch_order["firstname"]?></span></h3>
                                <h3>Last name : <span><?=$fetch_order["lastname"]?></span></h3>
                                <h3>email : <span><?=$fetch_order["email"]?></span></h3>
                                <h3>Phone : <span><?=$fetch_order["phone"]?></span></h3>
                                <h3>Adresse : <span><?=$fetch_order["adresse"]?>, <?=$fetch_order["city"]?>, <?=$fetch_order["country"]?>, <?=$fetch_order["adresse_type"]?>.</span></h3>
                            </div>
                            <div class="product_infos">
                                <h1>Product Informations</h1>
                                <h3>ID : <span><?=$fetch_order["product_id"]?></span></h3>
                                <?php
                                $select_products=$conn->prepare("SELECT * FROM  `products` WHERE id=? ");
                                $select_products->execute([$fetch_order["product_id"]]);
                                $fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)
                                ?>
                                <h3>name : <span><?=$fetch_products["name"]?></span></h3>
                                <h3>marque : <span><?=$fetch_products["marque"]?></span></h3>
                                <h3>category : <span><?=$fetch_products["category"]?></span></h3>
                                <h3>price : <span><?=$fetch_order["price"]?> $</span></h3>
                                <h3>quantity : <span><?=$fetch_order["qty"]?></span></h3>
                            </div>
                        </div>
                        <div class="order_infos">
                            <h3>Payement method : <span><?=$fetch_order["payement"]?></span></h3>
                            <h3>Date : <span><?=date('Y-m-d',strtotime($fetch_order["date"]))?></span></h3>
                            <h3>Status : <span><?=$fetch_order["status"]?></span></h3>
                        </div>
                        <form action="" method="post">
                            <input type="hidden" name="order_id" value="<?=$fetch_order['id'];?>">
                            <?php
                            if ($fetch_order['status']==='in progress') {
                                $btn_class = 'accept_btn';
                                $btn_name = 'accept';
                            }elseif ($fetch_order['status']==='accepted') {
                                $btn_class = 'delivered_btn';
                                $btn_name = 'delivered';
                            }else{
                                $btn_class = 'done_btn';
                                $btn_name = 'done';
                            }
                            ?>
                            <button type="submit" name="<?php echo $btn_name?>" class="<?php echo $btn_class?>" onclick="return confirm('sure!??')"><?php echo $btn_name?></button>
                            <button type="submit" name="delete" onclick="return confirm('delete this order ??')">Delete</button>
                        </form>
                        <?php
                                    };
                                }
                            }
                        ?>
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