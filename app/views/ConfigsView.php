<?php
/**
 * Created by PhpStorm.
 * User: thanhphuc,lamnguyenit
 * Date: 8/16/17
 * Time: 9:11 AM
 */

namespace YBAI\Views;


class ConfigsView
{
    static function content(){
        ?>
        <div class="wrap">
            <h2>Cài đặt hệ thống</h2>
            <?php if( isset($_GET['settings-updated']) ) { ?>
                <div id="content" class="updated-css">
                    <p><strong><?php _e('Settings saved.') ?></strong></p>
                </div>
            <?php } ?>
            <form method="post" action="options.php">
                <?php settings_fields( 'ybai-configs' ); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row" style="width: 100px">Nhúng mã:</th>
                        <td>
                            <textarea name="ybai_head_script" style="width: 100%; height: 300px"><?php esc_html_e(get_option('ybai_head_script')); ?></textarea>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
            <div class="guide" style="padding-top: 10px">
                <div>
                    <h3>Đồng bộ sản phẩm</h3>
                    <div>
                        <div id="ybai_sync_all" class="button">Đồng bộ tất cả</div>
                        <div class="hidden" id="ybai_sync_all_loading">
                            <div style="display: flex;align-items: center">
                                <img width="50px" src="<?php echo YBAI_URL ?>/assets/images/loading.gif">
                                <span style="color:darkred">Đang đồng bộ, vui lòng chờ...</span>
                            </div>
                        </div>
                        <p>Click đồng bộ và vui lòng <b>CHỜ</b> đến khi nhận được thông báo từ hệ thống! Có thể mất vài phút để đồng bộ, vui lòng không thực hiện thao tác khác khi đang đồng bộ.</p>
                    </div>
                </div>
            </div>
            <div class="guide" style="padding-top: 10px">
                <div>
                    <h3>Cách dùng:</h3>
                    <div>
                        <ul style="line-height: 30px">
                                Bước 1: Truy cập vào hệ thống <a href="https://ybai.me" target="_blank">ybai</a> và đăng nhập tài khoản quản trị doanh nghiệp.<br>
                                Bước 2: Truy cập vào menu <b>Kênh</b> <a href="https://ybai.me/channel" target="_blank">tại đây</a> hoặc click vào menu(9chấm) góc trên, bên phải và chọn vào "Kênh".<br>
                            Bước 3: Tạo mới "Kênh" (nếu chưa có) và click vào icon <b>"<>"</b>, sau đó copy mã nhúng<br>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
