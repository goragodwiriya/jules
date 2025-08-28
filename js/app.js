document.addEventListener('DOMContentLoaded', () => {
    // --- Element References ---
    const appContent = document.getElementById('app-content');
    const navLinks = document.querySelectorAll('.nav-item a');

    const roomModal = document.getElementById('add-room-modal');
    const roomModalTitle = document.getElementById('modal-title');
    const roomForm = document.getElementById('room-form');
    const roomModalCloseButton = roomModal.querySelector('.close-button');

    const tenantModal = document.getElementById('tenant-modal');
    const tenantModalTitle = document.getElementById('tenant-modal-title');
    const tenantForm = document.getElementById('tenant-form');
    const tenantModalCloseButton = tenantModal.querySelector('.close-button');

    const contractModal = document.getElementById('contract-modal');
    const contractForm = document.getElementById('contract-form');
    const contractModalCloseButton = contractModal.querySelector('.close-button');

    // --- State ---
    let currentRoomMode = 'create';
    let currentTenantMode = 'create';

    // --- Router ---
    const routes = {
        'dashboard': showDashboard,
        'rooms': showRooms,
        'tenants': showTenants,
        'contracts': showContracts,
        'billing': showBilling
    };

    function router(event) {
        event.preventDefault();
        const page = event.target.getAttribute('data-page');
        updateActiveLink(page);
        history.pushState({page}, null, `#${page}`);
        loadPage(page);
    }

    function loadPage(page) {
        (routes[page] || routes['dashboard'])();
    }

    function updateActiveLink(page) {
        navLinks.forEach(link => link.classList.remove('active'));
        const activeLink = document.querySelector(`.nav-item a[data-page="${page}"]`);
        if (activeLink) activeLink.classList.add('active');
    }

    // --- Page Rendering: Rooms ---
    async function showRooms() {
        appContent.innerHTML = `
            <div class="page-header"><h2>จัดการห้องพัก</h2><button id="add-room-btn" class="button-primary">เพิ่มห้องพัก</button></div>
            <div id="rooms-list">กำลังโหลด...</div>`;
        document.getElementById('add-room-btn').addEventListener('click', setupCreateRoomForm);
        try {
            const response = await fetch('api/rooms.php');
            if (!response.ok) throw new Error(`HTTP error! ${response.status}`);
            renderRooms((await response.json()).records || []);
        } catch (error) { document.getElementById('rooms-list').innerHTML = `<p class="error">เกิดข้อผิดพลาด: ${error.message}</p>`; }
    }

    function renderRooms(rooms) {
        const listContainer = document.getElementById('rooms-list');
        if (!listContainer) return;
        if (rooms.length === 0) { listContainer.innerHTML = '<p>ไม่พบข้อมูลห้องพัก</p>'; return; }
        const tableHTML = `
            <table class="data-table">
                <thead><tr><th>หมายเลขห้อง</th><th>ประเภท</th><th>ราคา</th><th>สถานะ</th><th>จัดการ</th></tr></thead>
                <tbody>${rooms.map(room => `
                    <tr>
                        <td>${room.room_number}</td><td>${room.room_type || '-'}</td><td>${Number(room.price).toLocaleString()}</td><td>${room.status}</td>
                        <td class="actions">
                            <button class="button-edit" data-id="${room.id}" data-type="room">แก้ไข</button>
                            <button class="button-delete" data-id="${room.id}" data-type="room">ลบ</button>
                        </td>
                    </tr>`).join('')}
                </tbody></table>`;
        listContainer.innerHTML = tableHTML;
        listContainer.addEventListener('click', handleTableActions);
    }

    // --- Page Rendering: Tenants ---
    async function showTenants() {
        appContent.innerHTML = `
            <div class="page-header"><h2>จัดการผู้เช่า</h2><button id="add-tenant-btn" class="button-primary">เพิ่มผู้เช่า</button></div>
            <div id="tenants-list">กำลังโหลด...</div>`;
        document.getElementById('add-tenant-btn').addEventListener('click', setupCreateTenantForm);
        try {
            const response = await fetch('api/tenants.php');
            if (!response.ok) throw new Error(`HTTP error! ${response.status}`);
            renderTenants((await response.json()).records || []);
        } catch (error) { document.getElementById('tenants-list').innerHTML = `<p class="error">เกิดข้อผิดพลาด: ${error.message}</p>`; }
    }

    function renderTenants(tenants) {
        const listContainer = document.getElementById('tenants-list');
        if (!listContainer) return;
        if (tenants.length === 0) { listContainer.innerHTML = '<p>ไม่พบข้อมูลผู้เช่า</p>'; return; }
        const tableHTML = `
            <table class="data-table">
                <thead><tr><th>ชื่อ-สกุล</th><th>เบอร์โทรศัพท์</th><th>อีเมล</th><th>จัดการ</th></tr></thead>
                <tbody>${tenants.map(tenant => `
                    <tr>
                        <td>${tenant.name}</td><td>${tenant.phone || '-'}</td><td>${tenant.email || '-'}</td>
                        <td class="actions">
                            <button class="button-edit" data-id="${tenant.id}" data-type="tenant">แก้ไข</button>
                            <button class="button-delete" data-id="${tenant.id}" data-type="tenant">ลบ</button>
                        </td>
                    </tr>`).join('')}
                </tbody></table>`;
        listContainer.innerHTML = tableHTML;
        listContainer.addEventListener('click', handleTableActions);
    }

    // --- Page Rendering: Contracts ---
    async function showContracts() {
        appContent.innerHTML = `
            <div class="page-header"><h2>จัดการสัญญา</h2><button id="add-contract-btn" class="button-primary">สร้างสัญญาใหม่</button></div>
            <div id="contracts-list">กำลังโหลด...</div>`;
        document.getElementById('add-contract-btn').addEventListener('click', setupCreateContractForm);
        try {
            const response = await fetch('api/contracts.php');
            if (!response.ok) throw new Error(`HTTP error! ${response.status}`);
            renderContracts((await response.json()).records || []);
        } catch (error) { document.getElementById('contracts-list').innerHTML = `<p class="error">เกิดข้อผิดพลาด: ${error.message}</p>`; }
    }

    function renderContracts(contracts) {
        const listContainer = document.getElementById('contracts-list');
        if (!listContainer) return;
        if (contracts.length === 0) { listContainer.innerHTML = '<p>ไม่พบข้อมูลสัญญา</p>'; return; }
        const tableHTML = `
            <table class="data-table">
                <thead><tr><th>หมายเลขห้อง</th><th>ผู้เช่า</th><th>วันที่เริ่มสัญญา</th><th>สถานะ</th><th>จัดการ</th></tr></thead>
                <tbody>${contracts.map(contract => `
                    <tr>
                        <td>${contract.room_number}</td><td>${contract.tenant_name}</td><td>${contract.start_date}</td><td>${contract.is_active ? 'Active' : 'Inactive'}</td>
                        <td class="actions"><button class="button-edit" data-id="${contract.id}" data-type="contract">ดู/แก้ไข</button></td>
                    </tr>`).join('')}
                </tbody></table>`;
        listContainer.innerHTML = tableHTML;
        listContainer.addEventListener('click', handleTableActions);
    }

    // --- Page Rendering: Other ---
    async function showBilling() {
        appContent.innerHTML = `
            <div class="page-header">
                <h2>จัดการบิล/ใบแจ้งหนี้</h2>
                <button id="generate-bills-btn" class="button-primary">สร้างบิลประจำเดือน</button>
            </div>
            <div id="bills-list">กำลังโหลด...</div>`;
        document.getElementById('generate-bills-btn').addEventListener('click', generateBills);
        try {
            const response = await fetch('api/bills.php');
            if (!response.ok) throw new Error(`HTTP error! ${response.status}`);
            renderBills((await response.json()).records || []);
        } catch (error) { document.getElementById('bills-list').innerHTML = `<p class="error">เกิดข้อผิดพลาด: ${error.message}</p>`; }
    }

    function renderBills(bills) {
        const listContainer = document.getElementById('bills-list');
        if (!listContainer) return;
        if (bills.length === 0) { listContainer.innerHTML = '<p>ไม่พบข้อมูลบิล</p>'; return; }
        const tableHTML = `
            <table class="data-table">
                <thead><tr><th>ห้อง</th><th>ผู้เช่า</th><th>วันที่สร้างบิล</th><th>วันครบกำหนด</th><th>ยอดรวม</th><th>สถานะ</th><th>จัดการ</th></tr></thead>
                <tbody>${bills.map(bill => `
                    <tr class="status-${bill.status}">
                        <td>${bill.room_number}</td><td>${bill.tenant_name}</td><td>${bill.bill_date}</td><td>${bill.due_date}</td><td>${Number(bill.total_amount).toLocaleString()}</td><td>${bill.status}</td>
                        <td class="actions">
                            ${bill.status === 'unpaid' ? `<button class="button-edit" data-id="${bill.id}" data-type="bill">จ่ายแล้ว</button>` : ''}
                        </td>
                    </tr>`).join('')}
                </tbody></table>`;
        listContainer.innerHTML = tableHTML;
    }

    // --- Event Handlers ---
    function handleTableActions(event) {
        const target = event.target;
        const id = target.dataset.id;
        const type = target.dataset.type;
        if (target.classList.contains('button-edit')) {
            if (type === 'room') setupEditRoomForm(id);
            else if (type === 'tenant') setupEditTenantForm(id);
            else if (type === 'contract') alert(`Viewing/Editing Contract ID: ${id} is not implemented yet.`);
            else if (type === 'bill') updateBillStatus(id, 'paid');
        } else if (target.classList.contains('button-delete')) {
            if (type === 'room') deleteRoom(id);
            else if (type === 'tenant') deleteTenant(id);
        }
    }

    async function handleFormSubmit(event, form, mode, type) {
        event.preventDefault();
        const apiEndpoint = `api/${type}/${mode}.php`;
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        try {
            const response = await fetch(apiEndpoint, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message);
            if (type === 'room') { closeRoomModal(); showRooms(); }
            else if (type === 'tenant') { closeTenantModal(); showTenants(); }
            else if (type === 'contract') { closeContractModal(); showContracts(); }
        } catch (error) { alert(`Error: ${error.message}`); }
    }

    // --- Modal and Form Logic ---
    function setupCreateRoomForm() { currentRoomMode = 'create'; roomForm.reset(); roomModalTitle.textContent = 'เพิ่มห้องพักใหม่'; roomModal.style.display = 'block'; }
    async function setupEditRoomForm(id) {
        currentRoomMode = 'update';
        try {
            const response = await fetch(`api/room/read_one.php?id=${id}`);
            if (!response.ok) throw new Error('Could not fetch room data.');
            const room = await response.json();
            roomForm.querySelector('#room_id').value = room.id;
            roomForm.querySelector('#room_number').value = room.room_number;
            roomForm.querySelector('#room_type').value = room.room_type;
            roomForm.querySelector('#price').value = room.price;
            roomForm.querySelector('#status').value = room.status;
            roomModalTitle.textContent = 'แก้ไขข้อมูลห้องพัก';
            roomModal.style.display = 'block';
        } catch(error) { alert(`Error: ${error.message}`); }
    }
    function closeRoomModal() { roomModal.style.display = 'none'; }

    function setupCreateTenantForm() { currentTenantMode = 'create'; tenantForm.reset(); tenantModalTitle.textContent = 'เพิ่มผู้เช่าใหม่'; tenantModal.style.display = 'block'; }
    async function setupEditTenantForm(id) {
        currentTenantMode = 'update';
        try {
            const response = await fetch(`api/tenant/read_one.php?id=${id}`);
            if (!response.ok) throw new Error('Could not fetch tenant data.');
            const tenant = await response.json();
            tenantForm.querySelector('#tenant_id').value = tenant.id;
            tenantForm.querySelector('#name').value = tenant.name;
            tenantForm.querySelector('#phone').value = tenant.phone;
            tenantForm.querySelector('#email').value = tenant.email;
            tenantForm.querySelector('#id_card').value = tenant.id_card;
            tenantForm.querySelector('#address').value = tenant.address;
            tenantModalTitle.textContent = 'แก้ไขข้อมูลผู้เช่า';
            tenantModal.style.display = 'block';
        } catch(error) { alert(`Error: ${error.message}`); }
    }
    function closeTenantModal() { tenantModal.style.display = 'none'; }

    async function setupCreateContractForm() {
        contractForm.reset();
        contractModal.style.display = 'block';
        const roomSelect = document.getElementById('contract-room-id');
        const tenantSelect = document.getElementById('contract-tenant-id');
        roomSelect.innerHTML = '<option>Loading...</option>';
        tenantSelect.innerHTML = '<option>Loading...</option>';

        try {
            const [roomsRes, tenantsRes] = await Promise.all([
                fetch('api/rooms/read_available.php'),
                fetch('api/tenants/read_unassigned.php')
            ]);

            const roomsData = await roomsRes.json();
            roomSelect.innerHTML = roomsData.records ? roomsData.records.map(r => `<option value="${r.id}">${r.room_number}</option>`).join('') : '<option>No rooms available</option>';

            const tenantsData = await tenantsRes.json();
            tenantSelect.innerHTML = tenantsData.records ? tenantsData.records.map(t => `<option value="${t.id}">${t.name}</option>`).join('') : '<option>No tenants available</option>';
        } catch (error) {
            alert('Failed to load data for contract form.');
            roomSelect.innerHTML = '<option>Error</option>';
            tenantSelect.innerHTML = '<option>Error</option>';
        }
    }
    function closeContractModal() { contractModal.style.display = 'none'; }

    // --- API Calls (Delete) ---
    async function deleteItem(type, id) {
        if (!confirm(`คุณแน่ใจหรือไม่ว่าต้องการลบ ${type} นี้?`)) return;
        try {
            const response = await fetch(`api/${type}/delete.php`, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify({ id }) });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message);
            if (type === 'room') showRooms();
            else if (type === 'tenant') showTenants();
        } catch (error) { alert(`Error: ${error.message}`); }
    }
    const deleteRoom = (id) => deleteItem('room', id);
    const deleteTenant = (id) => deleteItem('tenant', id);

    async function updateBillStatus(id, newStatus) {
        try {
            const response = await fetch('api/bill/update_status.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id, status: newStatus })
            });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message);
            showBilling(); // Refresh the list
        } catch (error) {
            alert(`Error: ${error.message}`);
        }
    }

    async function generateBills() {
        if (!confirm('คุณแน่ใจหรือไม่ว่าต้องการสร้างบิลสำหรับเดือนปัจจุบัน? \nการดำเนินการนี้จะไม่สร้างบิลซ้ำสำหรับสัญญาที่มีบิลในเดือนนี้อยู่แล้ว')) return;
        try {
            const response = await fetch('api/bills/generate.php', { method: 'POST' });
            const result = await response.json();
            if (!response.ok) throw new Error(result.message);
            alert(result.message); // Show success message
            showBilling(); // Refresh the list
        } catch (error) {
            alert(`Error: ${error.message}`);
        }
    }

    // --- Initial Setup ---
    navLinks.forEach(link => link.addEventListener('click', router));
    roomModalCloseButton.addEventListener('click', closeRoomModal);
    tenantModalCloseButton.addEventListener('click', closeTenantModal);
    contractModalCloseButton.addEventListener('click', closeContractModal);
    window.addEventListener('click', (event) => {
        if (event.target == roomModal) closeRoomModal();
        if (event.target == tenantModal) closeTenantModal();
        if (event.target == contractModal) closeContractModal();
    });
    roomForm.addEventListener('submit', (e) => handleFormSubmit(e, roomForm, currentRoomMode, 'room'));
    tenantForm.addEventListener('submit', (e) => handleFormSubmit(e, tenantForm, currentTenantMode, 'tenant'));
    contractForm.addEventListener('submit', (e) => handleFormSubmit(e, contractForm, 'create', 'contract'));

    const initialPage = window.location.hash.substring(1) || 'dashboard';
    loadPage(initialPage);
    updateActiveLink(initialPage);
});
