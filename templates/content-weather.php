<h3 class="aligncenter"><?php echo $title; ?></h3>
<p>Temperature: <?php echo $temperature; ?> &deg;C</p>
<p>Feels like: <?php echo $feelsLikeTemperature; ?> &deg;C</p>
<p>Humidity: <?php echo $humidity; ?> %</p>
<p>Wind speed: <?php echo $windSpeed; ?> m/s</p>

<div class="entry-footer default-max-width">
    <p><a href="<?php echo home_url( '/' . \Weather\Service\PagesManager::CHANGE_CITY_SLUG); ?>">Change city</a></p>
    <p><a href="<?php echo home_url( '/' . \Weather\Service\PagesManager::WEATHER_STATISTICS_SLUG); ?>">Get statistics</a></p>
    <?php if (current_user_can('manage_options')): ?>
        <p><a href="<?php echo home_url( '/' . \Weather\Service\PagesManager::SAVE_WEATHER_SLUG); ?>">Save data</a></p>
    <?php endif; ?>
</div>

