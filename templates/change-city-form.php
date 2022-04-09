<h3 class="aligncenter">Change city</h3>

<form method="POST" action="<?php echo admin_url('admin-post.php'); ?>">
    <label>
        <span>City name</span>
        <input type="text" name="city" value="<?php echo $defaultCity; ?>">
    </label>
    <input type="hidden" name="action" value="change_city">
    <input type="hidden" name="redirect" value="<?php echo $redirect; ?>">
    <input type="submit" value="Change city">
</form>