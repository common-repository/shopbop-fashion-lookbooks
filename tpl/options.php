<div class="wrap">
    <h2>SocialRoot - ShopBop Plugin Settings</h2>
    <form method="post" action="options.php">
        <?php settings_fields('sp_digitallylux_options'); ?>
        <?php $options = get_option('sp_digitallylux_options'); ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">
                    Enter your provided SocialRoot username
                </th>
                <td>
                    <input name="sp_digitallylux_options[name]" type="text" value="<?=$options['name']?>"/>
                </td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
    </form>
</div>

<br/>

<div class="wrap">    
    <h3>Don't have an account?</h3>
    Apply for one here: <a href="https://www.digitallylux.com/publishers/" target="_blank">SocialRoot</a> Publisher Program
</div>
