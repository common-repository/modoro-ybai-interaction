<?php
/**
 * Created by PhpStorm.
 * User: phucn
 * Date: 19/07/2016
 * Time: 9:47 SA
 */

namespace YBAI\Views;

class ConnectView
{

    static function content() {
        ?>
        <div class="wrap">
            <h2>Kết nối YBAI</h2>
            <?php if( isset($_GET['settings-updated']) ) { ?>
                <div id="message" class="updated">
                    <p><strong><?php _e('Settings saved.') ?></strong></p>
                </div>
            <?php } ?>
            <form method="post" id="check_connection_ybai" action="options.php">
                <?php settings_fields( 'ybai-connect' ); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Access key:</th>
                        <td>
                            <input type="text" name="ybai_access_key" style="width: 90%" value="<?php esc_html_e(get_option('ybai_access_key')); ?>" />
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Secret key:</th>
                        <td>
                            <input type="password" name="ybai_secret_key" style="width: 90%"  value="<?php esc_html_e(get_option('ybai_secret_key')); ?>" />
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
            <div class="guide" style="padding-top: 10px">
                <tr valign="top">
                    <h3>Cách dùng:</h3>
                    <td>
                        <ul style="line-height: 30px">
                            Bước 1: Truy cập vào hệ thống <a href="https://ybai.me" target="_blank">ybai</a> và đăng nhập tài khoản quản trị doanh nghiệp.<br>
                            Bước 2: Truy cập vào menu <b>Kênh</b> <a href="https://ybai.me/channel" target="_blank">tại đây</a> hoặc click vào menu(9chấm) góc trên, bên phải và chọn vào "Kênh".<br>
                            Bước 3: Copy mã <b>"Access key"</b> và <b>"Secret key"</b> bỏ vào ô tương ứng<br>
                            </li>
                        </ul>
                    </td>
                </tr>
            </div>
        </div>
    <?php }
}