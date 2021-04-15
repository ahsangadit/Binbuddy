<?php


// admin_head is a hook my_custom_fonts is a function we are adding it to the hook
add_action('admin_head', 'my_custom_fonts');
function my_custom_fonts()
{
	/*ads-sidebar*/
	/*popup-open-menu*/
	echo '<style>
        table.watchlist-table {
            border-collapse: collapse;
            border: 1px solid;
            width: 800px;
            text-align: center;
        }
        table.watchlist-table th {
            padding: 10px;
            color: #fff;
            background-color: #212529;
            border-color: #32383e;
        }
        table.watchlist-table td {
            border: 1px solid #c3c3c4;
            padding: .75rem;
            vertical-align: middle;
            border-top: 1px solid #dee2e6;
            font-size: 15px;
        }
        
        .image-product-auction{
            width: 100px;
        }
        
        .img-section span{
            display: block;
        }
        
        td.serial-no{
            font-weight: 700;
        }
        table.watchlist-table tr {
            background-color: #f9f9f9;
        }
        
        .pagination {
            display: inline-block;
            padding-left: 0;
            margin: 20px 0;
            border-radius: 4px;
        }
        
        .pagination>li {
            display: inline;
        }
        
        .pagination>li:first-child>a, .pagination>li:first-child>span {
            margin-left: 0;
            border-top-left-radius: 4px;
            border-bottom-left-radius: 4px;
        }
        
        .pagination>li>a, .pagination>li>span {
            position: relative;
            float: left;
            padding: 6px 12px;
            margin-left: -1px;
            line-height: 1.42857143;
            color: #337ab7;
            text-decoration: none;
            background-color: #fff;
            border: 1px solid #ddd;
        }
        
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0,0,0,0);
            border: 0;
        }
        
        .pagination>li>a, .pagination>li>span {
            position: relative;
            float: left;
            padding: 6px 12px;
            margin-left: -1px;
            line-height: 1.42857143;
            color: #337ab7;
            text-decoration: none;
            background-color: #fff;
            border: 1px solid #ddd;
        }
        
        .pagination>.active>a, .pagination>.active>a:focus, .pagination>.active>a:hover, .pagination>.active>span, .pagination>.active>span:focus, .pagination>.active>span:hover {
            z-index: 3;
            color: #fff;
            cursor: default;
            background-color: #337ab7;
            border-color: #337ab7;
        }
        
        .maxRow-section{
            margin-bottom: 10px;
        }
        
        .pagination li {
            cursor: pointer;
        }
        
  </style>';


	echo '<script>
    jQuery(function($){
 
        //        $("select#maxRows").change(function(){
        //            var selectedCountry = jQuery(this).children("option:selected").val();
        //          
        //        });
                
        getPagination("#auction-watcher-table");
        function getPagination(table) {
          var lastPage = 1;
        
          $("#maxRows")
            .on("change", function(evt) {
              //$(\'.paginationprev\').html(\'\');						// reset pagination
        
             lastPage = 1;
              $(".pagination")
                .find(\'li\')
                .slice(1, -1)
                .remove();
              var trnum = 0; // reset tr counter
              var maxRows = parseInt($(this).val()); // get Max Rows from select option
        
              if (maxRows == 5000) {
                $(".pagination").hide();
              } else {
                $(".pagination").show();
              }
        
              var totalRows = $(table + " tbody tr ").length; // numbers of rows
              $(table + " tr:gt(0) ").each(function() {
                // each TR in  table and not the header
                trnum++; // Start Counter
                if (trnum > maxRows) {
                  // if tr number gt maxRows
        
                  $(this).hide(); // fade it out
                }
                if (trnum <= maxRows) {
                  $(this).show();
                } // else fade in Important in case if it ..
              }); //  was fade out to fade it in
              if (totalRows > maxRows) {
                // if tr total rows gt max rows option
                var pagenum = Math.ceil(totalRows / maxRows); // ceil total(rows/maxrows) to get ..
                //	numbers of pages
                for (var i = 1; i <= pagenum; ) {
                  // for each page append pagination li
                  $(".pagination #prev")
                    .before(
                      \'<li data-page="\' +
                        i +
                        \'">\
                                          <span>\' +
                        i++ +
                        \'<span class="sr-only">(current)</span></span>\
                                        </li>\'
                    )
                    .show();
                } // end for i
              } // end if row count > max rows
              $(\'.pagination [data-page="1"]\').addClass("active"); // add active class to the first li
              $(\'.pagination li\').on(\'click\', function(evt) {
                // on click each page
                evt.stopImmediatePropagation();
                evt.preventDefault();
                var pageNum = $(this).attr("data-page"); // get it\'s number
        
                var maxRows = parseInt($("#maxRows").val()); // get Max Rows from select option
        
                if (pageNum == "prev") {
                  if (lastPage == 1) {
                    return;
                  }
                  pageNum = --lastPage;
                }
                if (pageNum == "next") {
                  if (lastPage == $(".pagination li").length - 2) {
                    return;
                  }
                  pageNum = ++lastPage;
                }
        
                lastPage = pageNum;
                var trIndex = 0; // reset tr counter
                $(\'.pagination li\').removeClass(\'active\'); // remove active class from all li
                $(\'.pagination [data-page="\' + lastPage + \'"]\').addClass(\'active\'); // add active class to the clicked
                // $(this).addClass(\'active\');					// add active class to the clicked
                limitPagging();
                $(table + \' tr:gt(0)\').each(function() {
                  // each tr in table not the header
                  trIndex++; // tr index counter
                  // if tr index gt maxRows*pageNum or lt maxRows*pageNum-maxRows fade if out
                  if (
                    trIndex > maxRows * pageNum ||
                    trIndex <= maxRows * pageNum - maxRows
                  ) {
                    $(this).hide();
                  } else {
                    $(this).show();
                  } //else fade in
                }); // end of for each tr in table
              }); // end of on click pagination list
              limitPagging();
            })
            .val(5)
            .change();
        
          // end of on select change
        
          // END OF PAGINATION
        }
        function limitPagging(){
            // alert($(\'.pagination li\').length)
            if($(\'.pagination li\').length > 7 ){
                    if( $(\'.pagination li.active\').attr(\'data-page\') <= 3 ){
                    $(\'.pagination li:gt(5)\').hide();
                    $(\'.pagination li:lt(5)\').show();
                    $(\'.pagination [data-page="next"]\').show();
                }if ($(\'.pagination li.active\').attr(\'data-page\') > 3){
                    $(\'.pagination li:gt(0)\').hide();
                    $(\'.pagination [data-page="next"]\').show();
                    for( let i = ( parseInt($(\'.pagination li.active\').attr(\'data-page\'))  -2 )  ; i <= ( parseInt($(\'.pagination li.active\').attr(\'data-page\'))  + 2 ) ; i++ ){
                        $(\'.pagination [data-page="\'+i+\'"]\').show();
                    }
                }
            }
        }
        $(function() {
          // Just to append id number for each row
          $(\'table#auction-watcher-table  tr:eq(0)\').prepend(\'<th> ID </th>\');
          var id = 0;
          $(\'table#auction-watcher-table  tr:gt(0)\').each(function() {
            id++;
            $(this).prepend(\'<td>\' + id + \'</td>\');
          });
        });
        
    });
 
   </script>';

}

function uniqueAssocArray($array, $uniqueKey) {
	$uniqueKeys = array();

	foreach ($array as $key => $item) {
		$groupBy = $item[$uniqueKey];

		if (isset( $uniqueKeys[$groupBy] ))
		{
			$replace= $item;
        }
		else{
			$replace=$item;
		}
		if ($replace) $uniqueKeys[$groupBy] = $item;

	}
	return $uniqueKeys;
}


add_shortcode('woo_sales_coupon', 'woo_sales_coupon_fun');
function woo_sales_coupon_fun()
{

	$args = [
		'post_type'      => 'shop_order',
		'posts_per_page' => '-1',
		'post_status'    => ['wc-processing', 'wc-completed']
	];

	$my_query    = new WP_Query($args);
	$orders      = $my_query->posts;
	$total       = 0;
	$coupondata  = [];

	$args = array(
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'asc',
		'post_type'      => 'shop_coupon',
		'post_status'    => 'publish',
	);

	$coupons = get_posts($args);
	foreach ($coupons as $key => $coupon) {
		$coupondata [] = strtolower($coupon->post_title);
	}

	$total_coupons_data = [];

	$n = 0;
	foreach ($orders as $key => $value) {

		$order_id  = $value->ID;
		$order     = wc_get_order($order_id);
		$items     = $order->get_items('coupon');

		foreach ($items as $k => $item) {
			if (in_array($item['code'], $coupondata)) {
				$total += $order->get_total();
				$total_coupons_data [$n]['code'] = $item['code'];
				$total_coupons_data [$n]['total'] = wc_price($total);
				$n++;
			}
		}
	}

	$data = uniqueAssocArray($total_coupons_data,'code');
	ob_start();
	?>
	<div class="row">
		<div class="col-md-6">

			<div class="maxRow-section">
				<span>Select Rows:</span>
				<select class="form-control" name="state" id="maxRows">
					<option value="5000">Show ALL Rows</option>
					<option value="5">5</option>
					<option value="10">10</option>
					<option value="15">15</option>
					<option value="20">20</option>
					<option value="50">50</option>
					<option value="70">70</option>
					<option value="100">100</option>
				</select>
			</div>

			<table class="watchlist-table" id="auction-watcher-table">
				<thead>
				<tr>
					<th>Coupon Code</th>
					<th>Total</th>
				</tr>
				</thead>
				<tbody>

				<?php
				$i = 0;

				foreach ($data as $k => $val){
					?>
					<tr>
						<td><?= $val['code'] ?></td>
						<td>
							<?= $val['total'] ?>
						</td>
					</tr>
					<?php
				}


				?>

				</tbody>
			</table>

			<!--		Start Pagination -->
			<div class='pagination-container'>
				<nav>
					<ul class="pagination">

						<li data-page="prev">
							<span> < <span class="sr-only">(current)</span></span>
						</li>
						<!--	Here the JS Function Will Add the Rows -->
						<li data-page="next" id="prev">
							<span> > <span class="sr-only">(current)</span></span>
						</li>
					</ul>
				</nav>
			</div>


		</div>
	</div>
	<?php
	$html = ob_get_clean();
	return $html;
}


function register_sales_coupons()
{
	add_submenu_page('woocommerce', 'Sales By Coupon', 'Coupon Sales', 'manage_options', 'sales-by-coupon', 'sales_coupon_callback');
}

function sales_coupon_callback()
{
	echo '<h3>Sales Coupons</h3>';
	echo do_shortcode('[woo_sales_coupon]');
}

add_action('admin_menu', 'register_sales_coupons', 99);
