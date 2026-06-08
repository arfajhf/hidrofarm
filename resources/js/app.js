const authTokenKey = 'smartfarm_api_token';
const authExpiresKey = 'smartfarm_api_token_expires_at';
const oneTapTokenKey = 'smartfarm_one_tap_token';
const oneTapExpiresKey = 'smartfarm_one_tap_expires_at';
const oneTapUserKey = 'smartfarm_one_tap_user';

const form = document.querySelector('#auth-form');
const messageBox = document.querySelector('#auth-message');
const submitButton = document.querySelector('#submit-button');
const oneTapCard = document.querySelector('#one-tap-card');
const loginCard = document.querySelector('#login-card');
const oneTapButton = document.querySelector('#one-tap-button');
const oneTapAvatar = document.querySelector('#one-tap-avatar');
const oneTapName = document.querySelector('#one-tap-name');
const switchAccountButton = document.querySelector('#switch-account-button');
const togglePasswordButton = document.querySelector('#toggle-password');
const passwordInput = document.querySelector('#password');
const dashboardLogoutButton = document.querySelector('#dashboard-logout-button');
const dashboardAvatar = document.querySelector('#dashboard-avatar');
const dashboardName = document.querySelector('#dashboard-name');
const dashboardPhone = document.querySelector('#dashboard-phone');
const dashboardMenuButton = document.querySelector('#dashboard-menu-button');
const dashboardDropdown = document.querySelector('#dashboard-dropdown');
const dashboardMenuName = document.querySelector('#dashboard-menu-name');
const dashboardDropdownName = document.querySelector('#dashboard-dropdown-name');

// --- TAMBAHAN UNTUK HALAMAN PROFILE ---
const profilePageAvatar = document.querySelector('#profile-page-avatar');
const profilePageName = document.querySelector('#profile-page-name');
const profilePageEmail = document.querySelector('#profile-page-email');
const profilePageFullName = document.querySelector('#profile-page-fullname');
const profilePagePhone = document.querySelector('#profile-page-phone');
const profilePageOneTapStatus = document.querySelector('#profile-page-onetap-status');
// --------------------------------------

function isFuture(value) {
    return value && new Date(value).getTime() > Date.now();
}

function clearAuthToken() {
    localStorage.removeItem(authTokenKey);
    localStorage.removeItem(authExpiresKey);
}

function clearOneTapHistory() {
    localStorage.removeItem(oneTapTokenKey);
    localStorage.removeItem(oneTapExpiresKey);
    localStorage.removeItem(oneTapUserKey);
}

function saveAuth(payload) {
    localStorage.setItem(authTokenKey, payload.token);
    localStorage.setItem(authExpiresKey, payload.token_expires_at);
    localStorage.setItem(oneTapTokenKey, payload.one_tap_token);
    localStorage.setItem(oneTapExpiresKey, payload.one_tap_expires_at);
    localStorage.setItem(oneTapUserKey, JSON.stringify(payload.user));
}

function scheduleAutoLogout() {
    const expiresAt = localStorage.getItem(authExpiresKey);

    if (!expiresAt) {
        return;
    }

    const delay = new Date(expiresAt).getTime() - Date.now();

    if (delay <= 0) {
        logout(true);
        return;
    }

    window.setTimeout(() => logout(true), Math.min(delay, 2147483647));
}

function getOneTapUser() {
    try {
        return JSON.parse(localStorage.getItem(oneTapUserKey));
    } catch {
        return null;
    }
}

function showMessage(text, type = 'success') {
    if (!messageBox) {
        return;
    }

    messageBox.textContent = text;
    messageBox.className = `hydro-message is-${type}`;
}

function hideMessage() {
    if (!messageBox) {
        return;
    }

    messageBox.textContent = '';
    messageBox.className = 'hydro-message hidden';
}

function renderAvatar(target, user) {
    if (!target || !user) {
        return;
    }

    target.replaceChildren();

    if (user.profile_photo_url) {
        const image = document.createElement('img');
        image.src = user.profile_photo_url;
        image.alt = `Foto profil ${user.name}`;
        target.append(image);
        return;
    }

    target.textContent = user.name?.slice(0, 1).toUpperCase() || 'P';
}

function showOneTap() {
    const user = getOneTapUser();
    const canOneTap = user && isFuture(localStorage.getItem(oneTapExpiresKey));

    if (!oneTapCard || !loginCard || !switchAccountButton || !canOneTap) {
        oneTapCard?.classList.add('hidden');
        switchAccountButton?.classList.add('hidden');
        loginCard?.classList.remove('hidden');
        return;
    }

    oneTapName.textContent = user.name || 'Nama Pengguna';
    renderAvatar(oneTapAvatar, user);
    oneTapCard.classList.remove('hidden');
    switchAccountButton.classList.remove('hidden');
    loginCard.classList.add('hidden');
}

async function requestJson(url, options = {}) {
    const { headers = {}, ...requestOptions } = options;
    const response = await fetch(url, {
        ...requestOptions,
        headers: {
            Accept: 'application/json',
            ...headers,
        },
    });
    const payload = await response.json();

    if (!response.ok) {
        const errors = payload.errors ? Object.values(payload.errors).flat().join(' ') : payload.message;
        throw new Error(errors || 'Permintaan gagal diproses.');
    }

    return payload;
}

