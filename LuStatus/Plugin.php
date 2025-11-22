<?php
/**
 * LiyaoUniversity 在线状态插件
 * 
 * @package LuStatus
 * @author Liyao
 * @version 1.0.1
 * @link https://liyao.edu.kg
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;

class LuStatus_Plugin implements Typecho_Plugin_Interface
{
    public static function activate()
    {
        // 注册路由：当访问 /action/lustatus 时，调用 LuStatus_Action 类
        Helper::addAction('lustatus', 'LuStatus_Action');
        Typecho_Plugin::factory('Widget_Archive')->footer = array('LuStatus_Plugin', 'render');
        return _t('LuStatus 插件已激活');
    }

    public static function deactivate()
    {
        Helper::removeAction('lustatus');
    }

    public static function config(Typecho_Widget_Helper_Form $form)
    {
        $secretKey = new Typecho_Widget_Helper_Form_Element_Text('secretKey', NULL, 'secret_key', _t('通信密钥'), _t('请修改此密钥'));
        $form->addInput($secretKey);

        $timeout = new Typecho_Widget_Helper_Form_Element_Text('timeout', NULL, '300', _t('超时时间 (秒)'), _t('超过这个时间未收到心跳即显示离线'));
        $form->addInput($timeout);
    }

    public static function personalConfig(Typecho_Widget_Helper_Form $form){}

    public static function render()
    {
        $options = Helper::options();
        // 这里的 index 确保生成的 URL 是正确的（无论是否开启伪静态）
        $apiUrl = Typecho_Common::url('/action/lustatus', $options->index);
        
        echo <<<HTML
<!-- LuStatus Widget Start -->
<style>
#lu-status-widget { text-align: center; padding: 10px 0; font-size: 13px; font-family: monospace; color: #666; }
#lu-status-dot { display: inline-block; width: 10px; height: 10px; border-radius: 50%; background-color: #95a5a6; margin-right: 6px; vertical-align: middle; transition: all 0.3s; }
</style>

<script>
(function() {
    var placeholder = document.getElementById('lu-status-placeholder');
    var widgetHtml = '<div id="lu-status-widget"><span id="lu-status-dot"></span><span id="lu-status-text">正在连接...</span></div>';
    
    if (placeholder) {
        placeholder.innerHTML = widgetHtml;
    } else {
        var footer = document.getElementsByTagName('footer')[0];
        if(footer) {
            var div = document.createElement('div');
            div.innerHTML = widgetHtml;
            footer.appendChild(div);
        }
    }

    var apiUrl = '{$apiUrl}';

    function updateLuStatus() {
        // 前端查询使用 ?do=check
        fetch(apiUrl + '?do=check')
            .then(res => res.json())
            .then(data => {
                var dot = document.getElementById('lu-status-dot');
                var text = document.getElementById('lu-status-text');
                if(!dot || !text) return;

                if (data.online) {
                    dot.style.backgroundColor = '#2ecc71';
                    dot.style.boxShadow = '0 0 8px #2ecc71';
                    text.innerText = ' 设备在线';
                    text.style.color = '#2ecc71';
                } else {
                    dot.style.backgroundColor = '#95a5a6';
                    dot.style.boxShadow = 'none';
                    
                    var seconds = data.last_seen;
                    var timeStr = '';
                    if (seconds < 60) timeStr = seconds + ' 秒前';
                    else if (seconds < 3600) timeStr = Math.floor(seconds / 60) + ' 分钟前';
                    else if (seconds < 86400) timeStr = Math.floor(seconds / 3600) + ' 小时前';
                    else timeStr = Math.floor(seconds / 86400) + ' 天前';

                    text.innerText = ' 设备离线 (' + timeStr + ')';
                    text.style.color = '#7f8c8d';
                }
            })
            .catch(e => console.log('LuStatus Error:', e));
    }

    updateLuStatus();
    setInterval(updateLuStatus, 60000);
})();
</script>
<!-- LuStatus Widget End -->
HTML;
    }
}