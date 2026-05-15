# Joe’s Electronics — Inventory & Sales Management System

**Joe’s Electronics** is a specialized web-based solution for inventory management and sales automation for retail electronics stores. The project is designed to replace manual bookkeeping with a digital ecosystem that eliminates human error and accelerates business processes.

## 🚀 Key Features

* **Inventory Tracking:**
    * Real-time stock monitoring.
    * **Bubble Sort** implementation to prioritize items with critical stock levels (items running out are displayed first).
    * Visual status indicators: `OK`, `Low Stock` (≤5 units), and `Out of Stock`.
* **Sales Automation (Point of Sale):**
    * Rapid checkout processing with instant stock validation.
    * Automatic total calculation and warehouse updates post-transaction.
    * Generation of detailed digital receipts including cashier data and itemized lists.
* **Analytics & Reporting:**
    * Daily revenue reports and unit sales tracking.
    * Staff performance statistics.
    * Advanced SQL queries (`JOIN`, `SUM`, `GROUP BY`) for deep data analysis.
* **Security & Roles (RBAC):**
    * Role-based access control for **Managers** (full access, analytics, product management) and **Cashiers** (sales operations only).

## 🛠 Tech Stack

* **Backend:** PHP (Session handling, server-side logic).
* **Database:** MySQL (Relational tables: `Users`, `Products`, `Sales`, `Sale_Items`).
* **Frontend:** HTML5, CSS3 (Responsive Dashboard, UI animations).
* **Algorithms:** Custom Bubble Sort for data prioritization.

## 📈 Impact

Implementing this system saves approximately **90 minutes of administrative work daily** by automating report generation and eliminating manual inventory recounts.

## 💻 Installation

1. Clone the repository.
2. Import the `joes_electronics.sql` database via phpMyAdmin.
3. Configure database credentials in `connect.php`.
4. Run the project using a local server (XAMPP, WAMP, or OpenServer).

---
*Developed with a focus on clean data architecture and modern minimalist UI design.*
