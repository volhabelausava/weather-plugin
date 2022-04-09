<div class="wrap">
    <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
<form action="options.php" method="post">
    <?php
    settings_fields( \Weather\Admin\Configuration::OPTION_GROUP);
    do_settings_sections( \Weather\Admin\Configuration::OPTION_GROUP);
    submit_button( 'Save Changes' );
    ?>
</form>
</div>