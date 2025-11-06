document.addEventListener('DOMContentLoaded', () => {
    const profileForm = document.getElementById('profile-form');
    const usernameInput = document.getElementById('username');
    const emailInput = document.getElementById('email');
    const currentPasswordInput = document.getElementById('current-password');
    const newPasswordInput = document.getElementById('new-password');
    const confirmPasswordInput = document.getElementById('confirm-password');

    const GET_URL = '../../backend/api/users/get.php';
    const UPDATE_URL = '../../backend/api/users/update.php';

    const fetchProfile = async () => {
        try {
            const response = await fetch(GET_URL);
            const { data } = await response.json();
            if (data) {
                usernameInput.value = data.username;
                emailInput.value = data.email;
            }
        } catch (error) {
            console.error('Error fetching profile:', error);
        }
    };

    profileForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const email = emailInput.value;
        const current_password = currentPasswordInput.value;
        const new_password = newPasswordInput.value;
        const confirm_password = confirmPasswordInput.value;

        let payload = { email };

        if (new_password) {
            if (new_password !== confirm_password) {
                alert('New passwords do not match.');
                return;
            }
            payload.current_password = current_password;
            payload.new_password = new_password;
            payload.confirm_password = confirm_password;
        }

        try {
            const response = await fetch(UPDATE_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const result = await response.json();

            if (result.status === 'success') {
                alert('Profile updated successfully!');
                // Clear password fields
                currentPasswordInput.value = '';
                newPasswordInput.value = '';
                confirmPasswordInput.value = '';
            } else {
                alert(`Update failed: ${result.message}`);
            }
        } catch (error) {
            console.error('Error updating profile:', error);
            alert('An error occurred while updating the profile.');
        }
    });

    fetchProfile();
});
