# 彈簧製程報表系統 - 技術設計文件

## 1. 系統架構
*   **前端**: Blade Template (Laravel 內建), CSS (Vanilla/Bootstrap), JavaScript (Vanilla/Axios)
*   **後端**: PHP 8.2+ / Laravel 11
*   **資料庫**: MariaDB 10.4+ (phpMyAdmin 管理)
*   **認證**: Laravel Session-based Auth

## 2. 資料庫設計 (Schema)

### `users` (使用者表)
*   `id`: PK (Primary Key)
*   `username`: 帳號 (唯一)
*   `name`: 中文姓名
*   `password`: 加密密碼
*   `role`: 權限 (枚舉: `manager`, `worker`)

### `process_types` (製程選項庫)
*   `id`: PK
*   `name`: 製程名稱 (預設：生產、分尺寸、回火、研磨、精研、珠擊、預壓)
*   `is_default`: 是否為系統預設

### `products` (產品/工件表)
*   `id`: PK
*   `product_code`: 工件編號
*   `sales_rep`: 接單業務
*   `status`: 總體狀態 (待處理/進行中/已完成)
*   `created_at / updated_at`

### `product_processes` (產品特定製程流)
*   `id`: PK
*   `product_id`: FK (關聯 products)
*   `process_type_id`: FK (關聯 process_types)
*   `sequence`: 排序序號 (1, 2, 3...)
*   `status`: 狀態 (pending, processing, completed)
*   `worker_id`: 當前負責工人 FK (關聯 users, 可為空)
*   `started_at`: 開始時間 (NULL if pending)
*   `completed_at`: 完成時間 (NULL if not completed)

## 3. 功能模組

### A. 權限與登入
*   登入頁面：驗證 `username` 與 `password`。
*   Middleware：區分 `Manager` 與 `Worker` 路由，防止工人進入後台。

### B. 主管後台 (Manager Dashboard)
1.  **使用者管理**: CRUD 功能 (新增、刪除、編輯、查看)。
2.  **製程選項管理**: 自由新增/刪除全局製程選項。
3.  **產品上架**:
    *   輸入工件編號、業務名稱。
    *   **動態製程配置**: 從「製程選項庫」選取並排列該產品的工件順序（支援重複選取）。
4.  **編輯功能**: 產品上架後，皆可修改資訊與製程順序。

### C. 工人介面 (Worker Interface)
1.  **看板視圖**: 顯示所有在製產品。
2.  **操作邏輯**:
    *   點擊產品 -> 顯示該產品的所有製程步驟。
    *   **領取工作**: 勾選正在進行的製程 -> 狀態改為 processing，記錄人員。
    *   **回報完成**: 勾選已完成製程 -> 狀態改為 completed。
    *   **狀態回退**: 可撤銷進行中或已完成的狀態，退回上一級。

## 4. 預設資料 (Initial Seed)
系統初始化後將自動建立以下帳號 (密碼與帳號相同)：
*   **主管 (Manager)**:
    1.  帳號: `admin1`, 姓名: `張主管`
    2.  帳號: `admin2`, 姓名: `李主管`
*   **工人 (Worker)**:
    1.  帳號: `worker1`, 姓名: `王小明`
    2.  帳號: `worker2`, 姓名: `陳大華`

