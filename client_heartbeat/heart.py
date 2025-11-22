import requests
import time

# 配置
# 你的blog地址(如果你用了地址重写可能是/action/lustatus)
URL = "https://liyao.edu.kg/index.php/action/lustatus"
# 确保这里和你插件配置的密钥一致
KEY = "secret_key" 

def send_heartbeat():
    try:
        payload = {'do': 'beat', 'key': KEY}
        no_proxy = {
            "http": None,
            "https": None,
        }
        r = requests.get(URL, params=payload, timeout=10, proxies=no_proxy)
        if r.status_code == 200:
            print(f"[{time.strftime('%H:%M:%S')}] 心跳发送成功")
        else:
            print(f"[{time.strftime('%H:%M:%S')}] 服务器拒绝: {r.text}")
    except requests.exceptions.ProxyError:
        print(f"[{time.strftime('%H:%M:%S')}] 代理错误：脚本已强制直连，但仍然失败。请检查网络或关闭 VPN。")
    except Exception as e:
        print(f"[{time.strftime('%H:%M:%S')}] 发送失败: {e}")
if __name__ == "__main__":
    print(f"开始向 {URL} 发送心跳数据...")
    send_heartbeat()
    while True:
        time.sleep(180) 
        send_heartbeat()