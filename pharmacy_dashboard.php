<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$pharma = isset($_GET['pharma']) ? trim($_GET['pharma']) : (isset($_SESSION['pharma_code']) ? $_SESSION['pharma_code'] : 'laurents');

// Enforce session security checks
if (!isset($_SESSION['user_role']) || 
    ($_SESSION['user_role'] !== 'pharmacy' && $_SESSION['user_role'] !== 'admin') || 
    ($_SESSION['user_role'] === 'pharmacy' && $_SESSION['pharma_code'] !== $pharma)) {
    header('Location: index.php?error=unauthorized');
    exit;
}

$_SESSION['pharma_code'] = $pharma;

$names = [
    'laurents' => "Laurent's Pharmacy",
    'jrm' => "JRM DOCTORS Pharmacy",
    'riteaid' => "D' Rite Aid Generics Pharmacy"
];
$pharmaName = $names[$pharma] ?? "Pharmacy Portal";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pharmaName; ?> Management ERP - PharmaSync</title>
    <link rel="stylesheet" href="assets/css/index.css">
    <style>
        .alert-banner {
            background: #D1FAE5;
            color: #065F46;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
            display: none;
            align-items: center;
            justify-content: space-between;
            font-weight: 500;
            border: 1px solid #A7F3D0;
        }

        /* Custom ERP Tabs */
        .erp-tabs {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            border-bottom: 2px solid #E2E8F0;
            align-items: flex-end;
        }

        .erp-tab-btn {
            padding: 0.75rem 1.5rem;
            font-size: 1.05rem;
            font-weight: 600;
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            transition: all 0.2s;
        }

        .erp-tab-btn:hover {
            color: var(--primary);
        }

        .erp-tab-btn.active {
            color: var(--primary);
            border-bottom: 3px solid var(--primary);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }
    </style>
</head>

