<?php

// 设置配置
function fatrat_ajax_add_options() {
    global $wpdb;
    $table = $wpdb->prefix.'fr_options';
    $option_id = !empty($_REQUEST['option_id']) ? esc_sql( $_REQUEST['option_id'] ) : null;
    $remove_outer_link = esc_sql( $_REQUEST['remove_outer_link'] );
    $keywords_replace_rule =  esc_textarea( $_REQUEST['keywords_replace_rule'] ) ;
    $params = [
            'remove_outer_link' => $remove_outer_link,
            'keywords_replace_rule'=> $keywords_replace_rule
        ];
    if ($option_id === null){
        $wpdb->insert($table, $params);
        wp_send_json(['code'=>0, 'result'=>$wpdb->insert_id]);
    } else {
        $wpdb->update(
                $table,
                $params,
                ['id' => $option_id],
                ['%s', '%s'],
                ['%d']
            );
        wp_send_json(['code'=>0, 'result'=>$option_id]);
    }

    wp_die();
}
add_action( 'wp_ajax_add_options', 'fatrat_ajax_add_options' );


function rat_options()
{
    global $wpdb;
    $table = $wpdb->prefix.'fr_options';
    $option = $wpdb->get_row("select * from $table limit 1", ARRAY_A);
    ?>
    <div>
        <div>
            <h3>采集规则配置</h3>
        </div>
        <table class="form-table">
            <input type="hidden" hidden id="request_url" value="<?php echo admin_url( 'admin-ajax.php' );?>">
            <input type="hidden" hidden id="option_id" value="<?php echo isset($option['id'])? $option['id'] : ''?>">
            <tr>
                <th>是否移除内容链接</th>
                <td>
                    <input type="radio" name="remove_outer_link" value="2" <?php echo isset($option) && $option['remove_outer_link'] == '2' ? 'checked' : '' ?> > 是
                    <input type="radio" name="remove_outer_link" value="1" <?php echo isset($option) && $option['remove_outer_link'] == '1' ? 'checked' : '' ?> > 否
                </td>
            </tr>
            <tr>
                <th scope="row">关键词替换</th>
                <td>
                    <textarea name="keywords_replace_rule" cols="100" rows="8" placeholder="在此输入关键词替换规则，替换 title content 里面的内容"><?php if(isset($option['keywords_replace_rule'])) echo str_replace('/n', '<br />', $option['keywords_replace_rule']);?></textarea><br>
                    注意。阿拉伯数字 英文字符 不可以配置替换。 因为会把 内容图片URL替换成错误的：<br>
                    例子<br>
                    叶子猪=游戏<br>
                    天赋=种族天赋<br>
                </td>
            </tr>
        </table>
        <input id="save-button" type="button" class="button button-primary" value="保存配置">按钮点击后请不要重复点击，反应慢点
    </div>
    <?php
}

