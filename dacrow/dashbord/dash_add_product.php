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

        if (isset($_POST["insert"])) {
            $targetDirectory = '../imgs/products/';
            $fileName = basename($_FILES['file']['name']);
            $targetFilePath = $targetDirectory . $fileName;
            if (file_exists($targetFilePath)) {
                unlink($targetFilePath);
            }
            move_uploaded_file($_FILES['file']['tmp_name'], $targetFilePath);
            echo "File uploaded successfully.";

        $category = $_POST["category"];
        $category = filter_var($category,FILTER_SANITIZE_STRING);

        $name = $_POST["name"];
        $name = filter_var($name,FILTER_SANITIZE_STRING);

        $price = $_POST["price"];
        $price = filter_var($price,FILTER_SANITIZE_STRING);

        $marque = $_POST["marque"];
        $marque = filter_var($marque,FILTER_SANITIZE_STRING);

        $image = $_POST["image"];
        $image = filter_var($image,FILTER_SANITIZE_STRING);

        $quantité = $_POST["quantité"];
        $quantité = filter_var($quantité,FILTER_SANITIZE_STRING);

        if ($quantité>0) {
            $enstock = 1;
        }else{
            $enstock = 0;
        }
        $details = $_POST["details"];
        $details = filter_var($details,FILTER_SANITIZE_STRING);

        $add_product=$conn->prepare("INSERT INTO `products` (category,name,price,marque,image,quantité,enstock,details) VALUES (?,?,?,?,?,?,?,?)");
        $add_product->execute([$category,$name,$price,$marque,$image,$quantité,$enstock,$details]);

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
                    <p>My Dashboard / <span class="dash-root">Products / Add Product </span></p>
                </div>
                <div class="dash-box">
                    <span class="back" onclick="goBack()"><ion-icon name="arrow-back-outline" ></ion-icon>Back</span>
                    <script>
                        function goBack() {
                        window.history.back();
                            }
                    </script>
                    <div class="product_add">
                            <form action="" method="post" enctype="multipart/form-data">
                            <h3>Category : <input type="text" name="category" required></h3>
                            <h3>name : <input type="text" name="name" required></h3>
                            <h3>price : <input type="text" name="price" required></h3>
                            <h3>marque : <input type="text" name="marque" required></h3>
                            <h3>image : <input type="text" name="image" required></h3>
                            <h3>file : <input type="file" name="file" value="Upload" class="file"></h3>
                            <h3>quantité : <input type="text" name="quantité" required></h3>
                            <h3>details : <textarea name="details" required></textarea></h3>
                            <button type="submit" name="insert" onclick="return confirm('add the product ??')">Add product</button>
                            </form>
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