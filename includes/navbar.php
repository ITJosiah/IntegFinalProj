<?php
// includes/navbar.php - Auto-detection version with customer/guest awareness

$navItems = [
        'home'        => ['label' => 'Home',         'href' => 'customer_dashboard.php'],
        'pharmacies'  => ['label' => 'Pharmacies',  'href' => 'customer_pharmacies.php'],
        'support'     => ['label' => 'About',     'href' => 'customer_about.php'],
];

// Get current page filename
$currentFile = basename($_SERVER['PHP_SELF']);

// Map filenames to nav keys
$pageToNavKey = [
        'customer_dashboard.php'   => 'home',
        'customer_pharmacies.php'  => 'pharmacies',
        'customer_about.php'       => 'support'
];

// Determine active page
$activeNavKey = isset($pageToNavKey[$currentFile]) ? $pageToNavKey[$currentFile] : '';

// Map pharmacy codes to names
$pharmaCode = isset($_SESSION['pharma_code']) ? $_SESSION['pharma_code'] : '';
$pharmaNames = [
        'laurents' => "Laurent's Pharmacy",
        'jrm'      => "JRM DOCTORS Pharmacy",
        'riteaid'  => "D' Rite Aid Generics Pharmacy",
];
$pharmaName = $pharmaNames[$pharmaCode] ?? "Pharmacy Portal";
?>
<nav class="navbar">
    <div class="nav-container">
        <a href="customer_dashboard.php" class="brand" style="display:flex; align-items:center; gap:0.35rem;">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                 stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                <line x1="12" y1="8" x2="12" y2="16"></line>
                <line x1="8" y1="12" x2="16" y2="12"></line>
            </svg>
            <span>Pharma<span class="sync-part" style="color: black;">Sync</span></span>
        </a>

        <ul class="nav-tabs">
            <?php foreach ($navItems as $key => $item): ?>
                <li>
                    <a href="<?= htmlspecialchars($item['href']) ?>"
                       class="<?= ($activeNavKey === $key) ? 'active' : '' ?>">
                        <?= htmlspecialchars($item['label']) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="nav-user" style="display: flex; align-items: center; gap: 0.5rem;">
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <!-- Admin portal badge + logout -->
                <a href="admin_dashboard.php" class="portal-badge" title="Go to Admin Dashboard">
                    ADMIN PORTAL: <?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?>
                </a>
                <a href="logout.php" class="btn-switch-role">Logout</a>

            <?php elseif (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'pharmacy'): ?>
                <!-- Pharmacy portal badge + logout -->
                <a href="pharmacy_dashboard.php?pharma=<?= urlencode($pharmaCode) ?>" class="portal-badge" title="Go to Pharmacy Dashboard">
                    PORTAL: <?= htmlspecialchars($pharmaName) ?>
                </a>
                <a href="logout.php" class="btn-switch-role">Logout</a>

            <?php elseif (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'customer'): ?>
                <!-- Logged-in customer: show name + logout -->
                <span class="portal-badge static" onclick="openEditProfileModal()" style="background: #D1FAE5; color: #065F46; border-color: #A7F3D0; cursor: pointer; transition: all 0.2s ease;" title="Edit Profile">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="margin-right: 0.2rem; vertical-align: -1px;">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    <span id="portalUserName"><?= htmlspecialchars($_SESSION['user_name']) ?></span>
                </span>
                <a href="logout.php" class="btn-switch-role">Logout</a>

                <!-- Edit Profile Modal for Customer -->
                <div id="editProfileModal" class="fda-modal" style="display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); align-items: flex-start; justify-content: center; padding: 4rem 1.5rem 1.5rem 1.5rem;">
                    <div class="fda-modal-content" style="background: white; border-radius: 1.25rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid #E2E8F0; max-width: 450px; width: 100%; max-height: 85vh; display: flex; flex-direction: column; overflow: hidden; animation: fadeUp 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);">
                        <div class="fda-modal-header" style="padding: 1.5rem 1.75rem; border-bottom: 1px solid #E2E8F0; display: flex; justify-content: space-between; align-items: flex-start;">
                            <div>
                                <h3 style="margin:0; font-weight:800; color:#0F172A; font-size: 1.4rem;">Edit Profile</h3>
                                <p style="color: #2563EB; font-weight: 600; font-size: 0.9rem; margin: 0.25rem 0 0 0;">Update your customer details</p>
                            </div>
                            <button onclick="closeEditProfileModal()" style="background:none; border:none; font-size:1.75rem; color:#94A3B8; cursor:pointer; line-height:1;">&times;</button>
                        </div>
                        <div class="fda-modal-body" style="padding: 1.5rem 1.75rem; overflow-y: auto; text-align: left;">
                            <form id="editProfileForm" onsubmit="submitEditProfile(event)">
                                <div id="editProfileAlert" style="display:none; padding: 0.75rem 1rem; border-radius: 0.75rem; font-size: 0.825rem; font-weight: 500; margin-bottom: 1rem;"></div>
                                
                                <div style="margin-bottom: 1rem;">
                                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.4rem; text-transform: uppercase; letter-spacing: 0.05em;">Full Name</label>
                                    <input type="text" id="editFullName" name="full_name" required style="width: 100%; padding: 0.7rem 1rem; border: 1.5px solid #E2E8F0; border-radius: 0.5rem; background: #F8FAFC; font-size: 0.95rem; font-family: 'Inter', sans-serif;">
                                </div>

                                <div style="margin-bottom: 1rem;">
                                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.4rem; text-transform: uppercase; letter-spacing: 0.05em;">Username</label>
                                    <input type="text" id="editUsername" name="username" required style="width: 100%; padding: 0.7rem 1rem; border: 1.5px solid #E2E8F0; border-radius: 0.5rem; background: #F8FAFC; font-size: 0.95rem; font-family: 'Inter', sans-serif;">
                                </div>

                                <div style="margin-bottom: 1rem;">
                                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.4rem; text-transform: uppercase; letter-spacing: 0.05em;">Email</label>
                                    <input type="email" id="editEmail" name="email" required style="width: 100%; padding: 0.7rem 1rem; border: 1.5px solid #E2E8F0; border-radius: 0.5rem; background: #F8FAFC; font-size: 0.95rem; font-family: 'Inter', sans-serif;">
                                </div>
                                
                                <div style="margin-bottom: 1.5rem;">
                                    <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #475569; margin-bottom: 0.4rem; text-transform: uppercase; letter-spacing: 0.05em;">New Password <span style="color:#94A3B8; font-size:0.75rem; font-weight:normal; text-transform:none;">(Leave blank to keep current)</span></label>
                                    <input type="password" name="password" minlength="6" placeholder="Enter new password" style="width: 100%; padding: 0.7rem 1rem; border: 1.5px solid #E2E8F0; border-radius: 0.5rem; background: #F8FAFC; font-size: 0.95rem; font-family: 'Inter', sans-serif;">
                                </div>

                                <button type="submit" style="width: 100%; padding: 0.85rem; font-size: 0.95rem; font-weight: 700; border-radius: 0.75rem; background: linear-gradient(135deg, #2563EB 0%, #3B82F6 100%); color: white; border: none; cursor: pointer; box-shadow: 0 4px 14px rgba(37, 99, 235, 0.25);">Save Changes</button>
                            </form>
                        </div>
                    </div>
                </div>

                <script>
                    async function openEditProfileModal() {
                        document.getElementById('editProfileModal').style.display = 'flex';
                        document.getElementById('editProfileAlert').style.display = 'none';
                        document.getElementById('editProfileForm').reset();
                        try {
                            const res = await fetch('api/update_profile.php');
                            const result = await res.json();
                            if (result.status === 'success') {
                                document.getElementById('editFullName').value = result.data.full_name;
                                document.getElementById('editUsername').value = result.data.username;
                                document.getElementById('editEmail').value = result.data.email;
                            }
                        } catch(err) {
                            console.error("Error fetching profile", err);
                        }
                    }

                    function closeEditProfileModal() {
                        document.getElementById('editProfileModal').style.display = 'none';
                    }

                    async function submitEditProfile(e) {
                        e.preventDefault();
                        const form = e.target;
                        const formData = new FormData(form);
                        const alertBox = document.getElementById('editProfileAlert');
                        
                        try {
                            const response = await fetch('api/update_profile.php', {
                                method: 'POST',
                                body: formData
                            });
                            const data = await response.json();
                            
                            if (data.status === 'success') {
                                alertBox.style.display = 'block';
                                alertBox.style.background = '#d1fae5';
                                alertBox.style.color = '#065f46';
                                alertBox.style.border = '1px solid #a7f3d0';
                                alertBox.innerHTML = `<strong>Success!</strong> ${data.message}`;
                                
                                document.getElementById('portalUserName').innerText = document.getElementById('editFullName').value;
                                
                                setTimeout(() => { 
                                    alertBox.style.display = 'none'; 
                                    closeEditProfileModal();
                                }, 2000);
                            } else {
                                alertBox.style.display = 'block';
                                alertBox.style.background = '#fee2e2';
                                alertBox.style.color = '#991b1b';
                                alertBox.style.border = '1px solid #fecaca';
                                alertBox.innerHTML = `<strong>Error!</strong> ${data.message}`;
                            }
                        } catch(err) {
                            alertBox.style.display = 'block';
                            alertBox.style.background = '#fee2e2';
                            alertBox.style.color = '#991b1b';
                            alertBox.style.border = '1px solid #fecaca';
                            alertBox.innerHTML = `<strong>Error!</strong> Failed to update profile.`;
                        }
                    }
                </script>

            <?php else: ?>
                <!-- Guest: show login + register -->
                <a href="index.php" class="btn-switch-role" style="background: var(--primary); color: white; border-color: var(--primary);">Sign In</a>
                <a href="register.php" class="btn-switch-role">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>