<body>

    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="brand" style="display:flex; align-items:center; gap:0.35rem;">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                    stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="12" y1="8" x2="12" y2="16"></line>
                    <line x1="8" y1="12" x2="16" y2="12"></line>
                </svg>
                <span><span style="color: var(--primary);">Pharma</span><span style="color: black;">Sync</span></span>
            </a>
            <div class="nav-links">
                <span
                    style="background: #EFF6FF; color: var(--primary); font-weight: 700; font-size: 0.8rem; padding: 0.4rem 1rem; border-radius: 2rem; text-transform: uppercase; display: inline-flex; align-items: center; margin-right: 0.75rem; letter-spacing: 0.05em;">PORTAL:
                    <?php echo htmlspecialchars($pharmaName); ?></span>
                <a href="logout.php" class="btn-switch-role">Logout</a>
            </div>
        </div>
    </nav>

    <main class="container animate-fade">
        <div id="syncBanner" class="alert-banner animate-fade">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                    <polyline points="22 4 12 14.01 9 11.01"></polyline>
                </svg>
                <span id="syncBannerText">Inventory Synchronized!</span>
            </div>
            <button onclick="document.getElementById('syncBanner').style.display='none'"
                style="background:none;border:none;cursor:pointer;font-weight:bold;color:#065F46;">✕</button>
        </div>

        <div
            style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h1 style="font-size: 2rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;">
                    <?php echo $pharmaName; ?> ERP
                </h1>
            </div>
            <div>
                <button id="toggleStatusBtn" onclick="toggleStoreStatus()" class="btn btn-outline">Store Status:
                    Loading...</button>
            </div>
        </div>

        <!-- ERP Tabs Navigation -->
        <div class="erp-tabs">
            <button class="erp-tab-btn active" onclick="switchTab('medicines', event)">Branded Products</button>
            <button class="erp-tab-btn" onclick="switchTab('brands', event)">Generics Dictionary</button>
            <button class="erp-tab-btn" onclick="switchTab('categories', event)">Categories</button>

            <!-- Dynamic Search & Action Controls on the Right -->
            <div class="erp-tab-controls-container"
                style="margin-left: auto; display: flex; align-items: center; margin-bottom: 8px;">
                <!-- Controls for Medicines -->
                <div id="controls_medicines" class="erp-tab-control-group"
                    style="display: flex; gap: 0.75rem; align-items: center;">
                    <input type="text" onkeyup="filterSpecificTable('medTableBody', this.value)"
                        placeholder="🔍 Search products..." class="form-input"
                        style="width: 260px; background: white; border-radius: 2rem; padding-left: 1.25rem; margin-bottom: 0;">
                    <button onclick="openModal('medicineModal')" class="btn btn-primary">Add Product</button>
                </div>

                <!-- Controls for Brands -->
                <div id="controls_brands" class="erp-tab-control-group"
                    style="display: none; gap: 0.75rem; align-items: center;">
                    <input type="text" onkeyup="filterSpecificTable('brandTableBody', this.value)"
                        placeholder="🔍 Search ingredients..." class="form-input"
                        style="width: 260px; background: white; border-radius: 2rem; padding-left: 1.25rem; margin-bottom: 0;">
                    <button onclick="openModal('brandModal')" class="btn btn-primary">Add Generic</button>
                </div>

                <!-- Controls for Categories -->
                <div id="controls_categories" class="erp-tab-control-group"
                    style="display: none; gap: 0.75rem; align-items: center;">
                    <input type="text" onkeyup="filterSpecificTable('catTableBody', this.value)"
                        placeholder="🔍 Search categories..." class="form-input"
                        style="width: 260px; background: white; border-radius: 2rem; padding-left: 1.25rem; margin-bottom: 0;">
                    <button onclick="openModal('categoryModal')" class="btn btn-primary">Add Category</button>
                </div>
            </div>
        </div>

        <!-- ================= TAB 1: PRODUCTS SKU ================= -->
        <div id="tab_medicines" class="tab-content active animate-fade">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th style="width: 25%;">Product SKU</th>
                            <th style="width: 30%;">Classification</th>
                            <th style="width: 30%;">Inventory & Pricing</th>
                            <th style="width: 15%;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="medTableBody">
                        <tr>
                            <td colspan="4" style="text-align: center; padding: 2rem; color: var(--text-muted);">Loading
                                products...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ================= TAB 2: GENERICS ================= -->
        <div id="tab_brands" class="tab-content animate-fade">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Generic Active Ingredient</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="brandTableBody">
                        <tr>
                            <td colspan="2" style="text-align: center; padding: 2rem; color: var(--text-muted);">Loading
                                generics...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ================= TAB 3: CATEGORIES ================= -->
        <div id="tab_categories" class="tab-content animate-fade">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Category Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="catTableBody">
                        <tr>
                            <td colspan="2" style="text-align: center; padding: 2rem; color: var(--text-muted);">Loading
                                categories...</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- ================= MODALS ================= -->

    <!-- Add Product SKU Modal -->
    <div id="medicineModal" class="modal-overlay">
        <div class="modal">
            <h2 style="margin-bottom: 1.5rem; font-weight: 700;">Add New Product SKU</h2>
            <form id="medForm" onsubmit="addMedicine(event)">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Brand Name</label>
                        <input type="text" id="medBrandName" class="form-input" placeholder="e.g. Biogesic" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Strength</label>
                        <input type="text" id="medStrength" class="form-input" placeholder="e.g. 500mg" required>
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Active Generic Ingredient</label>
                    <select id="medBrandSelect" class="form-input" style="background: white;" required></select>
                </div>
                <div class="form-group">
                    <label class="form-label">Assign Category</label>
                    <select id="medCatSelect" class="form-input" style="background: white;" required></select>
                </div>
                <div class="form-group">
                    <label class="form-label">Manufacturer</label>
                    <input type="text" id="medMan" class="form-input" placeholder="e.g. Unilab" required>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label class="form-label">Price (₱)</label>
                        <input type="number" step="0.01" id="medPrice" class="form-input" value="5.00" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Initial Stock</label>
                        <input type="number" id="medStock" class="form-input" value="100" required>
                    </div>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1.5rem;">
                    <button type="button" onclick="closeModal('medicineModal')" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save SKU</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Generic Active Ingredient Modal -->
    <div id="brandModal" class="modal-overlay">
        <div class="modal">
            <h2 style="margin-bottom: 1.5rem; font-weight: 700;">Add New Generic Active Ingredient</h2>
            <form id="brandForm" onsubmit="addBrand(event)">
                <div class="form-group">
                    <label class="form-label">Generic Name</label>
                    <input type="text" id="brandNameInput" class="form-input" placeholder="e.g. Paracetamol" required>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1.5rem;">
                    <button type="button" onclick="closeModal('brandModal')" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Generic</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Category Modal -->
    <div id="categoryModal" class="modal-overlay">
        <div class="modal">
            <h2 style="margin-bottom: 1.5rem; font-weight: 700;">Add New Category</h2>
            <form id="catForm" onsubmit="addCategory(event)">
                <div class="form-group">
                    <label class="form-label">Category Name</label>
                    <input type="text" id="catNameInput" class="form-input" placeholder="e.g. Antibiotics" required>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1.5rem;">
                    <button type="button" onclick="closeModal('categoryModal')" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Category</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const pharmaCode = "<?php echo $pharma; ?>";
        let currentMedicinesList = [];
        let currentBrandsList = [];
        let currentCategoriesList = [];

        function switchTab(tabId, e) {
            document.querySelectorAll('.erp-tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

            const targetBtn = e ? e.currentTarget : (event ? event.currentTarget || event.target : null);
            if (targetBtn) {
                targetBtn.classList.add('active');
            } else {
                const btn = document.querySelector(`.erp-tab-btn[onclick*="${tabId}"]`);
                if (btn) btn.classList.add('active');
            }

            document.getElementById('tab_' + tabId).classList.add('active');

            // Switch the dynamic control groups on the right
            document.querySelectorAll('.erp-tab-control-group').forEach(group => {
                group.style.display = 'none';
            });
            const activeControl = document.getElementById('controls_' + tabId);
            if (activeControl) {
                activeControl.style.display = 'flex';
            }
        }

        function filterSpecificTable(tbodyId, query) {
            query = query.toLowerCase();
            const tbody = document.getElementById(tbodyId);
            if (!tbody) return;

            const rows = tbody.querySelectorAll('tr');
            rows.forEach(row => {
                if (row.cells.length === 1 && row.cells[0].colSpan > 1) return;

                let text = '';

                // 1. Gather values from all text inputs
                row.querySelectorAll('input').forEach(input => {
                    if (input.value) {
                        text += ' ' + input.value.toLowerCase();
                    }
                });

                // 2. Gather text only from currently selected options in select dropdowns
                row.querySelectorAll('select').forEach(select => {
                    if (select.selectedIndex >= 0) {
                        const opt = select.options[select.selectedIndex];
                        if (opt) {
                            text += ' ' + opt.text.toLowerCase();
                        }
                    }
                });

                // 3. Fallback for static rows (e.g. initial states or items without inputs/selects)
                if (row.querySelectorAll('input, select').length === 0) {
                    text = row.textContent.toLowerCase();
                }

                row.style.display = text.includes(query) ? '' : 'none';
            });
        }

        function loadData() {
            fetch(`api/inventory.php?action=get&pharma=${pharmaCode}`)
                .then(res => res.json())
                .then(data => {
                    currentMedicinesList = data.data; // Products list
                    currentBrandsList = data.brands;   // Generic active ingredients
                    currentCategoriesList = data.categories; // Categories

                    const statusBtn = document.getElementById('toggleStatusBtn');
                    if (data.is_open == 1) {
                        statusBtn.innerHTML = `Store Status: <span style="color:#10B981;font-weight:bold;">OPEN</span>`;
                    } else {
                        statusBtn.innerHTML = `Store Status: <span style="color:#EF4444;font-weight:bold;">CLOSED</span>`;
                    }

                    // Populate Dropdowns
                    const medBrandSel = document.getElementById('medBrandSelect');
                    const brandCatSel = document.getElementById('medCatSelect');
                    medBrandSel.innerHTML = '<option value="">-- Select Active Ingredient --</option>';
                    brandCatSel.innerHTML = '<option value="">-- Select Category --</option>';

                    currentBrandsList.forEach(b => {
                        const opt = document.createElement('option');
                        opt.value = b.id;
                        opt.textContent = b.name;
                        medBrandSel.appendChild(opt);
                    });
                    currentCategoriesList.forEach(c => {
                        const opt = document.createElement('option');
                        opt.value = c.id;
                        opt.textContent = c.name;
                        brandCatSel.appendChild(opt);
                    });

                    // Populate Table 1: Products
                    const medBody = document.getElementById('medTableBody');
                    medBody.innerHTML = '';
                    if (currentMedicinesList.length === 0) {
                        medBody.innerHTML = `<tr><td colspan="4" style="text-align:center;padding:2rem;color:var(--text-muted);">No products found.</td></tr>`;
                    } else {
                        currentMedicinesList.forEach(med => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td>
                                    <div style="display:flex; flex-direction:column; gap:0.4rem;">
                                        <input type="text" value="${med.brand_name}" id="brand_name_${med.id}" style="width:100%;padding:0.35rem;border:1px solid var(--border-color);border-radius:0.25rem;font-weight:600;color:var(--text-main);" placeholder="Brand Name">
                                        <input type="text" value="${med.strength}" id="strength_${med.id}" style="width:100%;padding:0.35rem;border:1px solid var(--border-color);border-radius:0.25rem;font-weight:600;" placeholder="Strength (e.g. 500mg)">
                                    </div>
                                </td>
                                <td>
                                    <div style="display:flex; flex-direction:column; gap:0.4rem;">
                                        <select id="med_gen_sel_${med.id}" style="width:100%;padding:0.35rem;border:1px solid var(--border-color);border-radius:0.25rem;font-weight:600;color:var(--text-main);">
                                            ${currentBrandsList.map(b => `<option value="${b.id}" ${b.id == med.medicine_id ? 'selected' : ''}>${b.name}</option>`).join('')}
                                        </select>
                                        <select id="med_cat_sel_${med.id}" style="width:100%;padding:0.35rem;border:1px solid var(--border-color);border-radius:0.25rem;">
                                            ${currentCategoriesList.map(c => `<option value="${c.id}" ${c.id == med.category_id ? 'selected' : ''}>${c.name}</option>`).join('')}
                                        </select>
                                    </div>
                                </td>
                                <td>
                                    <div style="display:flex; flex-direction:column; gap:0.4rem;">
                                        <input type="text" value="${med.manufacturer || ''}" id="manufacturer_${med.id}" style="width:100%;padding:0.35rem;border:1px solid var(--border-color);border-radius:0.25rem;" placeholder="Manufacturer">
                                        <div style="display:flex; gap:0.4rem;">
                                            <div style="position:relative; flex:1; display:flex; align-items:center;">
                                                <span style="position:absolute; left:8px; color:var(--text-muted); font-size:0.9rem; font-weight:600;">₱</span>
                                                <input type="number" step="0.01" value="${med.price}" id="price_${med.id}" style="width:100%;padding:0.35rem 0.5rem 0.35rem 1.25rem;border:1px solid var(--border-color);border-radius:0.25rem;font-weight:700;color:var(--primary);" placeholder="Price">
                                            </div>
                                            <input type="number" value="${med.stock}" id="stock_${med.id}" style="flex:1;width:100%;padding:0.35rem 0.5rem;border:1px solid var(--border-color);border-radius:0.25rem;font-weight:600;" placeholder="Stock">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="display:flex; flex-direction:column; gap:0.4rem;">
                                        <button onclick="updateMedicine(${med.id})" class="btn btn-secondary" style="width:100%;padding:0.35rem 0.75rem;">Update</button>
                                        <button onclick="deleteMedicine(${med.id})" class="btn btn-danger" style="width:100%;padding:0.35rem 0.75rem;">Delete</button>
                                    </div>
                                </td>
                            `;
                            medBody.appendChild(tr);
                        });
                    }

                    // Populate Table 2: Generics
                    const brandBody = document.getElementById('brandTableBody');
                    brandBody.innerHTML = '';
                    if (currentBrandsList.length === 0) {
                        brandBody.innerHTML = `<tr><td colspan="2" style="text-align:center;padding:2rem;color:var(--text-muted);">No active ingredients found.</td></tr>`;
                    } else {
                        currentBrandsList.forEach(b => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td><input type="text" value="${b.name}" id="generic_name_${b.id}" style="width:100%;padding:0.35rem;border:1px solid var(--border-color);border-radius:0.25rem;font-weight:600;"></td>
                                <td style="display:flex;gap:0.5rem;">
                                    <button onclick="updateBrand(${b.id})" class="btn btn-secondary">Update</button>
                                    <button onclick="deleteBrand(${b.id})" class="btn btn-danger">Delete</button>
                                </td>
                            `;
                            brandBody.appendChild(tr);
                        });
                    }

                    // Populate Table 3: Categories
                    const catBody = document.getElementById('catTableBody');
                    catBody.innerHTML = '';
                    if (currentCategoriesList.length === 0) {
                        catBody.innerHTML = `<tr><td colspan="2" style="text-align:center;padding:2rem;color:var(--text-muted);">No categories found.</td></tr>`;
                    } else {
                        currentCategoriesList.forEach(c => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td><input type="text" value="${c.name}" id="cat_name_${c.id}" style="width:100%;padding:0.35rem;border:1px solid var(--border-color);border-radius:0.25rem;"></td>
                                <td style="display:flex;gap:0.5rem;">
                                    <button onclick="updateCategory(${c.id})" class="btn btn-secondary">Update</button>
                                    <button onclick="deleteCategory(${c.id})" class="btn btn-danger">Delete</button>
                                </td>
                            `;
                            catBody.appendChild(tr);
                        });
                    }
                });
        }

        function toggleStoreStatus() {
            fetch(`api/inventory.php?action=toggle_status&pharma=${pharmaCode}`)
                .then(res => res.json())
                .then(() => loadData());
        }

        // --- PRODUCT ACTIONS ---
        function updateMedicine(id) {
            const brand_name = document.getElementById(`brand_name_${id}`).value.trim();
            const strength = document.getElementById(`strength_${id}`).value.trim();
            const medicine_id = document.getElementById(`med_gen_sel_${id}`).value;
            const category_id = document.getElementById(`med_cat_sel_${id}`).value;
            const manufacturer = document.getElementById(`manufacturer_${id}`).value.trim();
            const price = document.getElementById(`price_${id}`).value;
            const stock = document.getElementById(`stock_${id}`).value;

            // 1. Client-side Input Validations
            if (!brand_name) {
                alert("Error: Brand Name cannot be empty.");
                return;
            }
            if (!strength) {
                alert("Error: Strength cannot be empty.");
                return;
            }
            if (!medicine_id) {
                alert("Error: Active Ingredient must be selected.");
                return;
            }
            if (!category_id) {
                alert("Error: Category must be selected.");
                return;
            }
            if (price === "" || parseFloat(price) < 0) {
                alert("Error: Price must be a positive number.");
                return;
            }
            if (stock === "" || parseInt(stock) < 0) {
                alert("Error: Stock cannot be negative.");
                return;
            }

            // 2. Unchanged Verification
            const original = currentMedicinesList.find(m => m.id == id);
            if (original) {
                const isUnchanged = 
                    original.brand_name === brand_name &&
                    original.strength === strength &&
                    original.medicine_id == medicine_id &&
                    original.category_id == category_id &&
                    original.manufacturer === manufacturer &&
                    parseFloat(original.price) === parseFloat(price) &&
                    parseInt(original.stock) === parseInt(stock);
                
                if (isUnchanged) {
                    alert("No changes detected. The product details are already up-to-date.");
                    return;
                }
            }

            // 3. API Execution with Error Handling
            fetch(`api/inventory.php?action=update&pharma=${pharmaCode}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, brand_name, strength, medicine_id, category_id, price, stock, manufacturer })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'error') {
                        alert("Error updating product: " + data.message);
                        return;
                    }
                    
                    // Sync to admin console with custom update action details
                    fetch('api/sync.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ 
                            pharmacy_code: pharmaCode, 
                            custom_action: 'PRODUCT_UPDATE',
                            custom_message: `System received an update from ${pharmaCode === 'laurents' ? "Laurent's Pharmacy" : pharmaCode === 'jrm' ? "JRM DOCTORS Pharmacy" : "D' Rite Aid Generics Pharmacy"}: Product SKU details for '${brand_name} ${strength}' were updated. Current stock is at ${stock} units, priced at ₱${price}.`
                        })
                    })
                        .then(() => {
                            document.getElementById('syncBanner').style.display = 'flex';
                            alert("Product SKU details updated successfully!");
                            loadData();
                        })
                        .catch(err => {
                            console.error("Admin sync failed:", err);
                            loadData();
                        });
                })
                .catch(err => {
                    alert("A connection error occurred while updating the product SKU.");
                });
        }
        function deleteMedicine(id) {
            if (!confirm("Delete product SKU?")) return;
            const original = currentMedicinesList.find(m => m.id == id);
            const medName = original ? `${original.brand_name} ${original.strength}` : `SKU #${id}`;

            fetch(`api/inventory.php?action=delete&pharma=${pharmaCode}`, { 
                method: 'POST', 
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id }) 
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'error') {
                    alert("Error deleting product SKU: " + data.message);
                } else {
                    // Sync delete event to admin
                    fetch('api/sync.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            pharmacy_code: pharmaCode,
                            custom_action: 'PRODUCT_DELETE',
                            custom_message: `System received an update from ${pharmaCode === 'laurents' ? "Laurent's Pharmacy" : pharmaCode === 'jrm' ? "JRM DOCTORS Pharmacy" : "D' Rite Aid Generics Pharmacy"}: Product SKU '${medName}' was removed from the inventory.`
                        })
                    }).finally(() => {
                        alert("Product SKU deleted successfully!");
                        loadData();
                    });
                }
            })
            .catch(err => {
                alert("A connection error occurred while deleting the product SKU.");
            });
        }
        function addMedicine(e) {
            e.preventDefault();
            const brand_name = document.getElementById('medBrandName').value.trim();
            const strength = document.getElementById('medStrength').value.trim();
            const medicine_id = document.getElementById('medBrandSelect').value;
            const category_id = document.getElementById('medCatSelect').value;
            const manufacturer = document.getElementById('medMan').value.trim();
            const price = document.getElementById('medPrice').value;
            const stock = document.getElementById('medStock').value;

            if (!brand_name) {
                alert("Error: Brand Name is required.");
                return;
            }
            if (!strength) {
                alert("Error: Strength is required.");
                return;
            }
            if (!medicine_id) {
                alert("Error: Active Ingredient must be selected.");
                return;
            }
            if (!category_id) {
                alert("Error: Category must be selected.");
                return;
            }
            if (price === "" || parseFloat(price) < 0) {
                alert("Error: Price must be a positive number.");
                return;
            }
            if (stock === "" || parseInt(stock) < 0) {
                alert("Error: Stock cannot be negative.");
                return;
            }

            fetch(`api/inventory.php?action=add&pharma=${pharmaCode}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ brand_name, strength, medicine_id, category_id, price, stock, manufacturer })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'error') {
                        alert("Error adding product SKU: " + data.message);
                    } else {
                        // Sync add event to admin
                        fetch('api/sync.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                pharmacy_code: pharmaCode,
                                custom_action: 'PRODUCT_ADD',
                                custom_message: `System received an update from ${pharmaCode === 'laurents' ? "Laurent's Pharmacy" : pharmaCode === 'jrm' ? "JRM DOCTORS Pharmacy" : "D' Rite Aid Generics Pharmacy"}: New product SKU '${brand_name} ${strength}' added with ${stock} units in stock, priced at ₱${price}.`
                            })
                        }).finally(() => {
                            alert("Product SKU added successfully!");
                            closeModal('medicineModal');
                            document.getElementById('medForm').reset();
                            loadData();
                        });
                    }
                })
                .catch(err => {
                    alert("A connection error occurred while creating the product SKU.");
                });
        }

        // --- GENERIC ACTIONS ---
        function updateBrand(id) {
            const brand_name = document.getElementById(`generic_name_${id}`).value.trim();
            
            if (!brand_name) {
                alert("Error: Generic active ingredient name cannot be empty.");
                return;
            }

            const original = currentBrandsList.find(b => b.id == id);
            if (original && original.name === brand_name) {
                alert("No changes detected. The active ingredient name is already up-to-date.");
                return;
            }
            const oldName = original ? original.name : `Ingredient #${id}`;

            fetch(`api/inventory.php?action=update_brand&pharma=${pharmaCode}`, { 
                method: 'POST', 
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, brand_name }) 
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'error') {
                        alert("Error updating active ingredient: " + data.message);
                    } else {
                        // Sync generic rename event to admin
                        fetch('api/sync.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                pharmacy_code: pharmaCode,
                                custom_action: 'GENERIC_UPDATE',
                                custom_message: `System received an update from ${pharmaCode === 'laurents' ? "Laurent's Pharmacy" : pharmaCode === 'jrm' ? "JRM DOCTORS Pharmacy" : "D' Rite Aid Generics Pharmacy"}: Generic active ingredient renamed from '${oldName}' to '${brand_name}'.`
                            })
                        }).finally(() => {
                            alert("Generic active ingredient updated successfully!");
                            loadData();
                        });
                    }
                })
                .catch(err => {
                    alert("A connection error occurred while updating the active ingredient.");
                });
        }
        function deleteBrand(id) {
            if (!confirm("Delete generic drug? All linked product SKUs will be deleted as well.")) return;
            const original = currentBrandsList.find(b => b.id == id);
            const brandName = original ? original.name : `Ingredient #${id}`;

            fetch(`api/inventory.php?action=delete_brand&pharma=${pharmaCode}`, { 
                method: 'POST', 
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id }) 
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'error') {
                        alert("Error deleting generic drug: " + data.message);
                    } else {
                        // Sync generic delete event to admin
                        fetch('api/sync.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                pharmacy_code: pharmaCode,
                                custom_action: 'GENERIC_DELETE',
                                custom_message: `System received an update from ${pharmaCode === 'laurents' ? "Laurent's Pharmacy" : pharmaCode === 'jrm' ? "JRM DOCTORS Pharmacy" : "D' Rite Aid Generics Pharmacy"}: Generic active ingredient '${brandName}' was removed from the dictionary.`
                            })
                        }).finally(() => {
                            alert("Generic drug deleted successfully!");
                            loadData();
                        });
                    }
                })
                .catch(err => {
                    alert("A connection error occurred while deleting the generic drug.");
                });
        }
        function addBrand(e) {
            e.preventDefault();
            const brand_name = document.getElementById('brandNameInput').value.trim();
            if (!brand_name) {
                alert("Error: Generic active ingredient name cannot be empty.");
                return;
            }

            fetch(`api/inventory.php?action=add_brand&pharma=${pharmaCode}`, { 
                method: 'POST', 
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ brand_name }) 
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'error') {
                        alert("Error adding generic active ingredient: " + data.message);
                    } else {
                        // Sync generic add event to admin
                        fetch('api/sync.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                pharmacy_code: pharmaCode,
                                custom_action: 'GENERIC_ADD',
                                custom_message: `System received an update from ${pharmaCode === 'laurents' ? "Laurent's Pharmacy" : pharmaCode === 'jrm' ? "JRM DOCTORS Pharmacy" : "D' Rite Aid Generics Pharmacy"}: New generic active ingredient '${brand_name}' added to dictionary.`
                            })
                        }).finally(() => {
                            alert("Generic active ingredient added successfully!");
                            closeModal('brandModal');
                            document.getElementById('brandForm').reset();
                            loadData();
                        });
                    }
                })
                .catch(err => {
                    alert("A connection error occurred while adding the active ingredient.");
                });
        }

        // --- CATEGORY ACTIONS ---
        function updateCategory(id) {
            const category_name = document.getElementById(`cat_name_${id}`).value.trim();
            
            if (!category_name) {
                alert("Error: Category name cannot be empty.");
                return;
            }

            const original = currentCategoriesList.find(c => c.id == id);
            if (original && original.name === category_name) {
                alert("No changes detected. The category name is already up-to-date.");
                return;
            }
            const oldName = original ? original.name : `Category #${id}`;

            fetch(`api/inventory.php?action=update_category&pharma=${pharmaCode}`, { 
                method: 'POST', 
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id, category_name }) 
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'error') {
                        alert("Error updating category: " + data.message);
                    } else {
                        // Sync category update to admin
                        fetch('api/sync.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                pharmacy_code: pharmaCode,
                                custom_action: 'CATEGORY_UPDATE',
                                custom_message: `System received an update from ${pharmaCode === 'laurents' ? "Laurent's Pharmacy" : pharmaCode === 'jrm' ? "JRM DOCTORS Pharmacy" : "D' Rite Aid Generics Pharmacy"}: Inventory category renamed from '${oldName}' to '${category_name}'.`
                            })
                        }).finally(() => {
                            alert("Category updated successfully!");
                            loadData();
                        });
                    }
                })
                .catch(err => {
                    alert("A connection error occurred while updating the category.");
                });
        }
        function deleteCategory(id) {
            if (!confirm("Delete category?")) return;
            const original = currentCategoriesList.find(c => c.id == id);
            const catName = original ? original.name : `Category #${id}`;

            fetch(`api/inventory.php?action=delete_category&pharma=${pharmaCode}`, { 
                method: 'POST', 
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id }) 
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'error') {
                        alert("Error deleting category: " + data.message);
                    } else {
                        // Sync category delete to admin
                        fetch('api/sync.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                pharmacy_code: pharmaCode,
                                custom_action: 'CATEGORY_DELETE',
                                custom_message: `System received an update from ${pharmaCode === 'laurents' ? "Laurent's Pharmacy" : pharmaCode === 'jrm' ? "JRM DOCTORS Pharmacy" : "D' Rite Aid Generics Pharmacy"}: Inventory category '${catName}' was removed.`
                            })
                        }).finally(() => {
                            alert("Category deleted successfully!");
                            loadData();
                        });
                    }
                })
                .catch(err => {
                    alert("A connection error occurred while deleting the category.");
                });
        }
        function addCategory(e) {
            e.preventDefault();
            const category_name = document.getElementById('catNameInput').value.trim();
            if (!category_name) {
                alert("Error: Category name cannot be empty.");
                return;
            }

            fetch(`api/inventory.php?action=add_category&pharma=${pharmaCode}`, { 
                method: 'POST', 
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ category_name }) 
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'error') {
                        alert("Error adding category: " + data.message);
                    } else {
                        // Sync category add to admin
                        fetch('api/sync.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                pharmacy_code: pharmaCode,
                                custom_action: 'CATEGORY_ADD',
                                custom_message: `System received an update from ${pharmaCode === 'laurents' ? "Laurent's Pharmacy" : pharmaCode === 'jrm' ? "JRM DOCTORS Pharmacy" : "D' Rite Aid Generics Pharmacy"}: New inventory category '${category_name}' was created.`
                            })
                        }).finally(() => {
                            alert("Category added successfully!");
                            closeModal('categoryModal');
                            document.getElementById('catForm').reset();
                            loadData();
                        });
                    }
                })
                .catch(err => {
                    alert("A connection error occurred while adding the category.");
                });
        }

        function openModal(id) { document.getElementById(id).classList.add('active'); }
        function closeModal(id) { document.getElementById(id).classList.remove('active'); }

        loadData();
    </script>
</body>

</html>