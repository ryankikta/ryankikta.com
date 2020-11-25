<?php
// Only show if logged in
$current_user = wp_get_current_user();
if (0 == $current_user->ID) {
    wp_redirect("/login");
    exit();
}

get_header();
?>

<div class="container-fluid dashboard_content my_brand">
    <div class="row">
        <?php include('sidebar.php'); ?>
        <div class="col py-80">
            <img class="dashboard_graphic dashboard_graphic_default" src="<?php echo get_template_directory_uri(); ?>/images/dashboard_graphic_default.png">
            <div class="row">
                <div class="col-lg-7 col-xl-6">
                    <h1 class="fs2"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="120" height="120" viewBox="0 0 120 120"><ellipse style="opacity:0.5; fill:#19CBC5;" cx="60" cy="60" rx="60" ry="60"/><g transform="translate(14.374 14.374)"><ellipse style="fill:#19CBC5;" cx="45.6" cy="45.6" rx="45.6" ry="45.6"/></g><g transform="translate(-1025.422 -588.821)"><path style="fill:none;stroke:#361181;stroke-width:2.5;" d="M1099.1,671.6h-27.5c-0.4,0-0.7-0.3-0.7-0.7l0,0v-27.8l-4.2,4.7c-0.3,0.3-0.7,0.3-1,0.1l-7-5.1c-0.3-0.2-0.4-0.7-0.2-1l7.5-12c1.3-2.1,3.6-3.5,6-3.7c0,0,4.2-0.7,6.6-1.1c0.4-0.1,0.7,0.2,0.8,0.5c1,3.2,4.5,5.1,7.7,4c1.9-0.6,3.4-2.1,4-4c0.1-0.4,0.5-0.6,0.8-0.5c2.5,0.4,6.6,1.1,6.6,1.1c2.5,0.2,4.7,1.6,6,3.7l7.5,12c0.2,0.3,0.1,0.8-0.2,1l-7,5.1c-0.3,0.2-0.7,0.2-1-0.1l-4.2-4.7v27.8C1099.9,671.3,1099.5,671.6,1099.1,671.6C1099.1,671.6,1099.1,671.6,1099.1,671.6z"/></g></svg> My Products</h1>

                    <h2 class="fs1">Discover Products</h2>
                    <h3 class="fs2">Customize your atmosphere with a galaxy of products</h3>
                    <p>This page is only for those who are using one of our apps such as Shopify, Storenvy, the API, or other integrations. All other manually placed orders do not use this system. To learn more about integrations, <a href="#">click here</a>.</p>
                </div>
            </div>
            <div class="col-12 col-lg-6 p-0 text-center">
                <h2 class="fs3">Your Products</h2>
                <svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="36" height="17" viewBox="0 0 35.1 16.8"><path style="fill:none;stroke:#DEDEDE;stroke-width:4;stroke-linecap:round;" d="M2,2l15.7,12.3L33.1,2"/></svg>
            </div>
        </div>
    </div>

</div>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 text-right">
            <input class="search_bar" type="text" placeholder="Search Products">
            <div class="row product_selectors justify-content-around">
                <select>
                    <option>Store</option>
                </select>
                <select>
                    <option>All Products</option>
                </select>
                <select>
                    <option>9 per page</option>
                </select>
            </div>
        </div>
        <div class="col-md-4 text-left">
            <div class="text-center">
                <a class="btn btn-primary" href="">Add New Product</a><br>
                <a class="btn btn-secondary mt-2 mt-md-4 mb-3" href="">Delete Selected</a><br>
                <a href=""><svg xmlns="http://www.w3.org/2000/svg" width="13.961" height="13.961" viewBox="0 0 13.961 13.961">
                      <path id="Path_1788" data-name="Path 1788" d="M4941.105,3027.107a1.5,1.5,0,0,1-1.5,1.5h-10.968a1.5,1.5,0,0,1-1.5-1.5h0v-10.968a1.5,1.5,0,0,1,1.5-1.5h10.968a1.5,1.5,0,0,1,1.5,1.5Zm-6.232-10.471a.75.75,0,0,0-.528,1.277l1,1L4929.25,3025a.371.371,0,0,0,0,.532l.965.965a.377.377,0,0,0,.532,0l6.089-6.092,1,1a.756.756,0,0,0,1.061,0,.747.747,0,0,0,.217-.529v-3.49a.747.747,0,0,0-.746-.749Z" transform="translate(-4927.145 -3014.643)" fill="#bbbdbf"/>
                    </svg>  EXPORT SELECTED TO .CSV
                </a>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">

        </div>

    </div>
