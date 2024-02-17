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
                    <p>My Dashboard / <span class="dash-root">Products / Manage</span></p>
                </div>
                <div class="dash-box">
                    <div class="products_manager">
                    <form class="search_bar" method="post">
                        <h3>Search product : <input type="text" name="search_value"><button type="submit" name="search"><ion-icon name="search-outline"></ion-icon></button></h3>
                    </form>
                    <?php
                    if (isset($_POST['search'])) {
                        $product_name = $_POST['search_value'];
                        $product_name = filter_var($product_name,FILTER_SANITIZE_STRING);
                        $select_products = $conn->prepare("SELECT * FROM `products` WHERE id LIKE ? OR category LIKE ? OR name LIKE ?");
                        $select_products->execute([$product_name, $product_name ,$product_name]);
                        if($select_products->rowCount()>0){
                    ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Category</th>
                                <th>Name</th>
                                <th>En stock</th>
                                <th>Qte</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                    <?php
                        while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){
                    ?>
                        <tr>
                            <td><?=$fetch_products["id"]?></td>
                            <td><?=$fetch_products["category"]?></td>
                            <td><?=$fetch_products["name"]?></td>
                            <td><?php
                                if ($fetch_products["enstock"]===1) {
                                ?>
                                <span style="color:green;font-size:20px"><ion-icon name="checkmark"></ion-icon></span>
                                <?php
                                }else{
                                ?>
                                <span style="color:red;font-size:20px"><ion-icon name="close"></ion-icon></span>
                                <?php
                                }
                            ?></td>
                            <td><?=$fetch_products["quantité"]?></td>
                            <td><form action="" method="post">
                                <a href="dash_product_details?pid=<?php echo $fetch_products["id"];?>">Details</a>
                                <input type="hidden" name="product_id" value="<?=$fetch_products['id'];?>">
                                <a href="dash_product_update?pid=<?php echo $fetch_products["id"];?>" class="update">Update</a>
                                <button type="submit" name="delete_product" class="delete" onclick="return confirm('delete this product ??')">Delete</button>
                                </form></td>
                        </tr>
                    <?php
                            }
                        }
                    ?>
                        </tbody>
                    </table>
                    <?php
                        }else{
                    ?>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Category</th>
                                <th>Name</th>
                                <th>En stock</th>
                                <th>Qte</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $select_stock = $conn -> prepare("SELECT * FROM `products` ORDER BY category");
                                $select_stock->execute();
                                if ($select_stock->rowCount()>0) {
                                    while ($fetch_stock = $select_stock->fetch(PDO::FETCH_ASSOC)) {
                            ?>
                            <tr>
                                <td><?=$fetch_stock["id"]?></td>
                                <td><?=$fetch_stock["category"]?></td>
                                <td><?=$fetch_stock["name"]?></td>
                                <td><?php
                                    if ($fetch_stock["enstock"]===1) {
                                    ?>
                                        <span style="color:green;font-size:20px"><ion-icon name="checkmark"></ion-icon></span>
                                    <?php
                                    }else{
                                    ?>
                                        <span style="color:red;font-size:20px"><ion-icon name="close"></ion-icon></span>
                                    <?php
                                        }
                                    ?>
                                </td>
                                <td><?=$fetch_stock["quantité"]?></td>
                                <td><form action="" method="post">
                                    <a href="dash_product_details?pid=<?php echo $fetch_stock["id"];?>">Details</a>
                                    <input type="hidden" name="product_id" value="<?=$fetch_stock['id'];?>">
                                    <a href="dash_product_update?pid=<?php echo $fetch_stock["id"];?>" class="update">Update</a>
                                    <button type="submit" name="delete_product" class="delete" onclick="return confirm('delete this product ??')">Delete</button>
                                </form></td>
                            </tr>
                            <?php
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                    <?php
                        }
                    ?>
                    </div>
                </div>
            </div>
        </section>
    <script src="dashbord/dash_script.js"></script>
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>    
<?php include "../components/alert.php"?> 
</body>
</html>