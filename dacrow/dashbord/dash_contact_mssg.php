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
    
    if (isset($_POST['delete_mssg'])) {
        $contact_id = $_POST['contact_id'];
        $contact_id = filter_var($contact_id,FILTER_SANITIZE_STRING);
                                        
        $varify_delete_items=$conn->prepare("SELECT * FROM `contact` WHERE id=? ");
        $varify_delete_items->execute([$contact_id]);
                                                    
        if($varify_delete_items->rowCount()>0){
            $delete_contact_id = $conn ->prepare("DELETE FROM `contact` WHERE id=?");
            $delete_contact_id->execute([$contact_id]);
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
                    <p>My Dashboard / <span class="dash-root">Contact messages</span></p>
                </div>
                <div class="dash-box">
                    <div class="contact_table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Fullname</th>
                                    <th>Email</th>
                                    <th>Subject</th>
                                    <th>Message</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $select_contact_table = $conn  -> prepare("SELECT * FROM `contact`");
                            $select_contact_table ->execute();
                            if ($select_contact_table->rowCount()>0) {
                                while($fetch_contact_table = $select_contact_table->fetch(PDO::FETCH_ASSOC)){
                                    $select_user=$conn->prepare("SELECT * FROM `users` WHERE username=?");
                                    $select_user->execute([$fetch_contact_table["username"]]);    
                                    $fetch_user=$select_user->fetch(PDO::FETCH_ASSOC);                                
                            ?>
                                <tr>
                                    <td><?=$fetch_contact_table["username"]?></td>
                                    <td><?php if($select_user->rowCount()>0) {?><?=$fetch_user["firstname"]?> <?=$fetch_user["lastname"]?><?php }else{ ?> not logged <?php } ?></td>
                                    <td><?=$fetch_contact_table["email"]?></td>
                                    <td><?=$fetch_contact_table["subject"]?></td>
                                    <td ><textarea><?=$fetch_contact_table["message"]?></textarea></td>
                                    <td><form action="" method="post">
                                        <input type="hidden" name="contact_id" value="<?=$fetch_contact_table['id'];?>">
                                        <button type="submit" name="delete_mssg" onclick="return confirm('delete this message ??')">Delete</button>
                                        <a href="">answer</a>
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