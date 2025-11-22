# LuStatus

**Typecho 状态插件 / A Status Plugin for Typecho**
---
<img width="128" height="24" alt="image" src="https://github.com/user-attachments/assets/ea5c2c1b-efc7-49c5-836d-b06f34a61911" />

通过本地心跳包，在博客页脚实时展示博主是否在线。

### ✨ 特性
*   **实时同步**：基于 Python 客户端发送心跳
*   **自动挂载**：无需修改主题代码，自动注入页脚
*   **状态判定**：超时自动显示离线时长

### 🛠 使用方法
1.  **服务端**：上传并启用插件，在设置中填写通信密钥。
2.  (可选) 在你的主题**footer.php**中添加```<div id="lu-status-placeholder"></div>```, 插件会自动在这里添加状态显示, 否则自动添加到**footer.php**尾部
3.  **客户端**：修改仓库内 Python 脚本中的 `URL` 和 `密钥`。
4.  **运行**：在本地电脑挂起 Python 脚本即可。

### 📂 依赖
*   Typecho
*   Python 3.x (客户端) + `requests` 库
