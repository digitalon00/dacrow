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
                    <a class="menu-contact" href="dash_root_users">Manage users</a>
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
                    <p>My Dashboard / <span class="dash-root">Manage Orders / Canceled</span></p>
                </div>
                <div class="dash-box">
                    <div class="orders_list">
                        <div class="setuation">
                            <a class="set_canceled" href="dash_orders_canceled">canceled</a>
                            <a class="set_inprogress" href="dash_root_orders">in progress</a>
                            <a class="set_accepted" href="dash_orders_accepted">accepted</a>
                            <a class="set_delivered" href="dash_orders_delivered">delivered</a>
                            <a class="history" href="dash_orders_done">history</a>
                        </div>
                        <div class="orders_table">
                            <table>
                                <thead>
                                    <tr>
                                        <th>user ID</th>
                                        <th>user fullname</th>
                                        <th>prod ID</th>
                                        <th>prod name</th>
                                        <th>total price</th>
                                        <th>canceling date</th>
                                        <th>status</th>
                                        <th>actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $select_order=$conn->prepare("SELECT * FROM `orders` WHERE status='canceled' ORDER BY user_id DESC");
                                    $select_order->execute();
                                    if ($select_order->rowCount()>0) {
                                        while ($fetch_order = $select_order -> fetch(PDO::FETCH_ASSOC)) {
                                            ?>
                                            <tr>
                                            <td><?=$fetch_order["user_id"] ?></td>
                                            <td><?=$fetch_order["firstname"] ?> <?=$fetch_order["lastname"] ?></td>
                                            <td><?=$fetch_order["product_id"] ?></td>
                                            <?php
                                            $select_product=$conn->prepare("SELECT * FROM `products` WHERE id=?");
                                            $select_product->execute([$fetch_order["product_id"]]);
                                            $fetch_product =  $select_product->fetch(PDO::FETCH_ASSOC);
                                            ?>
                                            <td><?=$fetch_product["name"] ?></td>
                                            <td><?=$fetch_order["price"]*$fetch_order["qty"] ?></td>
                                            <td><?=$fetch_order["canceled_time"] ?></td>
                                            <td><?=$fetch_order["status"] ?></td>
                                            <td><form action="" method="post">
                                                <input type="hidden" name="order_id" value="<?=$fetch_order['id'];?>">
                                                <button type="submit" name="delete" onclick="return confirm('delete this order ??')">Delete</button>
                                            </form></td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
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