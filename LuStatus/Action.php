<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

class LuStatus_Action extends Typecho_Widget implements Widget_Interface_Do
{
    public function action()
    {
        $request = Typecho_Request::getInstance();
        $options = Helper::options();
        $pluginOpts = $options->plugin('LuStatus');
        
        // 存储数据文件
        $dataFile = __TYPECHO_ROOT_DIR__ . '/usr/uploads/lu_heartbeat.dat';
        
        $do = $request->get('do');

        // 1. 接收心跳 (Client -> Server)
        if ($do == 'beat') {
            $key = $request->get('key');
            
            if ($key === $pluginOpts->secretKey) {
                // 检查，如果写入失败直接报错
                $writeResult = @file_put_contents($dataFile, time());
                
                if ($writeResult === false) {
                    $this->response->setStatus(500);
                    $this->response->throwJson([
                        'status' => 'error', 
                        'msg' => 'Write Failed: Permission Denied on /usr/uploads/'
                    ]);
                } else {
                    $this->response->throwJson(['status' => 'success']);
                }
            } else {
                $this->response->setStatus(403);
                $this->response->throwJson(['status' => 'error', 'msg' => 'Invalid Key']);
            }
        }

        // 2. 前端查询 (Footer -> Server)
        if ($do == 'check') {
            $last_seen = 9999999;
            
            // 检查文件是否存在且有内容
            if (file_exists($dataFile)) {
                $timestamp = (int)file_get_contents($dataFile);
                if ($timestamp > 0) {
                    $last_seen = time() - $timestamp;
                }
            }
            
            $is_online = $last_seen < (int)$pluginOpts->timeout;
            
            $this->response->throwJson([
                'online' => $is_online,
                'last_seen' => $last_seen,
            ]);
        }
    }
}