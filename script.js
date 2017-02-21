jQuery(document).ready(function() {
    var post_entries_count = jQuery('.post-entries').length;
    if(post_entries_count > 1)
        jQuery( "#post-entries" ).first().css( "display", "none" );
});
