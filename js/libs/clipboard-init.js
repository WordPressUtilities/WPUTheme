jQuery(document).ready(function($) {
    var clipboard = new ClipboardJS('[data-clipboard],[data-clipboard-text]');
    jQuery('[data-clipboard],[data-clipboard-text]').on('click', function(e) {
        e.preventDefault();
    });
    clipboard.on('success', function(e) {
        alert(wputh_clipboard_init_js.txt_copied);
    });
});
