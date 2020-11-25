<?php
/********************************************Login Form***********************************************/
function login_form()
{
    ?>
    <div id="wpmem_login">
        <form class="form" id="" method="POST" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
            <h3>Existing Users Log In</h3>
            <p><label for="log" class="text">Username</label>
            </p>
            <div class="div_text"><input type="text" class="username" value="" id="log" name="login_name"></div>
            <p><label for="pwd" class="text">Password</label>
            </p>
            <div class="div_text"><input type="password" class="password" id="pwd" name="login_password"></div>
            <p><input type="hidden" value="1" name="login">
            </p>
            <div class="button_div"><input type="submit" class="buttons" value="Login" name="login"></div>
            <div class="clear"></div>
            <div align="right" class="link-text">Forgot password?&nbsp;<a href="/my-profile/?a=pwdreset"
                                                                          target="_blank">Click here to reset</a></div>
            <div class="clearfix"></div>
        </form>
        <br class="clearfix">
    </div>
    <br class="clear">
    <div style="width:630px;text-align: center;font-size:30px;"> OR</div>
    <br class="clearfix">
    <?php
}

function login_auth($username, $password)
{
    global $user;
    $creds = array();
    $creds['user_login'] = $username;
    $creds['user_password'] = $password;
    $creds['remember'] = true;
    $user = wp_signon($creds, false);
    if (is_wp_error($user)) {
        ?>
        <div class="errors_apps"
             style="width: 50%; padding: 10px; margin: 0px 40px 20px; border: 1px solid red; background-color: rgb(255, 235, 232);">
            <strong>ERROR</strong>
            : The username or password you entered is incorrect.
        </div>
        <?php
    } else {
        $redirect = $_SERVER['REQUEST_URI'];
        wp_redirect($redirect);
    }
}

function login_process()
{
    if (isset($_POST['login'])) {
        login_auth($_POST['login_name'], $_POST['login_password']);
    }
    unset($_SESSION['errors_big']);
    login_form();
}

function login_shortcode()
{
    ob_start();
    login_process();
    return ob_get_clean();
}

add_shortcode('big_login_form', 'login_shortcode');

/*********************************************Register Form********************************************************/
function register_form($data = array())
{
    @extract($data);
    ?>
    <div id="wpmem_reg">
        <form class="form" id="" action="<?php echo $_SERVER['REQUEST_URI']; ?>" method="post" name="form">
            <h3>Become A Member</h3>

            <label class="text" for="user_login">Username<span class="req">*</span></label>
            <div class="div_text">
                <input type="text" required="" class="textbox"
                       value="<?php echo(isset($_POST['user_login']) ? $_POST['user_login'] : $user_name); ?>"
                       id="user_login" name="user_login">
            </div>

            <label class="text" for="full_name">Full Name<span class="req">*</span></label>
            <div class="div_text">
                <input type="text" required="" class="textbox"
                       value="<?php echo(isset($_POST['full_name']) ? $_POST['full_name'] : $full_name); ?>"
                       id="full_name" name="full_name">
            </div>

            <label class="text" for="user_email">Email<span class="req">*</span></label>
            <div class="div_text">
                <input type="text" required="" class="textbox"
                       value="<?php echo(isset($_POST['user_email']) ? $_POST['user_email'] : $user_email); ?>"
                       id="user_email" name="user_email">
            </div>

            <label class="text" for="phone_number">Phone Number</label>
            <div class="div_text">
                <input type="text" class="textbox"
                       value="<?php echo(isset($_POST['phone_number']) ? $_POST['phone_number'] : $phone); ?>"
                       id="phone_number" name="phone_number">
            </div>

            <label class="text" for="business_name">Business Name</label>
            <div class="div_text">
                <input type="text" class="textbox"
                       value="<?php echo(isset($_POST['business_name']) ? $_POST['business_name'] : $full_name); ?>"
                       id="business_name" name="business_name">
            </div>

            <label class="text" for="user_url">Website</label>
            <div class="div_text">
                <input type="text" class="textbox"
                       value="<?php echo(isset($_POST['user_url']) ? $_POST['user_url'] : $user_url); ?>" id="user_url"
                       name="user_url">
            </div>

            <div class="div_checkbox">
                <input type="checkbox" checked="checked" value="agree" id="tos" name="tos">
                <label class="checkbox" for="tos">Agree to <a
                            onclick="window.open('https://ryankikta.com/wp-content/plugins/wp-members//wp-members-tos.php','mywindow');"
                            href="#"> TOS </a><span class="req">*</span></label>
            </div>

            <div class="div_checkbox">
                <input type="checkbox" checked="checked" value="1" id="getting_started_newsletter"
                       name="getting_started_newsletter">
                <label class="checkbox" for="getting_started_newsletter">Receive our Weekly Newsletter</label>
            </div>

            <div class="div_checkbox">
                <input type="checkbox" checked="checked" value="1" id="getting_started_guide"
                       name="getting_started_guide">
                <label class="checkbox" for="getting_started_guide">Receive the Getting Started Guide</label>
            </div>

            <div class="clear"></div>
            <input type="hidden" value="1" name="register"/>
            <div class="button_div">
                <input type="reset" class="buttons" value="Clear Form" name="reset">
                <input type="submit" class="buttons" value="Sign Me Up" name="submit">
            </div>
            <div class="clear"></div>

            <div class="req-text">
                <span class="req">*</span>Required field
            </div>
        </form>
    </div>
    <!------------Right Sidebar------>
    <div class="right_sidebar">
        <h3>Member Benefits</h3>
        <div class="content_desc">Ryan Kikta prints your designs on a variety of products and ships them to your
            customers under your brand,automatically! Sign up,upload your designs, integrate your shop, and start
            profiting from your art today!
        </div>
        <br>
        <ul>
            <li>No Membership Fees</li>
            <li>No Order Minimums</li>
            <li>Branding Options</li>
            <li>100's of Products</li>
            <li>High Quality Prints</li>
            <li>Automated Order Fulfillment</li>
        </ul>
    </div>
    <br class="clear">
    <?php
}

