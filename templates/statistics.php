<h3 class="aligncenter">Weather statistics at <?php echo $cityName; ?></h3>
<p><a href="<?php echo home_url( '/' . \Weather\Service\PagesManager::CHANGE_CITY_SLUG); ?>">Change city</a></p>
<p><a href="<?php echo home_url( '/' . \Weather\Service\PagesManager::WEATHER_SLUG); ?>">Current weather</a></p>
<?php if (empty($statisticsData)): ?>
    <p>There is no data.</p>
<?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Temperature</th>
                <th>"Feels like" temperature</th>
                <th>Humidity</th>
                <th>Wind speed</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($statisticsData as $rowData) : ?>
            <tr>
                <td><?php echo $rowData->created; ?></td>
                <td><?php echo $rowData->temperature; ?> &deg;C</td>
                <td><?php echo $rowData->feels_like; ?> &deg;C</td>
                <td><?php echo $rowData->humidity; ?>%</td>
                <td><?php echo $rowData->wind_speed; ?> m/s</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>