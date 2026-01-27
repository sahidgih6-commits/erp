# POS System - Quick Start Guide

## 🚀 Getting Started

### Prerequisites
- Laravel 10+
- PHP 8.0+
- MySQL/PostgreSQL
- Composer dependencies installed
- Tailwind CSS configured

---

## 📋 Installation Steps

### 1. **Install Dependencies**
```bash
cd /workspaces/erp
composer install
npm install
npm run build
```

### 2. **Run Migrations**
```bash
php artisan migrate
```

This creates:
- `system_versions` - Version control per business
- `hardware_devices` - Device configuration and status
- `hardware_audit_logs` - Comprehensive activity logs
- `pos_transactions` - Transaction records
- `receipt_prints` - Receipt print jobs

### 3. **Create Test Data (Optional)**
```bash
php artisan db:seed
```

### 4. **Start Development Server**
```bash
php artisan serve
```

### 5. **Access the Application**
- Login at: `http://localhost:8000/login`
- Test credentials: Check your database seeder or create manually

---

## 🎯 Main Features

### **For Super Admins**
- **URL**: `/superadmin/hardware`
- **Access**: Role: `superadmin`
- **Features**:
  - View all businesses and their hardware status
  - Configure system version (Basic/Pro/Enterprise)
  - Add/Edit/Delete hardware devices
  - Enable/disable features
  - View comprehensive audit logs
  - Test device connections

### **For Shop Owners/Salesman**
- **URL**: `/pos/dashboard`
- **Access**: Role: `owner` or `salesman`
- **Features**:
  - POS Dashboard with hardware status
  - Fast POS billing interface
  - Barcode scanning (if enabled)
  - Receipt printing (if enabled)
  - Cash drawer operations (if enabled)
  - Transaction history
  - Daily sales summary

---

## 🌐 Language Support

### **Switching Languages**
- Click language button (top-right corner)
- Select: English or বাংলা (Bengali)
- Session-based persistence

### **Available Translations**
- All POS terms: Hardware, Payment, Receipt, etc.
- Device types with translations
- Status messages
- UI labels and buttons

---

## 🛠️ Hardware Device Setup

### **Steps**:
1. Login as Super Admin
2. Go to `/superadmin/hardware`
3. Select a business
4. Click "Configure" → Select version (Pro/Enterprise enables hardware)
5. Click "Add Device"
6. Fill in:
   - Device Type (Barcode Scanner, Thermal Printer, Cash Drawer)
   - Device Name (e.g., "Main Counter Scanner")
   - Connection Type (USB, Network, Bluetooth)
   - Port (COM3, /dev/ttyUSB0, etc.)
7. Save

### **Device Types Supported**:
| Type | Basic | Pro | Enterprise | Connection |
|------|-------|-----|------------|-----------|
| Barcode Scanner | ✗ | ✓ | ✓ | USB/Network/BT |
| Thermal Printer | ✗ | ✓ | ✓ | USB/Network/BT |
| Cash Drawer | ✗ | ✗ | ✓ | USB/Network |

---

## 💳 POS Billing Workflow

### **Steps for Cashier**:
1. Click "Billing" button on dashboard
2. **Scan or Search Products**:
   - Scan barcode (if barcode scanner enabled)
   - Or click product from list
   - Or search by product name
3. **Add to Cart**:
   - Product appears in cart
   - Adjust quantity
   - System auto-calculates total
4. **Select Payment Method**:
   - Cash
   - Card
   - Mobile
5. **Enter Amount Tendered**:
   - System calculates change
6. **Process Payment**:
   - Transaction saved
   - Receipt printed (if printer enabled)
   - Drawer opens (if enabled)
7. **Complete**:
   - Cart clears
   - New transaction ready

---

## 📊 Transaction History

### **Access**:
- `/pos/history` → View all transactions
- Filter by:
  - Date range
  - Payment method
  - User

### **Actions**:
- View detailed transaction
- Reprint receipt
- Track payment status

---

## 🔐 Role-Based Permissions

### **Roles Included**:
1. **superadmin** - Full system access, hardware management
2. **owner** - Business owner, can use POS
3. **manager** - Manage staff and inventory
4. **salesman** - Use POS for billing

### **POS Access**:
- `/pos/*` routes accessible by: `owner|salesman|manager`
- `/superadmin/hardware/*` only for `superadmin`

---

## 🗂️ File Structure

