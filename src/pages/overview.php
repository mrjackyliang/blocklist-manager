<?php
/**
 * Block list manager for Pi-hole (pages/overview.php).
 *
 * @since 1.0.0
 */
require_once( 'config.php' );

/**
 * Load Pi-hole raw summary.
 *
 * @since 1.0.0
 */
$pi_hole_raw_summary_json   = file_get_contents( PI_HOLE_URL . '/admin/api.php?summaryRaw' );
$pi_hole_raw_summary_object = json_decode( $pi_hole_raw_summary_json, true );

$pi_hole_raw_summary_total           = ( $pi_hole_raw_summary_object[ 'dns_queries_today' ] ) ? $pi_hole_raw_summary_object[ 'dns_queries_today' ] : 0;
$pi_hole_raw_summary_blocked         = ( $pi_hole_raw_summary_object[ 'ads_blocked_today' ] ) ? $pi_hole_raw_summary_object[ 'ads_blocked_today' ] : 0;
$pi_hole_raw_summary_blocked_percent = ( $pi_hole_raw_summary_object[ 'ads_percentage_today' ] ) ? $pi_hole_raw_summary_object[ 'ads_percentage_today' ] : 0;

/**
 * Load Pi-hole top items.
 *
 * @since 1.0.0
 */
$pi_hole_top_items_json   = file_get_contents( PI_HOLE_URL . '/admin/api.php?topItems=1000000000&auth=' . PI_HOLE_TOKEN );
$pi_hole_top_items_object = json_decode( $pi_hole_top_items_json, true );

$pi_hole_top_items_top_queries = ( $pi_hole_top_items_object[ 'top_queries' ] ) ? $pi_hole_top_items_object[ 'top_queries' ] : [];
$pi_hole_top_items_top_ads     = ( $pi_hole_top_items_object[ 'top_ads' ] ) ? $pi_hole_top_items_object[ 'top_ads' ] : [];

/**
 * Import blocked domains into session.
 *
 * @since 1.0.0
 */
$block_list_domains = load_files_into_array( BLOCK_DIRECTORY, BLOCK_LIST_FILES );
$block_list_cleaned = filter_invalid_lines( $block_list_domains );

/**
 * Import accepted domains into session.
 *
 * @since 1.0.0
 */
$accepted_list_domains = load_files_into_array( ACCEPT_DIRECTORY, ACCEPT_LIST_FILES );
$accepted_list_cleaned = filter_invalid_lines( $accepted_list_domains );

/**
 * Import watched domains into session.
 *
 * @since 1.0.0
 */