async function logout(redirect = true) {
    const token = localStorage.getItem(authTokenKey);

    if (token && isFuture(localStorage.getItem(authExpiresKey))) {
        try {
            await requestJson('/auth/logout', {
                method: 'POST',
                headers: {
                    Authorization: `Bearer ${token}`,
                },
            });
        } catch {
            // Token may already be expired server-side.
        }
    }

    clearAuthToken();

    if (redirect) {
        window.location.href = '/login';
    }
}

async function requireDashboardAuth() {
    if (!dashboardMenuButton) {
        return;
    }

    const token = localStorage.getItem(authTokenKey);

    if (!token || !isFuture(localStorage.getItem(authExpiresKey))) {
        await logout(true);
        return;
    }

    try {
        const payload = await requestJson('/auth/me', {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });

        if (dashboardName) dashboardName.textContent = payload.user.name;
        if (dashboardPhone) dashboardPhone.textContent = payload.user.phone_number || 'No handphone belum diisi';
        if (dashboardMenuName) dashboardMenuName.textContent = payload.user.name || 'Admin';
        if (dashboardDropdownName) dashboardDropdownName.textContent = payload.user.name || 'Admin';
        renderAvatar(dashboardAvatar, payload.user);

        // --- TAMBAHAN UNTUK HALAMAN PROFILE ---
        if (profilePageName) profilePageName.textContent = payload.user.name || 'Pengguna';
        if (profilePageEmail) profilePageEmail.textContent = payload.user.email || 'email@belumdiset.com';
        if (profilePageFullName) profilePageFullName.textContent = payload.user.name || '-';
        if (profilePagePhone) profilePagePhone.textContent = payload.user.phone_number || 'Belum ditambahkan';
        if (profilePageOneTapStatus) {
            profilePageOneTapStatus.textContent = payload.user.one_tap_token_hash ? 'Aktif (Sesi Persisten)' : 'Belum Aktif';
        }
        if (profilePageAvatar) {
            // Karena fungsi renderAvatar lo udah bagus banget nanganin inisial dan foto, kita pake lagi aja
            renderAvatar(profilePageAvatar, payload.user);
        }
        // --------------------------------------

        scheduleAutoLogout();
    } catch {
        await logout(true);
    }
}

async function redirectIfAlreadyLoggedIn() {
    if (!form || !isFuture(localStorage.getItem(authExpiresKey))) {
        return;
    }

    const token = localStorage.getItem(authTokenKey);

    if (!token) {
        return;
    }

    try {
        await requestJson('/auth/me', {
            headers: {
                Authorization: `Bearer ${token}`,
            },
        });
        window.location.href = '/dashboard';
    } catch {
        clearAuthToken();
    }
}

form?.addEventListener('submit', async (event) => {
    event.preventDefault();
    hideMessage();
    submitButton.disabled = true;
    submitButton.textContent = 'Memproses...';

    try {
        const payload = await requestJson('/auth/login', {
            method: 'POST',
            body: new FormData(form),
        });

        saveAuth(payload);
        window.location.href = '/dashboard';
    } catch (error) {
        showMessage(error.message, 'error');
    } finally {
        submitButton.disabled = false;
        submitButton.textContent = 'Masuk';
    }
});

oneTapButton?.addEventListener('click', async () => {
    hideMessage();

    try {
        const body = new FormData();
        body.append('one_tap_token', localStorage.getItem(oneTapTokenKey));

        const payload = await requestJson('/auth/one-tap', {
            method: 'POST',
            body,
        });

        saveAuth(payload);
        window.location.href = '/dashboard';
    } catch (error) {
        clearOneTapHistory();
        showOneTap();
        showMessage(error.message, 'error');
    }
});

switchAccountButton?.addEventListener('click', () => {
    loginCard.classList.remove('hidden');
    oneTapCard.classList.add('hidden');
    switchAccountButton.classList.add('hidden');
    document.querySelector('#name')?.focus();
});

togglePasswordButton?.addEventListener('click', () => {
    const isPassword = passwordInput.type === 'password';
    passwordInput.type = isPassword ? 'text' : 'password';
    togglePasswordButton.setAttribute('aria-label', isPassword ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
});

dashboardLogoutButton?.addEventListener('click', () => logout(true));

dashboardMenuButton?.addEventListener('click', () => {
    const isOpen = !dashboardDropdown.classList.contains('hidden');

    dashboardDropdown.classList.toggle('hidden', isOpen);
    dashboardMenuButton.setAttribute('aria-expanded', String(!isOpen));
});

document.addEventListener('click', (event) => {
    if (!dashboardDropdown || !dashboardMenuButton) {
        return;
    }

    if (dashboardDropdown.classList.contains('hidden')) {
        return;
    }

    if (dashboardDropdown.contains(event.target) || dashboardMenuButton.contains(event.target)) {
        return;
    }

    dashboardDropdown.classList.add('hidden');
    dashboardMenuButton.setAttribute('aria-expanded', 'false');
});

if (form) {
    if (!isFuture(localStorage.getItem(authExpiresKey))) {
        clearAuthToken();
    }

    showOneTap();
    redirectIfAlreadyLoggedIn();
}

requireDashboardAuth();