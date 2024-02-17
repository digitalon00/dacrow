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
    
    if (isset($_POST['delete_product'])) {
        $product_id = $_POST['product_id'];
        $product_id = filter_var($product_id,FILTER_SANITIZE_STRING);
                                        
        $varify_delete_items=$conn->prepare("SELECT * FROM `products` WHERE id=? ");
        $varify_delete_items->execute([$product_id]);
                                                    
        if($varify_delete_items->rowCount()>0){
            $delete_product_id = $conn ->prepare("DELETE FROM `products` WHERE id=?");
            $delete_product_id->execute([$product_id]);
        }

        header("Location: dash_root_products");
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
                    <p>My Dashboard / <span class="dash-root">Products / Manage / Product details</span></p>
                </div>
                <div class="dash-box">
                    <span class="back" onclick="goBack()"><ion-icon name="arrow-back-outline" ></ion-icon>Back</span>
                    <script>
                        function goBack() {
                        window.history.back();
                            }
                    </script>
                    <div class="product_details">
                            <?php
                            if(isset($_GET['pid'])){
                            $pid = $_GET['pid'];
                            $select_products_table = $conn  -> prepare("SELECT * FROM `products` WHERE id='$pid'");
                            $select_products_table ->execute();
                            if ($select_products_table->rowCount()>0) {
                                while($fetch_products_table = $select_products_table->fetch(PDO::FETCH_ASSOC)){
                            ?>
                            <div class="product_id_category">
                                <h4>ID : <span><?=$fetch_products_table["id"]?></span></h4>
                                <h5>Category : <span><?=$fetch_products_table["category"]?></span></h5>
                            </div>
                            <h3>name : <span><?=$fetch_products_table["name"]?></span></h3>
                            <h3>price : <span><?=$fetch_products_table["price"]?></span></h3>
                            <h3>marque : <span><?=$fetch_products_table["marque"]?></span></h3>
                            <h3>image : <span><?=$fetch_products_table["image"]?></span></h3>
                            <h3>en stock : <span><?=$fetch_products_table["enstock"]?></span></h3>
                            <h3>quantité : <span><?=$fetch_products_table["quantité"]?></span></h3>
                            <p>details : <span><?=$fetch_products_table["details"]?></span></p>
                            <h3>Adding date : <span><?=$fetch_products_table["adding date"]?></span></h3>
                            <form action="" method="post">
                                <input type="hidden" name="product_id" value="<?=$fetch_products_table['id'];?>">
                                <button type="submit" name="delete_product" onclick="return confirm('delete this product ??')">Delete</button>
                                <a href="dash_product_update?pid=<?php echo $fetch_products_table["id"];?>">Update</a>
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