$watched_list_domains = load_files_into_array( WATCH_DIRECTORY, WATCH_LIST_FILES );
$watched_list_cleaned = filter_invalid_lines( $watched_list_domains );
?>
<div class="container px-sm-0 px-lg-3 py-3">
	<div class="row">
		<div class="stat-box col-lg mx-3 mb-3 p-3 bg-success text-white overflow-hidden" id="total-queries">
			<p class="stat-text mb-2 font-weight-light">Total Queries</p>
			<h2 class="stat-text mb-0 font-weight-bold"><?php echo number_format( $pi_hole_raw_summary_total, 0, '.', ',' ); ?></h2>
			<i class="stat-icon fas fa-globe-americas" aria-hidden="true"></i>
		</div>
		<div class="stat-box col-lg mx-3 mb-3 p-3 bg-primary text-white overflow-hidden" id="queries-blocked">
			<p class="stat-text mb-2 font-weight-light">Queries Blocked</p>
			<h2 class="stat-text mb-0 font-weight-bold"><?php echo number_format( $pi_hole_raw_summary_blocked, 0, '.', ',' ); ?></h2>
			<i class="stat-icon fas fa-hand-paper" aria-hidden="true"></i>
		</div>
		<div class="stat-box col-lg mx-3 mb-3 p-3 bg-warning text-white overflow-hidden" id="percent-blocked">
			<p class="stat-text mb-2 font-weight-light">Percent Blocked</p>
			<h2 class="stat-text mb-0 font-weight-bold"><?php echo number_format( $pi_hole_raw_summary_blocked_percent, 2, '.', ',' ) . '%'; ?></h2>
			<i class="stat-icon fas fa-chart-pie" aria-hidden="true"></i>
		</div>
		<div class="stat-box col-lg mx-3 mb-3 p-3 bg-danger text-white overflow-hidden" id="domains-count">
			<p class="stat-text mb-2 font-weight-light">Domains on Block List<?php echo ( count( BLOCK_LIST_FILES ) > 1 ) ? 's' : ''; ?></p>
			<h2 class="stat-text mb-0 font-weight-bold">0</h2>
			<i class="stat-icon fas fa-list-alt" aria-hidden="true"></i>
		</div>
	</div>
	<div class="row">
		<div class="col-lg mx-3 mb-3 p-0">
			<ul class="nav nav-pills nav-fill py-3 border-top border-bottom" id="overview-tab" role="tablist">
				<li class="nav-item">
					<a href="#overview-content-allowed-domains" class="nav-link active" id="overview-tab-allowed-domains" data-toggle="pill" role="tab" aria-controls="overview-content-allowed-domains" aria-selected="true">Allowed Domains</a>
				</li>
				<li class="nav-item">
					<a href="#overview-content-blocked-domains" class="nav-link" id="overview-tab-blocked-domains" data-toggle="pill" role="tab" aria-controls="overview-content-blocked-domains" aria-selected="false">Blocked Domains</a>
				</li>
				<li class="nav-item">
					<a href="#overview-content-watchlist-domains" class="nav-link" id="overview-tab-watchlist-domains" data-toggle="pill" role="tab" aria-controls="overview-content-watchlist-domains" aria-selected="false">Watchlist Domains</a>
				</li>
				<li class="nav-item">
					<a href="#overview-content-stagnant-domains" class="nav-link" id="overview-tab-stagnant-domains" data-toggle="pill" role="tab" aria-controls="overview-content-stagnant-domains" aria-selected="false">Stagnant Domains</a>
				</li>
			</ul>
			<div class="tab-content py-3" id="overview-content">
				<div class="tab-pane show active" id="overview-content-allowed-domains" role="tabpanel" aria-labelledby="overview-tab-allowed-domains">
					<div class="table-box table-responsive">
						<table class="domains-list tablesorter-bootstrap table table-sm" id="allowed-domains">
							<thead>
							<tr>
								<th scope="col" class="domain">Domain</th>
								<th scope="col" class="hits border-left">Hits</th>
								<th scope="col" class="resources border-left" data-sorter="false">Resources</th>
							</tr>
							</thead>
							<tbody>
							<?php
							// Remove if domain is accepted.
							$accept_list = array_filter( $pi_hole_top_items_top_queries, function ( $domain ) use ( $accepted_list_cleaned ) {
								if ( strpos_array( $domain, $accepted_list_cleaned ) ) {
									return false;
								}

								return true;
							}, ARRAY_FILTER_USE_KEY );

							foreach ( $accept_list as $domain => $hits ) {
								// Remove if domain is blank and not blocked.
								if ( $domain !== '' && ! in_array( $domain, $block_list_cleaned ) ) {
									echo '<tr><td class="domain"><a href="//' . $domain . '" title="' . $domain . '" target="_blank">' . $domain . '</a></td><td class="hits border-left">' . $hits . '</td><td class="resources border-left">' . get_link_resources( $domain ) . '</td></tr>';
								}
							}
							?>
							</tbody>
						</table>
					</div>
				</div>
				<div class="tab-pane" id="overview-content-blocked-domains" role="tabpanel" aria-labelledby="overview-tab-blocked-domains">
					<div class="table-box table-responsive">
						<table class="domains-list tablesorter-bootstrap table table-sm" id="blocked-domains">
							<thead>
							<tr>
								<th scope="col" class="domain">Domain</th>
								<th scope="col" class="hits border-left">Hits</th>
								<th scope="col" class="resources border-left" data-sorter="false">Resources</th>
							</tr>
							</thead>
							<tbody>
							<?php
							foreach ( $pi_hole_top_items_top_ads as $domain => $hits ) {
								// Remove if domain is blank and not blocked.
								if ( $domain !== '' && ! in_array( $domain, $block_list_cleaned ) ) {
									echo '<tr><td class="domain"><a href="//' . $domain . '" title="' . $domain . '" target="_blank">' . $domain . '</a></td><td class="hits border-left">' . $hits . '</td><td class="resources border-left">' . get_link_resources( $domain ) . '</td></tr>';
								}
							}
							?>
							</tbody>
						</table>
					</div>
				</div>
				<div class="tab-pane" id="overview-content-watchlist-domains" role="tabpanel" aria-labelledby="overview-tab-watchlist-domains">
					<div class="table-box table-responsive">
						<table class="domains-list tablesorter-bootstrap table table-sm" id="watchlist-domains">
							<thead>
							<tr>
								<th scope="col" class="domain">Domain</th>
								<th scope="col" class="resources border-left" data-sorter="false">Resources</th>
							</tr>
							</thead>
							<tbody>
							<?php
							$both_lists = array_unique( array_keys( array_merge( $pi_hole_top_items_top_queries, $pi_hole_top_items_top_ads ) ) );

							// Remove if domain is accepted.
							$accept_list = array_filter( $both_lists, function ( $domain ) use ( $accepted_list_cleaned ) {
								if ( strpos_array( $domain, $accepted_list_cleaned ) ) {
									return false;
								}

								return true;
							});

							// Remove if domain is not on watch list.
							$watch_list = array_filter( $accept_list, function ( $domain ) use ( $watched_list_cleaned ) {
								if ( strpos_array( $domain, $watched_list_cleaned ) ) {
									return true;
								}

								return false;
							});

							foreach ( $watch_list as $key => $domain ) {
								// Remove if domain is blank and not blocked.
								if ( $domain !== '' && ! in_array( $domain, $block_list_cleaned ) ) {
									echo '<tr><td class="domain"><a href="//' . $domain . '" title="' . $domain . '" target="_blank">' . $domain . '</a></td><td class="resources border-left">' . get_link_resources( $domain ) . '</td></tr>';
								}
							}
							?>
							</tbody>
						</table>
					</div>
				</div>
				<div class="tab-pane" id="overview-content-stagnant-domains" role="tabpanel" aria-labelledby="overview-tab-stagnant-domains">
					<div class="table-box table-responsive">
						<table class="domains-list tablesorter-bootstrap table table-sm" id="stagnant-domains">
							<thead>
							<tr>
								<th scope="col" class="domain">Domain</th>
								<th scope="col" class="resources border-left" data-sorter="false">Resources</th>
							</tr>
							</thead>
							<tbody>
							<?php
							foreach ( $block_list_cleaned as $key => $domain ) {
								// Remove if domain is blocked.
								if ( ! in_array( $domain, array_keys( $pi_hole_top_items_top_ads ) ) ) {
									echo '<tr><td class="domain"><a href="//' . $domain . '" title="' . $domain . '" target="_blank">' . $domain . '</a></td><td class="resources border-left">' . get_link_resources( $domain ) . '</td></tr>';
								}
							}
							?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
