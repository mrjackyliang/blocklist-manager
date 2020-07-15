<?php
/**
 * Block list manager for Pi-hole (tools/editor.php).
 *
 * @since 1.0.0
 */
$file_has_saved = false;

if ( isset( $_POST[ 'file-contents' ] ) ) {
	$file_has_saved = true;
}
?>
<script type="text/javascript">
  let promptBeforeLeaving = false;
  let formSubmitted = false;
  let fileHasSaved = <?php echo ( $file_has_saved ) ? 'true' : 'false'; ?>;

  // If user forgets to save, show browser warning.
  jQuery(($) => {
    $('textarea').on('input propertychange paste', () => {
      if (promptBeforeLeaving !== true) {
        promptBeforeLeaving = true;
      }
    });

    $('form').submit(() => {
      formSubmitted = true;
    });

    $(window).bind('beforeunload', (event) => {
      if (promptBeforeLeaving === true && formSubmitted !== true) {
        let message = 'You have unsaved changes. Are you sure you want to leave this page?';
        event.returnValue = message;
        return message;
      }
    });
  });

  // Show message if editing or just saved.
  jQuery(($) => {
    if (fileHasSaved) {
      $('#file-saved').removeClass('d-none');
      $('#file-edit').addClass('d-none');
      setTimeout(() => {
        $('#file-edit').removeClass('d-none');
        $('#file-saved').addClass('d-none');
      }, 5000);
    }
  });
</script>
