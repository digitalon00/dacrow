<header>
    <div class="header-title">
        <a href="index"><img src="imgs/logo/dacrow_site_w.png" alt="dacrow" class="header-img"></a>
        <?php if(isset($_SESSION['user_username']) && isset($_SESSION['user_email'])) : ?>
        <p class="title-username"><?php echo $_SESSION['user_username'];?></p>
        <?php endif; ?>
    </div>
    <div class="header-navbar">
        <a href="index#home" class="header-navbar-a">Home</a>
        <a href="index#products-section" class="header-navbar-a">Products</a>
        <a href="orders" class="header-navbar-a">Orders</a>
        <a href="index#about" class="header-navbar-a">About</a>
        <a href="index#contact" class="header-navbar-a">Contact</a>
    </div>
    <div class="header-nav-icons">
        <div class="vis">
            <a class="header-icons" id="search-icon" href="search"><ion-icon name="search"></ion-icon></a>
            <a <?php if(empty($_SESSION['user_username'])) : ?>href="login"<?php endif;?> class="header-icons" id="login-icon"><ion-icon name="person"></ion-icon></a>

            <?php
            $count_wishlist_items=$conn->prepare("SELECT * FROM `wishlist` WHERE user_id = ?");
            $count_wishlist_items->execute([$user_id]);
            $total_wishlist_items = $count_wishlist_items->rowCount();
            ?>

            <?php 
                if($user_id){
                    $wishlist_href='wishlist';
                    $shop_href='shop';
                }else{
                    $wishlist_href='login';
                    $shop_href='login';
                }
            ?>
            <a href="<?=$wishlist_href ?>" class="header-icons" id="heart-icon"> <ion-icon name="heart"></ion-icon><sup><?=$total_wishlist_items;?></sup></a>
            <?php
            $count_cart_items=$conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
            $count_cart_items->execute([$user_id]);
            $total_cart_items = $count_cart_items->rowCount();
            ?>

            <a href="<?=$shop_href ?>" class="header-icons" id="shop-icon"><ion-icon name="cart"></ion-icon><sup><?=$total_cart_items?></sup></a>
        </div>
        <a class="header-icons" id="menu-icon"><ion-icon name="grid"></ion-icon></a>
    </div>
    <div class="user-box">
    <?php if(isset($_SESSION['user_username']) && isset($_SESSION['user_email'])) : ?>
        <div class="user-box-infos">
            <p>username : <span><?php echo $_SESSION['user_username'];?></span></p>
            <p>Email : <span><?php  echo $_SESSION['user_email'];?></span></p>
        </div>
        <form action="" method="post">
                <button type="submit" name="logout" class="logout-btn">log out</button>
            </form>
            <?php endif; ?>
        </div>
</header>
