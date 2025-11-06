document.addEventListener('DOMContentLoaded', () => {
    const addUserBtn = document.getElementById('add-user-btn');
    const addUserModal = document.getElementById('add-user-modal');
    const cancelAddBtn = document.getElementById('cancel-add-btn');
    const addUserForm = document.getElementById('add-user-form');

    const editUserModal = document.getElementById('edit-user-modal');
    const cancelEditBtn = document.getElementById('cancel-edit-btn');
    const editUserForm = document.getElementById('edit-user-form');

    const deleteUserModal = document.getElementById('delete-user-modal');
    const cancelDeleteBtn = document.getElementById('cancel-delete-btn');
    const deleteUserForm = document.getElementById('delete-user-form');

    const usersTbody = document.getElementById('users-tbody');

    const API_URL = '../../backend/api/users/get_all.php';
    const CREATE_URL = '../../backend/api/users/create.php';
    const UPDATE_URL = '../../backend/api/users/update.php';
    const DELETE_URL = '../../backend/api/users/delete.php';

    // Function to open modal
    const openModal = (modal) => {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    };

    // Function to close modal
    const closeModal = (modal) => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };

    // Event listeners for modals
    addUserBtn.addEventListener('click', () => openModal(addUserModal));
    cancelAddBtn.addEventListener('click', () => closeModal(addUserModal));
    cancelEditBtn.addEventListener('click', () => closeModal(editUserModal));
    cancelDeleteBtn.addEventListener('click', () => closeModal(deleteUserModal));

    // Fetch and display users
    const fetchUsers = async () => {
        try {
            const response = await fetch(API_URL);
            const { data } = await response.json();
            
            usersTbody.innerHTML = '';
            data.filter(user => user.role === 'customer').forEach(user => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td class="p-3 border-b">${user.name}</td>
                    <td class="p-3 border-b">${user.email}</td>
                    <td class="p-3 border-b">${user.phone || 'N/A'}</td>
                    <td class="p-3 border-b">${new Date(user.created_at).toLocaleDateString()}</td>
                    <td class="p-3 border-b">
                        <button class="edit-btn text-blue-500 hover:text-blue-700" data-id="${user.user_id}"><i class="fas fa-edit"></i></button>
                        <button class="delete-btn text-red-500 hover:text-red-700 ml-2" data-id="${user.user_id}"><i class="fas fa-trash"></i></button>
                    </td>
                `;
                usersTbody.appendChild(tr);
            });
        } catch (error) {
            console.error('Error fetching users:', error);
        }
    };

    // Add new user
    addUserForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(addUserForm);
        formData.append('role', 'customer');

        try {
            const response = await fetch(CREATE_URL, {
                method: 'POST',
                body: JSON.stringify(Object.fromEntries(formData))
            });
            const result = await response.json();
            if (result.status === 'success') {
                closeModal(addUserModal);
                fetchUsers();
                addUserForm.reset();
            } else {
                console.error('Error adding user:', result.message);
            }
        } catch (error) {
            console.error('Error adding user:', error);
        }
    });

    // Edit user
    usersTbody.addEventListener('click', async (e) => {
        if (e.target.closest('.edit-btn')) {
            const userId = e.target.closest('.edit-btn').dataset.id;
            try {
                const response = await fetch(`${API_URL}?user_id=${userId}`);
                const { data } = await response.json();
                
                document.getElementById('edit-user-id').value = data.user_id;
                document.getElementById('edit-name').value = data.name;
                document.getElementById('edit-email').value = data.email;
                document.getElementById('edit-phone').value = data.phone;
                openModal(editUserModal);
            } catch (error) {
                console.error('Error fetching user data:', error);
            }
        }
    });

    editUserForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(editUserForm);
        const data = Object.fromEntries(formData.entries());

        try {
            const response = await fetch(UPDATE_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            const result = await response.json();

            if (result.status === 'success') {
                closeModal(editUserModal);
                fetchUsers();
            } else {
                console.error('Update failed:', result.message);
            }
        } catch (error) {
            console.error('Error updating user:', error);
        }
    });

    // Delete user
    usersTbody.addEventListener('click', (e) => {
        if (e.target.closest('.delete-btn')) {
            const userId = e.target.closest('.delete-btn').dataset.id;
            document.getElementById('delete-user-id').value = userId;
            openModal(deleteUserModal);
        }
    });

    deleteUserForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const userId = document.getElementById('delete-user-id').value;

        try {
            const response = await fetch(DELETE_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ user_id: userId })
            });
            const result = await response.json();

            if (result.status === 'success') {
                closeModal(deleteUserModal);
                fetchUsers();
            } else {
                console.error('Deletion failed:', result.message);
            }
        } catch (error) {
            console.error('Error deleting user:', error);
        }
    });

    fetchUsers();
});
