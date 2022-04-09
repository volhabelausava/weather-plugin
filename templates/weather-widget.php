<?php
    echo $args['before_widget'];
    echo $args['before_title'];
    echo $title;
    echo $args['after_title'];
?>
    <p>Temperature: <?php echo $temperature; ?> &deg;C</p>
    <a href="<?php echo home_url( '/' . \Weather\Service\PagesManager::CHANGE_CITY_SLUG); ?>">Change city</a>
<?php
    echo $args['after_widget'];
