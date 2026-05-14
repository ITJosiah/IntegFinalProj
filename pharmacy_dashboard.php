<?php
session_start();
$pharma = isset($_GET['pharma']) ? $_GET['pharma'] : (isset($_SESSION['pharma_code']) ? $_SESSION['pharma_code'] : 'laurents');
$_SESSION['pharma_code'] = $pharma;

$names = [
    'laurents' => "Laurent's Pharmacy",
    'jrmp' => "JRMP Doctors Pharmacy",
    'jas5' => "JAS5 Pharmacy"
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
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>
                <span><span style="color: var(--primary);">Pharma</span><span style="color: black;">Sync</span></span>
            </a>
            <div class="nav-links">
                <span class="badge badge-warning" style="background: #E0E7FF; color: var(--primary);">Portal: <?php echo $pharmaName; ?></span>
                <a href="index.php" class="btn btn-outline">Switch Role</a>
            </div>
        </div>
    </nav>

    <main class="container animate-fade">
        <div id="syncBanner" class="alert-banner animate-fade">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                <span id="syncBannerText">Inventory Synchronized!</span>
            </div>
            <button onclick="document.getElementById('syncBanner').style.display='none'" style="background:none;border:none;cursor:pointer;font-weight:bold;color:#065F46;">✕</button>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h1 style="font-size: 2rem; font-weight: 800; color: var(--text-main); margin-bottom: 0.25rem;"><?php echo $pharmaName; ?> ERP</h1>
            </div>
            <div>
                <button id="toggleStatusBtn" onclick="toggleStoreStatus()" class="btn btn-outline">Store Status: Loading...</button>
            </div>
        </div>

        <!-- ERP Tabs Navigation -->
        <div class="erp-tabs">
            <button class="erp-tab-btn active" onclick="switchTab('medicines')">Medicines</button>
            <button class="erp-tab-btn" onclick="switchTab('brands')">Brands</button>
            <button class="erp-tab-btn" onclick="switchTab('categories')">Categories</button>
        </div>

        <!-- ================= TAB 1: MEDICINES ================= -->
        <div id="tab_medicines" class="tab-content active animate-fade">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                <h2 style="font-size: 1.35rem; font-weight: 700;">Medicines Inventory</h2>
                <div style="display: flex; gap: 0.75rem; align-items: center;">
                    <input type="text" onkeyup="filterSpecificTable('medTableBody', this.value)" placeholder="🔍 Search medicines..." class="form-input" style="width: 260px; background: white; border-radius: 2rem; padding-left: 1.25rem;">
                    <button onclick="openModal('medicineModal')" class="btn btn-primary">Add Medicine</button>
                </div>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Generic Name</th>
                            <th>Brand Name</th>
                            <th>Category</th>
                            <th>Price (₱)</th>
                            <th>Current Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="medTableBody">
                        <tr><td colspan="6" style="text-align: center; padding: 2rem; color: var(--text-muted);">Loading medicines...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ================= TAB 2: BRANDS ================= -->
        <div id="tab_brands" class="tab-content animate-fade">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                <h2 style="font-size: 1.35rem; font-weight: 700;">Brands Directory</h2>
                <div style="display: flex; gap: 0.75rem; align-items: center;">
                    <input type="text" onkeyup="filterSpecificTable('brandTableBody', this.value)" placeholder="🔍 Search brands..." class="form-input" style="width: 260px; background: white; border-radius: 2rem; padding-left: 1.25rem;">
                    <button onclick="openModal('brandModal')" class="btn btn-primary">Add Brand</button>
                </div>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Brand Name</th>
                            <th>Assigned Category</th>
                            <th>Manufacturer</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="brandTableBody">
                        <tr><td colspan="4" style="text-align: center; padding: 2rem; color: var(--text-muted);">Loading brands...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ================= TAB 3: CATEGORIES ================= -->
        <div id="tab_categories" class="tab-content animate-fade">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 1rem;">
                <h2 style="font-size: 1.35rem; font-weight: 700;">Categories Directory</h2>
                <div style="display: flex; gap: 0.75rem; align-items: center;">
                    <input type="text" onkeyup="filterSpecificTable('catTableBody', this.value)" placeholder="🔍 Search categories..." class="form-input" style="width: 260px; background: white; border-radius: 2rem; padding-left: 1.25rem;">
                    <button onclick="openModal('categoryModal')" class="btn btn-primary">Add Category</button>
                </div>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Category Name</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="catTableBody">
                        <tr><td colspan="2" style="text-align: center; padding: 2rem; color: var(--text-muted);">Loading categories...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- ================= MODALS ================= -->

    <!-- Add Medicine Modal -->
    <div id="medicineModal" class="modal-overlay">
        <div class="modal">
            <h2 style="margin-bottom: 1.5rem; font-weight: 700;">Add New Medicine SKU</h2>
            <form id="medForm" onsubmit="addMedicine(event)">
                <div class="form-group">
                    <label class="form-label">Generic Name</label>
                    <input type="text" id="medGenName" class="form-input" placeholder="e.g. Paracetamol" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Select Assigned Brand</label>
                    <select id="medBrandSelect" class="form-input" style="background: white;" required></select>
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
                    <button type="submit" class="btn btn-primary">Save Medicine</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Add Brand Modal -->
    <div id="brandModal" class="modal-overlay">
        <div class="modal">
            <h2 style="margin-bottom: 1.5rem; font-weight: 700;">Add New Brand Record</h2>
            <form id="brandForm" onsubmit="addBrand(event)">
                <div class="form-group">
                    <label class="form-label">Brand Name</label>
                    <input type="text" id="brandNameInput" class="form-input" placeholder="e.g. Biogesic 500mg" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Assign Category</label>
                    <select id="brandCatSelect" class="form-input" style="background: white;" required></select>
                </div>
                <div class="form-group">
                    <label class="form-label">Manufacturer</label>
                    <input type="text" id="brandManInput" class="form-input" placeholder="e.g. Unilab" required>
                </div>
                <div style="display: flex; justify-content: flex-end; gap: 1rem; margin-top: 1.5rem;">
                    <button type="button" onclick="closeModal('brandModal')" class="btn btn-outline">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Brand</button>
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

        function switchTab(tabId) {
            document.querySelectorAll('.erp-tab-btn').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

            event.target.classList.add('active');
            document.getElementById('tab_' + tabId).classList.add('active');
        }

        function filterSpecificTable(tbodyId, query) {
            query = query.toLowerCase();
            const tbody = document.getElementById(tbodyId);
            if (!tbody) return;

            const rows = tbody.querySelectorAll('tr');
            rows.forEach(row => {
                if (row.cells.length === 1 && row.cells[0].colSpan > 1) return;

                let text = row.textContent.toLowerCase();
                row.querySelectorAll('input, select').forEach(elem => {
                    if (elem.tagName === 'INPUT' && elem.value) text += ' ' + elem.value.toLowerCase();
                    if (elem.tagName === 'SELECT' && elem.selectedIndex >= 0) {
                        const opt = elem.options[elem.selectedIndex];
                        if (opt) text += ' ' + opt.text.toLowerCase();
                    }
                });

                row.style.display = text.includes(query) ? '' : 'none';
            });
        }

        function loadData() {
            fetch(`api/inventory.php?action=get&pharma=${pharmaCode}`)
                .then(res => res.json())
                .then(data => {
                    currentMedicinesList = data.data;
                    currentBrandsList = data.brands;
                    currentCategoriesList = data.categories;

                    const statusBtn = document.getElementById('toggleStatusBtn');
                    if (data.is_open == 1) {
                        statusBtn.innerHTML = `Store Status: <span style="color:#10B981;font-weight:bold;">OPEN</span>`;
                    } else {
                        statusBtn.innerHTML = `Store Status: <span style="color:#EF4444;font-weight:bold;">CLOSED</span>`;
                    }

                    // Populate Dropdowns
                    const medBrandSel = document.getElementById('medBrandSelect');
                    const brandCatSel = document.getElementById('brandCatSelect');
                    medBrandSel.innerHTML = '<option value="">-- Select Brand --</option>';
                    brandCatSel.innerHTML = '<option value="">-- Select Category --</option>';

                    currentBrandsList.forEach(b => {
                        const opt = document.createElement('option');
                        opt.value = b.id;
                        opt.textContent = `${b.name} — [${b.category_name}]`;
                        medBrandSel.appendChild(opt);
                    });
                    currentCategoriesList.forEach(c => {
                        const opt = document.createElement('option');
                        opt.value = c.id;
                        opt.textContent = c.name;
                        brandCatSel.appendChild(opt);
                    });

                    // Populate Table 1: Medicines
                    const medBody = document.getElementById('medTableBody');
                    medBody.innerHTML = '';
                    if (currentMedicinesList.length === 0) {
                        medBody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--text-muted);">No medicines found.</td></tr>`;
                    } else {
                        currentMedicinesList.forEach(med => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td><input type="text" value="${med.generic_name}" id="gen_name_${med.id}" style="width:100%;padding:0.35rem;border:1px solid var(--border-color);border-radius:0.25rem;font-weight:600;color:var(--text-main);"></td>
                                <td>
                                    <select id="med_brand_sel_${med.id}" style="padding:0.35rem;border:1px solid var(--border-color);border-radius:0.25rem;">
                                        ${currentBrandsList.map(b => `<option value="${b.id}" ${b.id == med.brand_id ? 'selected' : ''}>${b.name}</option>`).join('')}
                                    </select>
                                </td>
                                <td><span style="background:#E2E8F0;padding:0.35rem 0.75rem;border-radius:1rem;font-size:0.85rem;font-weight:600;color:#334155;white-space:nowrap;display:inline-block;">${med.category || 'N/A'}</span></td>
                                <td><input type="number" step="0.01" value="${med.price}" id="price_${med.id}" style="width:80px;padding:0.35rem 0.5rem;border:1px solid var(--border-color);border-radius:0.25rem;font-weight:600;color:var(--primary);"></td>
                                <td><input type="number" value="${med.stock}" id="stock_${med.id}" style="width:80px;padding:0.35rem 0.5rem;border:1px solid var(--border-color);border-radius:0.25rem;font-weight:600;"></td>
                                <td style="display:flex;gap:0.5rem;align-items:center;">
                                    <button onclick="updateMedicine(${med.id})" class="btn btn-secondary">Update</button>
                                    <button onclick="deleteMedicine(${med.id})" class="btn btn-danger">Delete</button>
                                </td>
                            `;
                            medBody.appendChild(tr);
                        });
                    }

                    // Populate Table 2: Brands
                    const brandBody = document.getElementById('brandTableBody');
                    brandBody.innerHTML = '';
                    if (currentBrandsList.length === 0) {
                        brandBody.innerHTML = `<tr><td colspan="4" style="text-align:center;padding:2rem;color:var(--text-muted);">No brands found.</td></tr>`;
                    } else {
                        currentBrandsList.forEach(b => {
                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                                <td><input type="text" value="${b.name}" id="brand_name_${b.id}" style="width:100%;padding:0.35rem;border:1px solid var(--border-color);border-radius:0.25rem;"></td>
                                <td>
                                    <select id="brand_cat_${b.id}" style="padding:0.35rem;border:1px solid var(--border-color);border-radius:0.25rem;">
                                        ${currentCategoriesList.map(c => `<option value="${c.id}" ${c.id == b.category_id ? 'selected' : ''}>${c.name}</option>`).join('')}
                                    </select>
                                </td>
                                <td><input type="text" value="${b.manufacturer || ''}" id="brand_man_${b.id}" style="width:100%;padding:0.35rem;border:1px solid var(--border-color);border-radius:0.25rem;"></td>
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

        // --- MEDICINE ACTIONS ---
        function updateMedicine(id) {
            const generic_name = document.getElementById(`gen_name_${id}`).value;
            const brand_id = document.getElementById(`med_brand_sel_${id}`).value;
            const price = document.getElementById(`price_${id}`).value;
            const stock = document.getElementById(`stock_${id}`).value;

            fetch(`api/inventory.php?action=update&pharma=${pharmaCode}`, {
                method: 'POST',
                headers: {'Content-Type':'application/json'},
                body: JSON.stringify({ id, generic_name, brand_id, price, stock })
            })
            .then(res => res.json())
            .then(() => {
                fetch('api/sync.php', {
                    method: 'POST',
                    headers: {'Content-Type':'application/json'},
                    body: JSON.stringify({ pharmacy_code: pharmaCode, medicine_id: id, medicine_name: generic_name, qty_sold: 0, remaining_stock: stock })
                })
                .then(() => {
                    document.getElementById('syncBanner').style.display='flex';
                    loadData();
                });
            });
        }
        function deleteMedicine(id) {
            if (!confirm("Delete medicine?")) return;
            fetch(`api/inventory.php?action=delete&pharma=${pharmaCode}`, { method:'POST', body:JSON.stringify({id}) }).then(() => loadData());
        }
        function addMedicine(e) {
            e.preventDefault();
            const generic_name = document.getElementById('medGenName').value;
            const brand_id = document.getElementById('medBrandSelect').value;
            const price = document.getElementById('medPrice').value;
            const stock = document.getElementById('medStock').value;
            fetch(`api/inventory.php?action=add&pharma=${pharmaCode}`, { method:'POST', body:JSON.stringify({generic_name, brand_id, price, stock}) })
                .then(() => { closeModal('medicineModal'); document.getElementById('medForm').reset(); loadData(); });
        }

        // --- BRAND ACTIONS ---
        function updateBrand(id) {
            const brand_name = document.getElementById(`brand_name_${id}`).value;
            const category_id = document.getElementById(`brand_cat_${id}`).value;
            const manufacturer = document.getElementById(`brand_man_${id}`).value;
            fetch(`api/inventory.php?action=update_brand&pharma=${pharmaCode}`, { method:'POST', body:JSON.stringify({id, brand_name, category_id, manufacturer}) })
                .then(() => { alert("Brand updated!"); loadData(); });
        }
        function deleteBrand(id) {
            if (!confirm("Delete brand?")) return;
            fetch(`api/inventory.php?action=delete_brand&pharma=${pharmaCode}`, { method:'POST', body:JSON.stringify({id}) }).then(() => loadData());
        }
        function addBrand(e) {
            e.preventDefault();
            const brand_name = document.getElementById('brandNameInput').value;
            const category_id = document.getElementById('brandCatSelect').value;
            const manufacturer = document.getElementById('brandManInput').value;
            fetch(`api/inventory.php?action=add_brand&pharma=${pharmaCode}`, { method:'POST', body:JSON.stringify({brand_name, category_id, manufacturer}) })
                .then(() => { closeModal('brandModal'); document.getElementById('brandForm').reset(); loadData(); });
        }

        // --- CATEGORY ACTIONS ---
        function updateCategory(id) {
            const category_name = document.getElementById(`cat_name_${id}`).value;
            fetch(`api/inventory.php?action=update_category&pharma=${pharmaCode}`, { method:'POST', body:JSON.stringify({id, category_name}) })
                .then(() => { alert("Category updated!"); loadData(); });
        }
        function deleteCategory(id) {
            if (!confirm("Delete category?")) return;
            fetch(`api/inventory.php?action=delete_category&pharma=${pharmaCode}`, { method:'POST', body:JSON.stringify({id}) }).then(() => loadData());
        }
        function addCategory(e) {
            e.preventDefault();
            const category_name = document.getElementById('catNameInput').value;
            fetch(`api/inventory.php?action=add_category&pharma=${pharmaCode}`, { method:'POST', body:JSON.stringify({category_name}) })
                .then(() => { closeModal('categoryModal'); document.getElementById('catForm').reset(); loadData(); });
        }

        function openModal(id) { document.getElementById(id).classList.add('active'); }
        function closeModal(id) { document.getElementById(id).classList.remove('active'); }

        loadData();
    </script>
</body>
</html>
