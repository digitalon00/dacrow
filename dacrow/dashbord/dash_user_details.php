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
        header("Location: dash_root_users");
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
                    <p>My Dashboard / <span class="dash-root">Users / User details</span></p>
                </div>
                <div class="dash-box">
                    <span class="back" onclick="goBack()"><ion-icon name="arrow-back-outline" ></ion-icon>Back</span>
                    <script>
                        function goBack() {
                        window.history.back();
                            }
                    </script>
                    <div class="user_details">
                            <?php
                            if(isset($_GET['pid'])){
                            $pid = $_GET['pid'];
                            $select_users_table = $conn  -> prepare("SELECT * FROM `users` WHERE id='$pid' ORDER BY joining_date DESC");
                            $select_users_table ->execute();
                            if ($select_users_table->rowCount()>0) {
                                while($fetch_users_table = $select_users_table->fetch(PDO::FETCH_ASSOC)){
                            ?>
                            <div class="user_id_username">
                                <h4>ID : <span><?=$fetch_users_table["id"]?></span></h4>
                                <h5>Username : <span><?=$fetch_users_table["username"]?></span></h5>
                            </div>
                            <h3>First name : <span><?=$fetch_users_table["firstname"]?></span></h3>
                            <h3>Last name : <span><?=$fetch_users_table["lastname"]?></span></h3>
                            <h3>Gender : <span><?=$fetch_users_table["gender"]?></span></h3>
                            <h3>E-mail : <span><?=$fetch_users_table["email"]?></span></h3>
                            <h3>Password : <span><?=$fetch_users_table["pass"]?></span></h3>
                            <h3>Phone : <span><?=$fetch_users_table["phone"]?></span></h3>
                            <h3>Adresse : <span><?=$fetch_users_table["adresse"]?></span></h3>
                            <h3>Country : <span><?=$fetch_users_table["country"]?></span></h3>
                            <h3>City : <span><?=$fetch_users_table["city"]?></span></h3>
                            <h3>Joining date : <span><?=$fetch_users_table["joining_date"]?></span></h3>
                            <form action="" method="post">
                                <input type="hidden" name="user_id" value="<?=$fetch_users_table['id'];?>">
                                <button type="submit" name="delete_user" onclick="return confirm('delete this user ??')">Delete</button>
                            </form>
                                <?php
                            };
                        }
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