function validation($username, $fullname, $email, $tos = "")
{

    global $reg_errors;
    $reg_errors = new WP_Error;
    if (empty($username) || empty($fullname) || empty($email)) {
        $reg_errors->add('field', 'Required form field is missing');
    }
    if (username_exists($username)) {
        $reg_errors->add('user_name', 'Sorry, that username already exists!');
    }
    if (!validate_username($username)) {
        $reg_errors->add('username_invalid', 'Sorry, the username you entered is not valid');
    }
    if (!is_email($email)) {
        $reg_errors->add('email_invalid', 'Email is not valid');
    }
    if (email_exists($email)) {
        $reg_errors->add('email', 'Email Already in use');
    }
    if ($tos == "") {
        $reg_errors->add('Terms of Service', 'Sorry, Terms of Service is a required field.');
    }

}

function generate_password($length = 10)
{

    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%*()";
    $password = substr(str_shuffle($chars), 0, $length);
    return $password;
}

function register_process($data = array())
{
    if (isset($_POST['register'])) {
        validation($_POST['user_login'], $_POST['full_name'], $_POST['user_email'], $_POST['tos']);
        // sanitize user form input
        $username = sanitize_user($_POST['user_login']);
        $businessname = sanitize_text_field($_POST['business_name']);
        $fullname = sanitize_text_field($_POST['full_name']);
        $address = esc_textarea($_POST['full_address']);
        $email = sanitize_email($_POST['user_email']);
        $phone = $_POST['phone_number'];
        $tos = $_POST['tos'];

        global $reg_errors;
        if (count($reg_errors->get_error_messages()) < 1) {
            $password_generate = generate_password(10);
            $userdata = array(
                'user_login' => $username,
                'user_email' => $email,
                'user_pass' => $password_generate,
                'user_nicename' => $businessname,
                'last_name' => $username,
                'nickname' => $fullname,
                'display_name' => $username,
            );
            $user_id = wp_insert_user($userdata);

            if (!is_wp_error($user_id)) {
                update_user_meta($user_id, 'business_name', $businessname);
                update_user_meta($user_id, 'nickname', $businessname);
                update_user_meta($user_id, 'full_name', $fullname);
                update_user_meta($user_id, 'full_address ', $address);
                update_user_meta($user_id, 'phone_number', $phone);
                update_user_meta($user_id, 'tos', $tos);
                update_user_meta($user_id, 'first_name', $businessname);
                $from_email = get_option('wpmembers_email_wpfrom');
                $from_name = get_option('wpmembers_email_wpname');
                $body_subj['body'] = "
Thank you for joining Ryan Kikta!

Your registration information is below.

username: [username]
password: [password]

We strongly recommend changing your password using the
link below to keep your account secure.

You may change your password here:
https://ryankikta.com/my-profile-app/?app=bigcommerce&a=pwdchange

Let us know if you have any questions,";//get_option('wpmembers_email_newreg');
                $body_subj['subj'] = "Your registration info for Ryan Kikta";
                $subj = $body_subj['subj'];
                $body = $body_subj['body'];
                $shortcd = array('[username]', '[password]');
                $replace = array($username, $password_generate);
                $body = str_replace($shortcd, $replace, $body);
                $headers = 'To: ' . $username . ' <' . $email . '>' . "\r\n";
                $headers .= 'From: ' . $from_name . ' <' . $from_email . '>' . "\r\n";
                wp_mail($email, stripslashes($subj), stripslashes($body), $headers);

                // Automatic logged user
                wp_set_current_user($user_id, $username);
                wp_set_auth_cookie($user_id);
                $creds = array();
                $creds['user_login'] = $username;
                $creds['user_password'] = $password_generate;
                $creds['remember'] = true;
                $user = wp_signon($creds, false);
                $redirect = $_SERVER['REQUEST_URI'];
                wp_redirect($redirect);
            }
        } else {
            ?>
            <div class="errors_apps"
                 style="width: 50%; padding: 10px; margin: 0px 40px 20px; border: 1px solid red; background-color: rgb(255, 235, 232);">
                <?php
                foreach ($reg_errors->get_error_messages() as $error) {
                    echo $error . '<br>';
                }
                ?>
            </div>
        <?php }
    }
    unset($_SESSION['errors_big']);
    register_form($data);
}

function register_shortcode($atts)
{
    extract(shortcode_atts(array(
        'user_name' => '',
        'full_name' => '',
        'user_email' => '',
        'phone' => '',
        'user_url' => ''
    ), $atts));
    $user_data = array('user_name' => $user_name,
        'full_name' => $full_name,
        'user_email' => $user_email,
        'phone' => $phone,
        'user_url' => $user_url);
    ob_start();
    register_process($user_data);
    return ob_get_clean();
}

add_shortcode('big_register_form', 'register_shortcode');
