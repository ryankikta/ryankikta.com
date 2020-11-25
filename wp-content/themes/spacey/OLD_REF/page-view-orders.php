<?php
global $wpdb;
$current_user = wp_get_current_user();
$current_user_id = $current_user->ID;

//The possible statues that an order can have
$order_status = array("New", "Processing", "Pending", "Canceled", "Shipped", "On Hold");

//Only show if user is logged in
if (0 == $current_user_id) {
    wp_redirect("/login");
    exit();
}

get_header();
?>

<div class="container-fluid dashboard_content dashboard">
    <div class="row">
        <?php include('sidebar.php'); ?>
        <div class="col py-40 mx-auto">
            <h1 class="fs2"><svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="120" height="120" viewBox="0 0 120 120"><ellipse style="opacity:0.5; fill:#19CBC5;" cx="60" cy="60" rx="60" ry="60"/><g transform="translate(14.374 14.374)"><ellipse style="fill:#19CBC5;" cx="45.6" cy="45.6" rx="45.6" ry="45.6"/></g><g transform="translate(59.729 22.971) rotate(31)"><path style="fill:none;stroke:#361181;stroke-width:2;" d="M40.8,37.6C37.4,41,29,37.5,20.7,29.3S9,12.5,12.4,9.1s11.9,0.1,20.1,8.3S44.2,34.2,40.8,37.6z M31.1,46.7c-1.5-0.6-3.2,0.8-4.4,0.5c-1.9-0.6-2.3-0.9-3.9-1.4c-1-0.6-2.3-0.3-3.1,0.5L16,50c3.9,1.3,9.3,8,12.5,4.7l3.5-3.9C33.4,49.5,34.3,48,31.1,46.7L31.1,46.7z M18.7,39.8c-4.2-1.1-7.4-4.4-8.5-8.6c1.6-2.4,2.5-5.1,2.8-8c-1.3-1.9-2.3-4-3-6.1C10,21,10.4,26.5,4.6,32.2l-3.6,3.6c-2.2,2.2-1,6.9,2.6,10.5c3.6,3.6,8.3,4.8,10.5,2.6l3.6-3.6l0,0c5.7-5.7,11-5.5,14.9-5.5c-2.1-0.7-4-1.7-5.9-2.9C23.9,37.2,21.1,38.2,18.7,39.8L18.7,39.8z M27.6,22.3c-2.5-2.5-4.8-1.4-5.7-0.7c0.9,1.1,1.9,2.2,3.1,3.3c1.1,1.1,2.2,2.1,3.3,3C29.1,27.2,30.1,24.8,27.6,22.3L27.6,22.3z M37.2,14.7c-0.6,0.5-1.5,0.5-2-0.1c-0.5-0.5-0.5-1.3,0-1.8c0,0,4.4-4.9,4.4-4.9c0.5-0.5,1.4-0.5,2,0c0,0,0,0,0,0l0.5,0.5c0.5,0.5,0.5,1.4,0,2C42.1,10.3,37.2,14.7,37.2,14.7L37.2,14.7z M28,8c-0.4,0.7-1.2,0.9-1.9,0.5c-0.6-0.4-0.9-1.1-0.6-1.7c0,0,2.6-6,2.6-6c0.3-0.7,1.2-1,1.9-0.6l0.7,0.3c0.7,0.3,1,1.2,0.6,1.9c0,0,0,0,0,0C31.3,2.4,28,8,28,8L28,8z M41.9,21.9c-0.7,0.4-0.9,1.2-0.5,1.9c0.4,0.6,1.1,0.8,1.7,0.6l6-2.6c0.7-0.3,1-1.2,0.6-1.9l-0.3-0.7c-0.3-0.7-1.2-1-1.9-0.6c0,0,0,0,0,0C47.6,18.7,41.9,21.9,41.9,21.9L41.9,21.9z M16,12.8c-1.1,2.9,3.2,9.2,7.6,13.6c3.9,3.9,10.5,8.8,13.6,7.6c1.1-2.9-3.2-9.2-7.6-13.6C25.8,16.4,19.2,11.5,16,12.8z"/></g></svg>Orders</h1>

	<?php
	//Get everything needed to let the user know about their 'ON HOLD' orders
	$query = "select u.balance, count(o.order_id) as onhold_count, sum(o.order_total) as onhold_total from wp_users u
   		left join wp_rmproductmanagement_orders o on u.id = o.user_id
    		where id = 33599
		and o.status = 'ON HOLD'";
	$result = $wpdb->get_row($query, ARRAY_A);
	$balance = $result['balance'];
	$onhold_count = $result['onhold_count'];
	$onhold_total = $result['onhold_total'];
	$difference = $onhold_total - $balance;

	if($onhold_count > 0): 
	?>
        	<div class="message message_error text-center my-20">
                <p class="fs3 mb-0">You currently have <?php echo $onhold_count; ?> order<?php if ($onhold_count > 1) { echo "s"; } ?> for $<?php echo number_format($onhold_total, 2, '.', ''); ?> on hold. Deposit $<?php echo number_format($difference, 2, '.', ''); ?> to submit your orders for processing. <a href="/billing/"> Click here </a> to make a Deposit.</p>
            </div>
            <?php endif; ?>


		<form class="d-flex flex-row justify-content-between align-items-start mt-40" action="" method="get">
		<input class="input_grey" type="text" id="txt_search" name="txt_search" value="" placeholder="Order Number"/>
                <select name="ord_filter" id="ord_filter">
                    <option vlaue=''>All</option>
                <?php
                foreach($order_status as $status){        
                	echo "     
                            <option value='$status'>$status</option>
                        ";
                      }
                ?>
                </select>
                <input type="submit" class="btn-primary" />
            </form>

            <table class="view_orders_table">
                <thead> 
                    <tr>
                        <th class="manage-column" scope="col">Order Number</th>
                        <th class="manage-column" scope="col">Seller</th>                   
                        <th class="manage-column" scope="col">Order Date</th>
                        <th class="manage-column" scope="col">Order Time</th>                   
                        <th class="manage-column" scope="col">Ship Location</th>
                        <th class="manage-column" scope="col">QTY</th>
                        <th class="manage-column" scope="col">Order Total</th>
                        <!--<th class="manage-column" scope="col">Processed</th>
                        <th class="manage-column" scope="col">Shipped Date</th>-->
                        <th class="manage-column" scope="col">Status</th>
                        <th class="manage-column" scope="col">&nbsp;</th>
                    </tr>
                </thead>
		<tbody>
		<?php
		//Get the form results
		$status_filter = (isset($_GET['ord_filter']) && in_array($_GET['ord_filter'], $order_status)) ? $_GET['ord_filter'] : '';
		$txt_search = (isset($_GET['txt_search'])) ? sanitize_text_field($_GET['txt_search']) : '';

		//pagination setup
		$page = (isset($_GET['pagenum']) && is_numeric($_GET['pagenum']) ) ? $_GET['pagenum'] : 1;
		$limit = 50; //Amount of orders per page
		$offset = ($page - 1) * $limit;
		$total_orders = $wpdb->get_var("select count(o.order_id) from wp_rmproductmanagement_orders o
    						where o.user_id = $current_user_id
    						and lower(o.status) like lower('%$status_filter%')
						and lower(orderid) like lower('%$txt_search%')");

		//Get all of the orders for the user
		$query = "select o.order_id, o.orderid, o.shop, date_format(o.created_at,'%m/%d/%Y') as order_date, date_format(o.created_at,'%h:%i %p') as order_time, o.shippingaddress,o.order_total,o.status,sum(od.quantity) as quantity 
			from wp_rmproductmanagement_orders o
    			left join wp_rmproductmanagement_order_details od on od.order_id = o.order_id
    			where o.user_id = $current_user_id
    			and lower(o.status) like lower('%$status_filter%')
    			and lower(orderid) like lower('%$txt_search%')
    			group by od.order_id
  	 		order by o.created_at desc
    			limit $limit
			offset $offset";	

		//Loop through and dispaly all of the orders for the user
		$orders = $wpdb->get_results($query, ARRAY_A);
		foreach($orders as $order){
			//Coloring for the Order statues
			switch ($order['status']){
				case 'ON HOLD':
					$style = 'color:#F39C12';
					break;
				case 'Shipped':
					$style = 'color:#00A65A';
					break;
				case 'Canceled':
					$style = 'color:#DD4B39';
					break;
				default:
					$style = '';
                              }

			echo "<tr>
			<th scope='row'>" . $order['orderid'] . "</td>
			<td>" . $order['shop'] . "</td>
			<td>" . $order['order_date'] . "</td>
			<td>" . $order['order_time'] . "</td>
			<td>" . $order['shippingaddress'] . "</td>
			<td>" . $order['quantity'] . "</td>
			<td>$" . $order['order_total'] . "</td>
			<td style=" . $style . ";font-weight:600>" . $order['status'] . "</td>
			<td> <a href='' class='edit'>Edit</a> <a href='' class='view'>View</a></td>	
			</tr>";	
		}
		?>			
					
                    <!--tr class="alternate">
                        <th scope="row">#1078-replacement</th>
                        <td>Brand New Muscle Car</td>
                        <td>09/02/2020</td>
                        <td>10:08 AM</td>
                        <td>Benny Broadway
                        7560 County Road 466
                        Princeton , Texas 75407-2248
                        United States</td>
                        <td>1</td>
                        <td>20.50</td>
                        <td>&nbsp;New</td>
                        <td><a href="?page=inventory-orders&amp;action=edit&amp;order_id=4918936" class="edit">Edit</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="?page=inventory-orders&amp;action=view&amp;order_id=4918936" class="edit">View</a></td>
                    </tr>
                    <tr class="">
                        <th scope="row">#1078-replacement</th>
                        <td>Brand New Muscle Car</td>
                        <td>09/02/2020</td>
                        <td>10:08 AM</td>
                        <td>Benny Broadway
                        7560 County Road 466
                        Princeton , Texas 75407-2248
                        United States</td>
                        <td>1</td>
                        <td>20.50</td>
                        <td>&nbsp;New</td>
                        <td><a href="?page=inventory-orders&amp;action=edit&amp;order_id=4918936" class="edit">Edit</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="?page=inventory-orders&amp;action=view&amp;order_id=4918936" class="edit">View</a></td>
                    </tr>
                    <tr class="">
                        <th scope="row">#1078-replacement</th>
                        <td>Brand New Muscle Car</td>
                        <td>09/02/2020</td>
                        <td>10:08 AM</td>
                        <td>Benny Broadway
                        7560 County Road 466
                        Princeton , Texas 75407-2248
                        United States</td>
                        <td>1</td>
                        <td>20.50</td>
                        <td>&nbsp;New</td>
                        <td><a href="?page=inventory-orders&amp;action=edit&amp;order_id=4918936" class="edit">Edit</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="?page=inventory-orders&amp;action=view&amp;order_id=4918936" class="edit">View</a></td>
                    </tr-->
                </tbody>
            </table>
        </div>
    </div>   
</div> 
		<?php
		echo getPaginationString($page, $total_orders, 30, 4, "/view-orders/", '?txt_search='. $txt_saerch . '&ord_filter=' . $status_filter . '&pagenum=');
		?>
<?php get_footer(); ?>
    