<!-- <div class="container"> -->
        <div class="row">
            <div class="col-12 col-md-6 col-lg-4 my-3">
                <div class="product_wrapper card">
                    <div class="d-flex justify-content-between mb-2">
                        <label><input type="checkbox" /> Select</label>
                        <svg xmlns="http://www.w3.org/2000/svg" width="31.486" height="28.922" viewBox="0 0 31.486 28.922">
                              <g id="layer1" transform="translate(-66.749 -164.196)">
                                <path id="path2361" d="M76.257,165.7c-8-.144-15.4,12.362,6.163,25.329a.091.091,0,0,1,.138,0c22.256-13.385,13.653-26.277,5.389-25.274a6.468,6.468,0,0,0-5.458,3.717,6.468,6.468,0,0,0-5.458-3.717A7.536,7.536,0,0,0,76.257,165.7Z" transform="translate(0)" fill="none" stroke="#bbbdbf" stroke-width="3"/>
                              </g>
                        </svg>
                    </div>
                    <img src="https://placehold.it/400" class="img-fluid card-img-top" alt="...">
                    <div class="card-body">
                        <h4 class="card-title fs3">FLEXIT WASHED COTTON DAD HAT</h4>
                        <h5>Yupoong 6997</h5>
                        <p class="card-text">View:</p>
                        <div class="d-flex justify-content-between">
                            <ul>
                                <li><a href="">MOCKUP FRONT</a></li>
                                <li><a href="">PRINT FILE FRONT</a></li>
                            </ul>
                            <ul>
                                <li><a href="">MOCKUP BACK</a></li>
                                <li><a href="">PRINT FILE BACK</a></li>
                            </ul>
                        </div>
                        <div class="d-flex justify-content-center mt-2">
                            <a href="#" class="btn btn-primary">Product Details</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-md-6 col-lg-4 my-3">
                <div class="product_wrapper card">
                    <div class="d-flex justify-content-between mb-2">
                        <label><input type="checkbox" /> Select</label>
                        <svg xmlns="http://www.w3.org/2000/svg" width="31.486" height="28.922" viewBox="0 0 31.486 28.922">
                              <g id="layer1" transform="translate(-66.749 -164.196)">
                                <path id="path2361" d="M76.257,165.7c-8-.144-15.4,12.362,6.163,25.329a.091.091,0,0,1,.138,0c22.256-13.385,13.653-26.277,5.389-25.274a6.468,6.468,0,0,0-5.458,3.717,6.468,6.468,0,0,0-5.458-3.717A7.536,7.536,0,0,0,76.257,165.7Z" transform="translate(0)" fill="none" stroke="#bbbdbf" stroke-width="3"/>
                              </g>
                        </svg>
                    </div>
                    <img src="https://placehold.it/400" class="img-fluid card-img-top" alt="...">
                    <div class="card-body">
                        <h4 class="card-title fs3">FLEXIT WASHED COTTON DAD HAT</h4>
                        <h5>Yupoong 6997</h5>
                        <p class="card-text">View:</p>
                        <div class="d-flex justify-content-between">
                            <ul>
                                <li><a href="">MOCKUP FRONT</a></li>
                                <li><a href="">PRINT FILE FRONT</a></li>
                            </ul>
                            <ul>
                                <li><a href="">MOCKUP BACK</a></li>
                                <li><a href="">PRINT FILE BACK</a></li>
                            </ul>
                        </div>
                        <div class="d-flex justify-content-center mt-2">
                            <a href="#" class="btn btn-primary">Product Details</a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-12 col-md-6 col-lg-4 my-3">
                <div class="product_wrapper card">
                    <div class="d-flex justify-content-between mb-2">
                        <label><input type="checkbox" /> Select</label>
                        <svg xmlns="http://www.w3.org/2000/svg" width="31.486" height="28.922" viewBox="0 0 31.486 28.922">
                              <g id="layer1" transform="translate(-66.749 -164.196)">
                                <path id="path2361" d="M76.257,165.7c-8-.144-15.4,12.362,6.163,25.329a.091.091,0,0,1,.138,0c22.256-13.385,13.653-26.277,5.389-25.274a6.468,6.468,0,0,0-5.458,3.717,6.468,6.468,0,0,0-5.458-3.717A7.536,7.536,0,0,0,76.257,165.7Z" transform="translate(0)" fill="none" stroke="#bbbdbf" stroke-width="3"/>
                              </g>
                        </svg>
                    </div>
                    <img src="https://placehold.it/400" class="img-fluid card-img-top" alt="...">
                    <div class="card-body">
                        <h4 class="card-title fs3">FLEXIT WASHED COTTON DAD HAT</h4>
                        <h5>Yupoong 6997</h5>
                        <p class="card-text">View:</p>
                        <div class="d-flex justify-content-between">
                            <ul>
                                <li><a href="">MOCKUP FRONT</a></li>
                                <li><a href="">PRINT FILE FRONT</a></li>
                            </ul>
                            <ul>
                                <li><a href="">MOCKUP BACK</a></li>
                                <li><a href="">PRINT FILE BACK</a></li>
                            </ul>
                        </div>
                        <div class="d-flex justify-content-center mt-2">
                            <a href="#" class="btn btn-primary">Product Details</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <!-- </div> -->
</div>

<?php include('includes/pagination.php'); ?>
<?php include('includes/graphic_design.php'); ?>

<?php get_footer(); ?>
