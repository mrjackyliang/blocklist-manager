<?php
/**
 * Block list manager for Pi-hole (tools/overview.php).
 *
 * @since 1.0.0
 */
require_once( 'config.php' );
require_once( 'src/functions.php' );

/**
 * Import blocked domains into session.
 *
 * @since 1.0.0
 */
$block_list_domains = load_files_into_array( BLOCK_DIRECTORY, BLOCK_LIST_FILES );
$block_list_cleaned = filter_invalid_lines( $block_list_domains );
?>
<script type="text/javascript">
  let blockedDomainsCount = <?php echo count( $block_list_cleaned ); ?>;

  // Replace "#domains-count" statistics.
  jQuery(($) => {
    $('#domains-count h2').text(blockedDomainsCount.toLocaleString('en-US'));
  });

  // Sort the tables.
  jQuery(($) => {
    $('.domains-list').tablesorter();
  });

  // Save tab location on page refresh.
  jQuery(($) => {
    $('a[data-toggle="pill"]').on('show.bs.tab', function (event) {
      localStorage.setItem('activeTab', $(event.target).attr('href'));
    });

    let activeTab = localStorage.getItem('activeTab');

    if (activeTab) {
      $('#overview-tab a[href="' + activeTab + '"]').tab('show');
    }
  });
</script>
