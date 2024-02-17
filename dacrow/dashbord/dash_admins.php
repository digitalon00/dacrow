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
    
    if (isset($_POST['delete_admin'])) {
        $admin_id = $_POST['admin_id'];
        $admin_id = filter_var($admin_id,FILTER_SANITIZE_STRING);
                                        
        $varify_delete_items=$conn->prepare("SELECT * FROM `admins` WHERE id=? ");
        $varify_delete_items->execute([$admin_id]);
                                                    
        if($varify_delete_items->rowCount()>0){
            $delete_admin_id = $conn ->prepare("DELETE FROM `admins` WHERE id=?");
            $delete_admin_id->execute([$admin_id]);
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
                    <p>My Dashboard / <span class="dash-root">Admins / Manage</span></p>
                </div>
                <div class="dash-box">
                    <div class="admins">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Rôle</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $select_admins = $conn  -> prepare("SELECT * FROM `admins`");
                            $select_admins ->execute();
                            if ($select_admins->rowCount()>0) {
                                while($fetch_admins = $select_admins->fetch(PDO::FETCH_ASSOC)){                            
                            ?>
                                <tr>
                                    <td><?=$fetch_admins["id"]?></td>
                                    <td><?=$fetch_admins["username"]?></td>
                                    <td><?=$fetch_admins["email"]?></td>
                                    <td><?=$fetch_admins["role"]?></td>
                                    <td><form action="" method="post">
                                        <input type="hidden" name="admin_id" value="<?=$fetch_admins['id'];?>">
                                        <button type="submit" name="delete_admin" onclick="return confirm('delete this admin ??')">Delete</button>
                                        <a href="dash_admin_update?pid=<?php echo $fetch_admins["id"];?>" class="update">Update</a>
                                    </form></td>
                                </tr>
                                <?php
                            };
                        }
                        ?>
                            </tbody>
                        </table>
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