```
resources/
  ├── lang/
  │   ├── en/
  │   │   └── pos.php (English translations)
  │   └── bn/
  │       └── pos.php (Bengali translations)
  └── views/
      ├── pos/
      │   ├── layout.blade.php (Main layout)
      │   ├── dashboard.blade.php (Dashboard)
      │   ├── billing.blade.php (POS interface)
      │   └── history.blade.php (Transactions)
      └── superadmin/hardware/
          ├── layout.blade.php
          ├── index.blade.php (All businesses)
          ├── show.blade.php (Business details)
          ├── configure-version.blade.php (Version config)
          ├── create-device.blade.php (Add device)
          ├── edit-device.blade.php (Edit device)
          └── audit-logs.blade.php (Activity logs)

app/
  ├── Models/
  │   ├── SystemVersion.php
  │   ├── HardwareDevice.php
  │   ├── HardwareAuditLog.php
  │   ├── POSTransaction.php
  │   └── ReceiptPrint.php
  └── Http/Controllers/
      ├── SuperAdmin/
      │   └── HardwareManagementController.php
      └── POS/
          └── POSDashboardController.php

database/migrations/
  ├── 2026_01_22_000001_create_system_versions_table.php
  ├── 2026_01_22_000002_create_hardware_devices_table.php
  ├── 2026_01_22_000003_create_hardware_audit_logs_table.php
  ├── 2026_01_22_000004_create_pos_transactions_table.php
  └── 2026_01_22_000005_create_receipt_prints_table.php
```

---

## 🧪 Testing

### **Manual Testing Checklist**:

#### Localization
- [ ] Switch to Bengali language
- [ ] Verify all UI text translates
- [ ] Switch back to English
- [ ] Verify language persists on page refresh

#### Hardware Management (Super Admin)
- [ ] Login as superadmin
- [ ] Navigate to `/superadmin/hardware`
- [ ] View all businesses
- [ ] Configure a business version
- [ ] Add a hardware device
- [ ] Edit device settings
- [ ] Toggle device enable/disable
- [ ] View audit logs

#### POS Billing (Cashier)
- [ ] Login as salesman/owner
- [ ] Go to `/pos/dashboard`
- [ ] View hardware status
- [ ] Click "Billing"
- [ ] Add products manually
- [ ] Adjust quantities
- [ ] Calculate totals
- [ ] Complete transaction
- [ ] View transaction history

#### Transactions
- [ ] Create multiple transactions
- [ ] Filter transactions by date/method
- [ ] View transaction details
- [ ] Print receipt

---

## 🔧 API Endpoints (JSON)

### **POS Endpoints**:
```
POST /pos/transaction
  {
    "items": [{"product_id": 1, "quantity": 2, "price": 100}],
    "subtotal": 200,
    "discount": 0,
    "tax": 20,
    "total": 220,
    "payment_method": "cash",
    "amount_tendered": 250
  }

GET /pos/search-product?query=barcode
  Returns: [{"id": 1, "name": "Product", "price": 100, "stock": 50}]

POST /pos/print-receipt/1
  {"paper_size": "80mm"}

GET /pos/summary
  Returns daily sales summary

POST /pos/open-drawer
```

---

## 🚨 Troubleshooting

### **Issue**: Migrations not running
**Solution**:
```bash
php artisan migrate:reset
php artisan migrate
```

### **Issue**: Language not switching
**Solution**:
- Clear session cache: `php artisan cache:clear`
- Clear config cache: `php artisan config:clear`

### **Issue**: Hardware device not connecting
**Solution**:
- Check port configuration matches actual device
- Verify USB/Network cable connection
- Test device connection from admin panel
- Check `hardware_audit_logs` for error messages

### **Issue**: Receipt not printing
**Solution**:
- Verify thermal printer is enabled in version config
- Check device status in dashboard
- Verify printer is connected and online
- Check `receipt_prints` table for print jobs

---

## 📈 Future Enhancements

### **Priority 1 (High)**:
- Actual hardware printer driver integration
- Barcode scanner input handling
- Payment gateway integration
- Offline mode with queue system

### **Priority 2 (Medium)**:
- Advanced reporting and analytics
- Customer loyalty system
- Multi-branch management
- Mobile POS app

### **Priority 3 (Low)**:
- Kitchen display system
- Table management (for restaurants)
- Delivery tracking
- Custom themes

---

## 📞 Support

For issues or questions:
1. Check the audit logs: `/superadmin/hardware/:business/audit-logs`
2. Review error messages in receipt_prints table
3. Check hardware_audit_logs for activity history
4. Test device connection from admin panel

---

**Created**: January 22, 2026
**Version**: 1.0.0 (Beta)
**Status**: ✅ Ready for testing and implementation
