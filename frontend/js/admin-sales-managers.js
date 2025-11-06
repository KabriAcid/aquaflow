document.addEventListener('DOMContentLoaded', () => {
    const addManagerBtn = document.getElementById('add-manager-btn');
    const managerModal = document.getElementById('manager-modal');
    const cancelBtn = document.getElementById('cancel-btn');
    const managerForm = document.getElementById('manager-form');
    const modalTitle = document.getElementById('modal-title');
    const saveBtn = document.getElementById('save-btn');

    const deleteManagerModal = document.getElementById('delete-manager-modal');
    const cancelDeleteBtn = document.getElementById('cancel-delete-btn');
    const deleteManagerForm = document.getElementById('delete-manager-form');

    const managersTbody = document.getElementById('managers-tbody');

    const API_URL = '../../backend/api/users/get_all.php';
    const CREATE_URL = '../../backend/api/users/create.php';
    const UPDATE_URL = '../../backend/api/users/update.php';
    const DELETE_URL = '../../backend/api/users/delete.php';

    const openModal = (modal) => {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };

    const closeModal = (modal) => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };

    const fetchManagers = async () => {
        try {
            const response = await fetch(API_URL, { credentials: 'same-origin' });
            const { data } = await response.json();

            managersTbody.innerHTML = '';
            data.filter(user => user.role === 'sales_manager').forEach(manager => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="p-3 border-b">${manager.name}</td>
                    <td class="p-3 border-b">${manager.email}</td>
                    <td class="p-3 border-b">${manager.phone || 'N/A'}</td>
                    <td class="p-3 border-b">${new Date(manager.created_at).toLocaleDateString()}</td>
                    <td class="p-3 border-b">
                        <button class="edit-btn text-blue-500 hover:text-blue-700" data-id="${manager.user_id}"><i class="fas fa-edit"></i></button>
                        <button class="delete-btn text-red-500 hover:text-red-700 ml-2" data-id="${manager.user_id}"><i class="fas fa-trash"></i></button>
                    </td>
                `;
                managersTbody.appendChild(tr);
            });
        } catch (error) {
            console.error('Error fetching managers:', error);
        }
    };

    const prepareAddForm = () => {
        managerForm.reset();
        document.getElementById('user_id').value = '';
        modalTitle.textContent = 'Add New Manager';
        saveBtn.textContent = 'Save Manager';
        document.getElementById('password').required = true;
        openModal(managerModal);
    };

    const prepareEditForm = async (userId) => {
        try {
            const response = await fetch(`${API_URL}?user_id=${userId}`, { credentials: 'same-origin' });
            const { data } = await response.json();

            managerForm.reset();
            document.getElementById('user_id').value = data.user_id;
            document.getElementById('name').value = data.name;
            document.getElementById('email').value = data.email;
            document.getElementById('phone').value = data.phone;
            document.getElementById('password').required = false;

            modalTitle.textContent = 'Edit Manager';
            saveBtn.textContent = 'Update Manager';
            openModal(managerModal);
        } catch (error) {
            console.error('Error fetching manager data:', error);
        }
    };

    addManagerBtn.addEventListener('click', prepareAddForm);
    cancelBtn.addEventListener('click', () => closeModal(managerModal));

    managerForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(managerForm);
        const data = Object.fromEntries(formData.entries());
        const userId = data.user_id;

        if (data.password !== data.confirm_password) {
            alert('Passwords do not match.');
            return;
        }

        const url = userId ? UPDATE_URL : CREATE_URL;
        if (!userId) {
            data.role = 'sales_manager';
        }

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'same-origin',
                body: JSON.stringify(data)
            });
            const result = await response.json();

            if (result.status === 'success') {
                closeModal(managerModal);
                fetchManagers();
            } else {
                console.error('Operation failed:', result.message);
            }
        } catch (error) {
            console.error('Error saving manager:', error);
        }
    });

    managersTbody.addEventListener('click', (e) => {
        const editBtn = e.target.closest('.edit-btn');
        const deleteBtn = e.target.closest('.delete-btn');
        if (editBtn) {
            prepareEditForm(editBtn.dataset.id);
        } else if (deleteBtn) {
            document.getElementById('delete-user-id').value = deleteBtn.dataset.id;
            openModal(deleteManagerModal);
        }
    });

    cancelDeleteBtn.addEventListener('click', () => closeModal(deleteManagerModal));

    deleteManagerForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const userId = document.getElementById('delete-user-id').value;

        try {
            const response = await fetch(DELETE_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'same-origin',
                body: JSON.stringify({ user_id: userId })
            });
            const result = await response.json();

            if (result.status === 'success') {
                closeModal(deleteManagerModal);
                fetchManagers();
            } else {
                console.error('Deletion failed:', result.message);
            }
        } catch (error) {
            console.error('Error deleting manager:', error);
        }
    });

    fetchManagers();
});
