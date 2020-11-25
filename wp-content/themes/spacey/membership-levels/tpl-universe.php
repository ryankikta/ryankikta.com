<?php /* Template Name: Universe */ ?>
<?php get_header(); ?>

<div class="header graphic_bg text-white-container">
	<img class="header_graphic" src="<?php echo CLOUD_URL_Assets; ?>/uploads/solutions/plan-overview-banner.svg">
	<div class="container">
		<div class="row align-items-center text-center">
            <div class="col-6 col-lg-2 offset-lg-2 mb-lg-3 d-flex justify-content-end justify-content-lg-center">
                <img width="150px" src="<?php echo CLOUD_URL_Assets; ?>/uploads/solutions/membership_universe.svg">
            </div>
            <div class="col-6 col-lg-1 d-flex justify-content-start">
                <div class="text-center">
                    <h1 class="fs2">Universe</h1>
                    <!-- <span class="badge">Best Value</span> -->
                </div>
            </div>
            <div class="col-12 col-lg-6 ml-lg-5 text-center text-lg-left mt-4 mt-lg-0">
                <h2 class="fs1">$99.00<span> / per month</span></h2>
            </div>
        </div>
        <div id="membershipHeroCopy" class="d-none d-lg-block col-12 col-lg-5 offset-lg-4 text-center text-lg-left">
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris fringilla in nisi vel convallis. Suspendisse sodales tritisque.</p>
        </div>
        <div class="col-12 col-lg-4 offset-lg-4 text-center">
            <a href="/sign-up" class="btn-primary">Sign Up</a>
        </div>
        <div class="col-12 text-center d-block d-lg-none mb-40">
            <a class="btn btn-link" data-toggle="collapse" href="#membershipSidebar" role="button" aria-expanded="false" aria-controls="collapseExample">
                VIEW PLAN DETAILS
            </a>
        </div>
    </div>
</div>

<section class="py-40">
	<div class="container-fluid">
		<div class="row">
			<div class="col text-center d-none d-lg-block">
				<h2 class="fs3">Why choose to build a universe</h2>
				<svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="36" height="17" viewBox="0 0 35.1 16.8"><path style="fill:none;stroke:#DEDEDE;stroke-width:4;stroke-linecap:round;" d="M2,2l15.7,12.3L33.1,2"/></svg>
			</div>
        </div>
        <div class="row">

            <div class="col-12 col-lg-4 membership-sidebar text-center mb-5 mb-lg-0 mr-lg-3 py-40 collapse" id="membershipSidebar">
                <img width="149px" src="<?php echo CLOUD_URL_Assets; ?>/uploads/solutions/membership_universe.svg">
                <h2 class="mt-3">UNIVERSE</h2>
                <div class="d-flex justify-content-center mt-3">
                    <div class="text-left">
                        <h2 class="fs3">PRICING</h2>
                        <ul>
                            <li>15% Discount</li>
                        </ul>
                        <h2 class="fs3">PRODUCTS</h2>
                        <ul>
                            <li>Unlimited Products</li>
                            <li>Discounted Samples**</li>
                        </ul>
                        <h2 class="fs3">ORDER/SHIPPING</h2>
                        <ul>
                            <li>No Fee Bulk Orders</li>
                            <li>Discounted Shipping</li>
                            <li>Free 7-10 Day Shipping</li>
                        </ul>
                        <h2 class="fs3">DESIGN</h2>
                        <ul>
                            <li>Create Mockup Images</li>
                            <li>50 Free Images Mo.*</li>
                            <li>Artwork Creation</li>
                            <li>Web Setup + Design</li>
                        </ul>
                        <h2 class="fs3">INTEGRATIONS</h2>
                        <ul>
                            <li>API Access</li>
                            <li>Unlimited Integrations</li>
                            <li>Free Site Ground</li>
                        </ul>
                        <h2 class="fs3">SUPPORT</h2>
                        <ul>
                            <li>Phone Support</li>
                            <li>Full Articles and Videos</li>
                            <li>Insider Tips and Tutorials</li>
                        </ul>
                        <h2 class="fs3">REWARDS</h2>
                        <ul>
                            <li>PrintCash Rewards 3%</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-7 mt-lg-5">
                <h2 class="fs1">HEADER FOR UNIVERSE HERE</h2>
                <h2>SUBHEADER FOR WHY CHOOSE UNIVERSE HERE</h2>
                <?php atlas(); ?>
                <a class="btn-primary mt-4" href="/sign-up">SIGN UP</a>
                <img class="mt-5 img-fluid d-block" src="<?php echo CLOUD_URL_Assets; ?>/uploads/solutions/plan-overview-lady.svg">
            </div>
        </div>
    </div>
</section>


<?php get_footer(); ?>