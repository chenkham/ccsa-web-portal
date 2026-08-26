<?php
declare(strict_types=1);

// Ensure /admin has a trailing slash so relative paths and form actions resolve properly
$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$urlPath = parse_url($requestUri, PHP_URL_PATH) ?? '';
if (rtrim($urlPath, '/') === '/admin' && !str_ends_with($urlPath, '/')) {
    $queryString = parse_url($requestUri, PHP_URL_QUERY);
    $targetUrl = '/admin/' . ($queryString ? '?' . $queryString : '');
    header('Location: ' . $targetUrl, true, 301);
    exit;
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/security-validator.php';
require_once __DIR__ . '/check_session.php';

$error = $_GET['error'] ?? null;
$success = $_GET['success'] ?? null;
$expired = isset($_GET['expired']);

// Authentication check: show login form if not logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('X-Robots-Tag: noindex, nofollow, noarchive, nosnippet');
    $csrf_token = SecurityValidator::generateCSRFToken();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="robots" content="noindex, nofollow, noarchive, nosnippet">
        <meta name="googlebot" content="noindex, nofollow, noarchive, nosnippet">
        <title>Admin Login - CCSA Dibrugarh University</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <script src="https://cdn.tailwindcss.com"></script>
        <style>
            body { font-family: 'Plus Jakarta Sans', sans-serif; }
            .login-card { animation: fadeIn 0.4s ease-out; }
            @keyframes fadeIn { from { opacity: 0; transform: translateY(15px); } to { opacity: 1; transform: translateY(0); } }
            .otp-box-input { width: 44px; height: 50px; text-align: center; font-size: 20px; font-weight: 800; font-family: monospace; }
            .login-tab-btn.active { background-color: #1a365d; color: #ffffff; font-weight: 700; shadow: 0 2px 4px rgba(0,0,0,0.1); }
            .login-tab-btn { color: #64748b; font-weight: 600; }
        </style>
    </head>
    <body class="bg-gradient-to-br from-[#1a365d] via-[#1e293b] to-[#0f172a] min-h-screen flex items-center justify-center p-4">
        <div class="login-card max-w-md w-full bg-white/95 backdrop-blur-md rounded-3xl p-7 sm:p-8 shadow-2xl border border-white/20">
            <div class="text-center mb-6">
                <div class="inline-flex p-3 rounded-2xl bg-indigo-50 border border-indigo-100 mb-3">
                    <img src="/faculty/du.png" alt="Dibrugarh University" class="w-16 h-16 object-contain" onerror="this.src='../faculty/du.png'">
                </div>
                <h1 class="text-2xl font-extrabold text-[#1a365d]">CCSA Admin Portal</h1>
                <p class="text-xs text-slate-500 mt-1">Centre for Computer Science and Applications</p>
            </div>

            <!-- Global Feedback Alert -->
            <div id="loginAlertBox" class="<?php echo ($error || $expired || $success) ? '' : 'hidden'; ?> mb-5 p-3.5 rounded-xl text-xs font-semibold flex items-center gap-2 <?php echo $error ? 'bg-red-50 border border-red-200 text-red-700' : ($expired ? 'bg-amber-50 border border-amber-200 text-amber-800' : 'bg-emerald-50 border border-emerald-200 text-emerald-800'); ?>">
                <i class="fas <?php echo $error ? 'fa-exclamation-circle text-red-500' : ($expired ? 'fa-clock text-amber-500' : 'fa-check-circle text-emerald-500'); ?> text-sm flex-shrink-0"></i>
                <span id="loginAlertMsg"><?php echo htmlspecialchars($error ?? ($expired ? 'Your session has expired. Please log in again.' : ($success ?? ''))); ?></span>
            </div>

            <!-- Tab Switcher: Password vs OTP -->
            <div class="grid grid-cols-2 p-1 bg-slate-100/90 rounded-2xl mb-6 border border-slate-200/80">
                <button type="button" onclick="switchLoginTab('password')" id="tabBtnPassword" class="login-tab-btn active py-2.5 text-xs rounded-xl flex items-center justify-center gap-1.5 transition-all">
                    <i class="fas fa-key text-xs"></i>
                    <span>Password</span>
                </button>
                <button type="button" onclick="switchLoginTab('otp')" id="tabBtnOtp" class="login-tab-btn py-2.5 text-xs rounded-xl flex items-center justify-center gap-1.5 transition-all">
                    <i class="fas fa-envelope-open-text text-xs"></i>
                    <span>Email OTP</span>
                </button>
            </div>

            <!-- 1. Password Login View -->
            <form id="passwordLoginForm" method="POST" action="/admin/login.php" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Admin Email</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-3.5 top-3.5 text-slate-400 text-sm"></i>
                        <input type="email" id="email" name="email" required placeholder="admin@ccsdu.in" 
                            class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition-all">
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1.5">
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Password</label>
                        <button type="button" onclick="openForgotPasswordModal()" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold">Forgot?</button>
                    </div>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-3.5 top-3.5 text-slate-400 text-sm"></i>
                        <input type="password" id="password" name="password" required placeholder="••••••••" 
                            class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition-all">
                    </div>
                </div>

                <button type="submit" 
                    class="w-full bg-[#1a365d] hover:bg-indigo-900 text-white font-bold py-3.5 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center gap-2 text-sm mt-2">
                    <span>Sign In with Password</span>
                    <i class="fas fa-arrow-right text-xs"></i>
                </button>
            </form>

            <!-- 2. OTP Login View -->
            <div id="otpLoginForm" class="hidden space-y-4">
                <!-- Step 1: Request OTP -->
                <div id="otpStep1" class="space-y-4">
                    <div>
                        <label for="otpEmail" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Admin Email</label>
                        <div class="relative">
                            <i class="fas fa-envelope absolute left-3.5 top-3.5 text-slate-400 text-sm"></i>
                            <input type="email" id="otpEmail" placeholder="admin@ccsdu.in" 
                                class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition-all">
                        </div>
                        <p class="text-[11px] text-slate-500 mt-1.5">We will send a 6-digit verification code to your registered admin email.</p>
                    </div>

                    <button type="button" onclick="sendLoginOtp()" id="btnSendOtp"
                        class="w-full bg-[#1a365d] hover:bg-indigo-900 text-white font-bold py-3.5 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center gap-2 text-sm">
                        <span>Send Login OTP</span>
                        <i class="fas fa-paper-plane text-xs"></i>
                    </button>
                </div>

                <!-- Step 2: Enter 6-digit OTP -->
                <div id="otpStep2" class="hidden space-y-4">
                    <div class="p-3 bg-indigo-50/80 border border-indigo-100 rounded-xl text-center">
                        <p class="text-xs text-slate-600">Verification code sent to:</p>
                        <p id="otpTargetEmailDisplay" class="text-xs font-bold text-[#1a365d] break-all"></p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider text-center mb-2">Enter 6-Digit OTP</label>
                        <div class="flex justify-between gap-1.5 sm:gap-2 max-w-xs mx-auto" id="otpInputsContainer">
                            <input type="text" maxlength="1" inputmode="numeric" class="otp-box-input bg-slate-50 border border-slate-300 rounded-xl focus:border-indigo-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500" data-idx="0">
                            <input type="text" maxlength="1" inputmode="numeric" class="otp-box-input bg-slate-50 border border-slate-300 rounded-xl focus:border-indigo-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500" data-idx="1">
                            <input type="text" maxlength="1" inputmode="numeric" class="otp-box-input bg-slate-50 border border-slate-300 rounded-xl focus:border-indigo-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500" data-idx="2">
                            <input type="text" maxlength="1" inputmode="numeric" class="otp-box-input bg-slate-50 border border-slate-300 rounded-xl focus:border-indigo-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500" data-idx="3">
                            <input type="text" maxlength="1" inputmode="numeric" class="otp-box-input bg-slate-50 border border-slate-300 rounded-xl focus:border-indigo-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500" data-idx="4">
                            <input type="text" maxlength="1" inputmode="numeric" class="otp-box-input bg-slate-50 border border-slate-300 rounded-xl focus:border-indigo-600 focus:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500" data-idx="5">
                        </div>
                    </div>

                    <button type="button" onclick="verifyLoginOtp()" id="btnVerifyOtp"
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center gap-2 text-sm">
                        <span>Verify &amp; Sign In</span>
                        <i class="fas fa-shield-alt text-xs"></i>
                    </button>

                    <div class="flex items-center justify-between text-xs pt-1">
                        <button type="button" onclick="resendLoginOtp()" id="btnResendOtp" class="text-indigo-600 hover:text-indigo-800 font-semibold disabled:text-slate-400 disabled:pointer-events-none">Resend Code</button>
                        <button type="button" onclick="resetOtpView()" class="text-slate-500 hover:text-slate-700 font-medium">Change Email</button>
                    </div>
                </div>
            </div>

            <!-- Forgot Password Modal -->
            <div id="forgotPasswordModal" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
                <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-200 relative">
                    <div class="flex justify-between items-center pb-3 border-b border-slate-100 mb-4">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm font-black">
                                <i class="fas fa-unlock-alt"></i>
                            </div>
                            <h3 class="text-sm sm:text-base font-bold text-slate-800">Reset Account Password</h3>
                        </div>
                        <button type="button" onclick="closeForgotPasswordModal()" class="text-slate-400 hover:text-slate-700 text-2xl font-light">&times;</button>
                    </div>

                    <div id="forgotModalAlert" class="hidden mb-4 p-3 rounded-xl text-xs font-semibold"></div>

                    <!-- Step 1: Send Reset OTP -->
                    <div id="forgotStep1" class="space-y-4">
                        <p class="text-xs text-slate-600 leading-relaxed">Enter your registered administrator email to receive a password reset verification code.</p>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Admin Email</label>
                            <input type="email" id="forgotEmail" placeholder="admin@ccsdu.in" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600">
                        </div>
                        <button type="button" onclick="sendResetOtp()" id="btnSendResetOtp" class="w-full bg-[#1a365d] hover:bg-indigo-900 text-white font-bold py-3 rounded-xl text-xs shadow transition-all flex items-center justify-center gap-2">
                            <span>Send Verification Code</span>
                            <i class="fas fa-paper-plane text-xs"></i>
                        </button>
                    </div>

                    <!-- Step 2: Enter OTP + New Password -->
                    <div id="forgotStep2" class="hidden space-y-3.5">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">6-Digit OTP Code</label>
                            <input type="text" id="forgotOtp" maxlength="6" placeholder="123456" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono tracking-widest text-center focus:outline-none focus:ring-2 focus:ring-indigo-600">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">New Password (Min 8 chars)</label>
                            <input type="password" id="forgotNewPass" placeholder="••••••••" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase mb-1">Confirm New Password</label>
                            <input type="password" id="forgotConfirmPass" placeholder="••••••••" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600">
                        </div>
                        <button type="button" onclick="verifyAndResetPassword()" id="btnVerifyResetPass" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-xl text-xs shadow transition-all flex items-center justify-center gap-2">
                            <span>Verify &amp; Update Password</span>
                            <i class="fas fa-check text-xs"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-6 border-t border-slate-100 text-center">
                <a href="../index.php" class="text-xs text-indigo-600 hover:text-indigo-800 font-semibold inline-flex items-center gap-1">
                    <i class="fas fa-chevron-left text-[10px]"></i>
                    <span>Back to Portal Homepage</span>
                </a>
            </div>
        </div>

        <script>
        const CSRF_TOKEN = "<?php echo $csrf_token; ?>";
        let resendTimerInterval = null;

        function showLoginAlert(msg, type = 'error') {
            const box = document.getElementById('loginAlertBox');
            const text = document.getElementById('loginAlertMsg');
            if (!box || !text) return;
            box.className = `mb-5 p-3.5 rounded-xl text-xs font-semibold flex items-center gap-2 ${
                type === 'error' ? 'bg-red-50 border border-red-200 text-red-700' :
                type === 'success' ? 'bg-emerald-50 border border-emerald-200 text-emerald-800' :
                'bg-amber-50 border border-amber-200 text-amber-800'
            }`;
            text.textContent = msg;
            box.classList.remove('hidden');
        }

        function switchLoginTab(tab) {
            const passForm = document.getElementById('passwordLoginForm');
            const otpForm = document.getElementById('otpLoginForm');
            const tabPass = document.getElementById('tabBtnPassword');
            const tabOtp = document.getElementById('tabBtnOtp');

            if (tab === 'otp') {
                passForm.classList.add('hidden');
                otpForm.classList.remove('hidden');
                tabPass.classList.remove('active');
                tabOtp.classList.add('active');
            } else {
                otpForm.classList.add('hidden');
                passForm.classList.remove('hidden');
                tabOtp.classList.remove('active');
                tabPass.classList.add('active');
            }
        }

        async function sendLoginOtp() {
            const emailInput = document.getElementById('otpEmail');
            const email = (emailInput?.value || '').trim();
            const btn = document.getElementById('btnSendOtp');

            if (!email || !email.includes('@')) {
                showLoginAlert('Please enter a valid administrator email address.', 'error');
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Sending OTP...</span>';

            try {
                const res = await fetch('/admin/otp_actions.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'send_login_otp', email, csrf_token: CSRF_TOKEN })
                });
                const data = await res.json();

                if (data.success) {
                    showLoginAlert(data.message, 'success');
                    document.getElementById('otpStep1').classList.add('hidden');
                    document.getElementById('otpStep2').classList.remove('hidden');
                    document.getElementById('otpTargetEmailDisplay').textContent = email;
                    startResendCountdown(60);
                    setupOtpInputs();
                } else {
                    showLoginAlert(data.error || 'Failed to send verification code.', 'error');
                }
            } catch (err) {
                showLoginAlert('Network error. Please check your connection.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<span>Send Login OTP</span> <i class="fas fa-paper-plane text-xs"></i>';
            }
        }

        function setupOtpInputs() {
            const inputs = document.querySelectorAll('.otp-box-input');
            inputs.forEach((input, idx) => {
                input.value = '';
                input.oninput = (e) => {
                    const val = e.target.value;
                    if (val.length === 1 && idx < inputs.length - 1) {
                        inputs[idx + 1].focus();
                    }
                    if (getEnteredOtp().length === 6) {
                        verifyLoginOtp();
                    }
                };
                input.onkeydown = (e) => {
                    if (e.key === 'Backspace' && !input.value && idx > 0) {
                        inputs[idx - 1].focus();
                    }
                };
                input.onpaste = (e) => {
                    e.preventDefault();
                    const pasted = (e.clipboardData || window.clipboardData).getData('text').trim();
                    if (/^\d{6}$/.test(pasted)) {
                        pasted.split('').forEach((char, i) => {
                            if (inputs[i]) inputs[i].value = char;
                        });
                        verifyLoginOtp();
                    }
                };
            });
            inputs[0]?.focus();
        }

        function getEnteredOtp() {
            const inputs = document.querySelectorAll('.otp-box-input');
            let otp = '';
            inputs.forEach(i => otp += i.value.trim());
            return otp;
        }

        async function verifyLoginOtp() {
            const email = (document.getElementById('otpEmail')?.value || '').trim();
            const otp = getEnteredOtp();
            const btn = document.getElementById('btnVerifyOtp');

            if (otp.length !== 6) {
                showLoginAlert('Please enter the complete 6-digit code.', 'error');
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Verifying...</span>';

            try {
                const res = await fetch('/admin/otp_actions.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'verify_login_otp', email, otp, csrf_token: CSRF_TOKEN })
                });
                const data = await res.json();

                if (data.success) {
                    showLoginAlert('Authentication successful! Loading dashboard...', 'success');
                    window.location.href = data.redirect || '/admin/index.php';
                } else {
                    showLoginAlert(data.error || 'Verification failed.', 'error');
                    btn.disabled = false;
                    btn.innerHTML = '<span>Verify &amp; Sign In</span> <i class="fas fa-shield-alt text-xs"></i>';
                }
            } catch (err) {
                showLoginAlert('Network error during verification.', 'error');
                btn.disabled = false;
                btn.innerHTML = '<span>Verify &amp; Sign In</span> <i class="fas fa-shield-alt text-xs"></i>';
            }
        }

        function startResendCountdown(seconds) {
            const btn = document.getElementById('btnResendOtp');
            if (!btn) return;
            clearInterval(resendTimerInterval);
            let remaining = seconds;
            btn.disabled = true;

            resendTimerInterval = setInterval(() => {
                remaining--;
                if (remaining <= 0) {
                    clearInterval(resendTimerInterval);
                    btn.disabled = false;
                    btn.textContent = 'Resend Code';
                } else {
                    btn.textContent = `Resend in ${remaining}s`;
                }
            }, 1000);
        }

        function resendLoginOtp() {
            sendLoginOtp();
        }

        function resetOtpView() {
            document.getElementById('otpStep2').classList.add('hidden');
            document.getElementById('otpStep1').classList.remove('hidden');
            clearInterval(resendTimerInterval);
        }

        function openForgotPasswordModal() {
            document.getElementById('forgotPasswordModal').classList.remove('hidden');
            document.getElementById('forgotStep1').classList.remove('hidden');
            document.getElementById('forgotStep2').classList.add('hidden');
            document.getElementById('forgotModalAlert').classList.add('hidden');
        }

        function closeForgotPasswordModal() {
            document.getElementById('forgotPasswordModal').classList.add('hidden');
        }

        async function sendResetOtp() {
            const email = (document.getElementById('forgotEmail')?.value || '').trim();
            const btn = document.getElementById('btnSendResetOtp');
            const alertBox = document.getElementById('forgotModalAlert');

            if (!email || !email.includes('@')) {
                alertBox.className = 'mb-4 p-3 rounded-xl text-xs font-semibold bg-red-50 border border-red-200 text-red-700';
                alertBox.textContent = 'Please enter a valid email address.';
                alertBox.classList.remove('hidden');
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Sending code...</span>';

            try {
                const res = await fetch('/admin/otp_actions.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'send_reset_otp', email, csrf_token: CSRF_TOKEN })
                });
                const data = await res.json();

                if (data.success) {
                    alertBox.className = 'mb-4 p-3 rounded-xl text-xs font-semibold bg-emerald-50 border border-emerald-200 text-emerald-800';
                    alertBox.textContent = data.message;
                    alertBox.classList.remove('hidden');
                    document.getElementById('forgotStep1').classList.add('hidden');
                    document.getElementById('forgotStep2').classList.remove('hidden');
                } else {
                    alertBox.className = 'mb-4 p-3 rounded-xl text-xs font-semibold bg-red-50 border border-red-200 text-red-700';
                    alertBox.textContent = data.error || 'Failed to send reset code.';
                    alertBox.classList.remove('hidden');
                }
            } catch (e) {
                alertBox.className = 'mb-4 p-3 rounded-xl text-xs font-semibold bg-red-50 border border-red-200 text-red-700';
                alertBox.textContent = 'Network error.';
                alertBox.classList.remove('hidden');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<span>Send Verification Code</span> <i class="fas fa-paper-plane text-xs"></i>';
            }
        }

        async function verifyAndResetPassword() {
            const email = (document.getElementById('forgotEmail')?.value || '').trim();
            const otp = (document.getElementById('forgotOtp')?.value || '').trim();
            const new_password = (document.getElementById('forgotNewPass')?.value || '').trim();
            const confirm_password = (document.getElementById('forgotConfirmPass')?.value || '').trim();
            const alertBox = document.getElementById('forgotModalAlert');
            const btn = document.getElementById('btnVerifyResetPass');

            if (otp.length !== 6) {
                alertBox.className = 'mb-4 p-3 rounded-xl text-xs font-semibold bg-red-50 border border-red-200 text-red-700';
                alertBox.textContent = 'Please enter the 6-digit verification code.';
                alertBox.classList.remove('hidden');
                return;
            }

            if (new_password.length < 8) {
                alertBox.className = 'mb-4 p-3 rounded-xl text-xs font-semibold bg-red-50 border border-red-200 text-red-700';
                alertBox.textContent = 'New password must be at least 8 characters.';
                alertBox.classList.remove('hidden');
                return;
            }

            if (new_password !== confirm_password) {
                alertBox.className = 'mb-4 p-3 rounded-xl text-xs font-semibold bg-red-50 border border-red-200 text-red-700';
                alertBox.textContent = 'Passwords do not match.';
                alertBox.classList.remove('hidden');
                return;
            }

            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <span>Updating password...</span>';

            try {
                const res = await fetch('/admin/otp_actions.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'verify_reset_password', email, otp, new_password, confirm_password, csrf_token: CSRF_TOKEN })
                });
                const data = await res.json();

                if (data.success) {
                    closeForgotPasswordModal();
                    showLoginAlert(data.message, 'success');
                    switchLoginTab('password');
                } else {
                    alertBox.className = 'mb-4 p-3 rounded-xl text-xs font-semibold bg-red-50 border border-red-200 text-red-700';
                    alertBox.textContent = data.error || 'Password reset failed.';
                    alertBox.classList.remove('hidden');
                }
            } catch (e) {
                alertBox.className = 'mb-4 p-3 rounded-xl text-xs font-semibold bg-red-50 border border-red-200 text-red-700';
                alertBox.textContent = 'Network error.';
                alertBox.classList.remove('hidden');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<span>Verify &amp; Update Password</span> <i class="fas fa-check text-xs"></i>';
            }
        }
        </script>
    </body>
    </html>
    <?php
    exit;
}

check_auth();

$username = htmlspecialchars($_SESSION['user_name'] ?? 'Administrator');
$userRole = (string)($_SESSION['user_role'] ?? 'super_admin');
$userrole = htmlspecialchars($userRole);
$useremail = htmlspecialchars($_SESSION['user_email'] ?? 'ccsduoffice@gmail.com');
$userEmail = (string)($_SESSION['user_email'] ?? 'ccsduoffice@gmail.com');
$isSuperAdmin = ($userRole === 'super_admin');
$csrf_token = SecurityValidator::generateCSRFToken();

// Role-based allowed tabs: Editor gets ONLY Notices and Inquiries
$allowedTabs = ['notifications', 'messages'];
if ($userRole === 'super_admin' || $userRole === 'admin') {
    $allowedTabs[] = 'logs';
}
if ($userRole === 'super_admin') {
    $allowedTabs[] = 'admin';
}

$currentTab = $_GET['tab'] ?? 'notifications';
if (!in_array($currentTab, $allowedTabs, true)) {
    $currentTab = 'notifications';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CCSA Management Dashboard - Dibrugarh University</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .tab-content { display: none; }
        .tab-content.active { display: block; animation: fadeIn 0.3s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col">

    <!-- Top Navigation Header -->
    <header class="bg-[#1a365d] text-white sticky top-0 z-40 shadow-md">
        <div class="container mx-auto px-4 sm:px-6 py-3.5 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="../index.php" title="Go to Website">
                    <img src="../faculty/du.png" alt="DU Logo" class="h-10 w-10 object-contain p-1 bg-white rounded-lg">
                </a>
                <div>
                    <h1 class="text-base sm:text-lg font-bold leading-tight">CCSA Admin Suite</h1>
                    <p class="text-[11px] text-blue-200 hidden sm:block">Centre for Computer Science and Applications, Dibrugarh University</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- Live Database Connection Status Badge -->
                <?php if (isset($pdo) && $pdo !== null): ?>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30" title="Connected directly to Hostinger MySQL Database">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span class="hidden md:inline">MySQL</span> Connected
                    </span>
                <?php else: ?>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30" title="Running in Local Cache Mirror">
                        <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                        <span class="hidden md:inline">Local</span> Cache
                    </span>
                <?php endif; ?>

                <div class="text-right hidden sm:block">
                    <p class="text-xs font-bold text-white"><?php echo $username; ?></p>
                    <p class="text-[11px] text-blue-200"><?php echo $useremail; ?> (<?php echo $userrole; ?>)</p>
                </div>
                <button type="button" onclick="document.getElementById('changePasswordModal').classList.remove('hidden')" 
                    class="bg-indigo-700/80 hover:bg-indigo-600 text-white text-xs font-bold px-3 py-2 rounded-xl transition-all flex items-center gap-1.5 shadow border border-indigo-400/30 cursor-pointer">
                    <i class="fas fa-key text-[11px]"></i>
                    <span class="hidden sm:inline">Change Password</span>
                </button>
                <a href="logout.php" 
                    class="bg-red-600 hover:bg-red-700 text-white text-xs font-bold px-3.5 py-2 rounded-xl transition-all flex items-center gap-1.5 shadow">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </header>

    <!-- Change Password Modal for All Roles (super_admin, admin, editor) -->
    <div id="changePasswordModal" class="hidden fixed inset-0 z-50 bg-slate-950/70 backdrop-blur-xs flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-200 relative animate-fadeIn">
            <div class="flex justify-between items-center pb-3 border-b border-slate-100 mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-base font-black">
                        <i class="fas fa-lock"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800">Change Account Password</h3>
                        <p class="text-xs text-slate-500"><?php echo $useremail; ?></p>
                    </div>
                </div>
                <button type="button" onclick="document.getElementById('changePasswordModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-700 text-2xl font-light">&times;</button>
            </div>

            <!-- Tab Switcher: Current Password vs Email OTP -->
            <div class="grid grid-cols-2 p-1 bg-slate-100 rounded-xl mb-4 border border-slate-200 text-xs">
                <button type="button" onclick="switchChangePassMode('current_pass')" id="btnModeCurrentPass" class="py-2 rounded-lg font-bold bg-[#1a365d] text-white transition-all">With Old Password</button>
                <button type="button" onclick="switchChangePassMode('otp')" id="btnModeOtp" class="py-2 rounded-lg font-semibold text-slate-600 hover:text-slate-900 transition-all">Authorize via OTP</button>
            </div>

            <div id="changePassAlert" class="hidden mb-4 p-3 rounded-xl text-xs font-semibold"></div>

            <form method="POST" action="actions.php" id="changePassForm" class="space-y-3.5">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                <input type="hidden" name="action" value="change_password">

                <!-- Mode A: Old Password -->
                <div id="fieldCurrentPass">
                    <label for="current_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Current Password</label>
                    <input type="password" id="current_password" name="current_password" placeholder="••••••••" 
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition-all">
                </div>

                <!-- Mode B: OTP Verification -->
                <div id="fieldOtp" class="hidden space-y-2">
                    <div class="flex items-center justify-between">
                        <label for="change_pass_otp" class="block text-xs font-bold text-slate-700 uppercase tracking-wider">6-Digit Email OTP</label>
                        <button type="button" onclick="sendChangePassOtp()" id="btnSendChangePassOtp" class="text-xs text-indigo-600 hover:text-indigo-800 font-bold">Send OTP Code</button>
                    </div>
                    <input type="text" id="change_pass_otp" name="otp" maxlength="6" placeholder="123456" 
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-mono tracking-widest text-center focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition-all">
                </div>

                <div>
                    <label for="new_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">New Password (Min 8 chars)</label>
                    <input type="password" id="new_password" name="new_password" required minlength="8" placeholder="••••••••" 
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition-all">
                </div>

                <div>
                    <label for="confirm_password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required minlength="8" placeholder="••••••••" 
                        class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:bg-white transition-all">
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-100">
                    <button type="button" onclick="document.getElementById('changePasswordModal').classList.add('hidden')" 
                        class="px-4 py-2.5 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 font-bold text-xs transition-colors">
                        Cancel
                    </button>
                    <button type="submit" 
                        class="px-5 py-2.5 bg-[#1a365d] hover:bg-indigo-900 text-white font-bold text-xs rounded-xl shadow transition-all flex items-center gap-1.5">
                        <i class="fas fa-check text-xs"></i>
                        <span>Update Password</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function switchChangePassMode(mode) {
        const btnCurrent = document.getElementById('btnModeCurrentPass');
        const btnOtp = document.getElementById('btnModeOtp');
        const fieldCurrent = document.getElementById('fieldCurrentPass');
        const fieldOtp = document.getElementById('fieldOtp');
        const curPassInput = document.getElementById('current_password');

        if (mode === 'otp') {
            btnOtp.className = 'py-2 rounded-lg font-bold bg-[#1a365d] text-white transition-all';
            btnCurrent.className = 'py-2 rounded-lg font-semibold text-slate-600 hover:text-slate-900 transition-all';
            fieldCurrent.classList.add('hidden');
            fieldOtp.classList.remove('hidden');
            curPassInput.required = false;
            curPassInput.value = '';
        } else {
            btnCurrent.className = 'py-2 rounded-lg font-bold bg-[#1a365d] text-white transition-all';
            btnOtp.className = 'py-2 rounded-lg font-semibold text-slate-600 hover:text-slate-900 transition-all';
            fieldCurrent.classList.remove('hidden');
            fieldOtp.classList.add('hidden');
            curPassInput.required = true;
            document.getElementById('change_pass_otp').value = '';
        }
    }

    async function sendChangePassOtp() {
        const btn = document.getElementById('btnSendChangePassOtp');
        const alertBox = document.getElementById('changePassAlert');
        const email = "<?php echo $userEmail; ?>";

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

        try {
            const res = await fetch('/admin/otp_actions.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'send_reset_otp', email: email, csrf_token: "<?php echo $csrf_token; ?>" })
            });
            const data = await res.json();
            if (data.success) {
                alertBox.className = 'mb-4 p-3 rounded-xl text-xs font-semibold bg-emerald-50 border border-emerald-200 text-emerald-800';
                alertBox.textContent = data.message;
                alertBox.classList.remove('hidden');
                btn.textContent = 'Code Sent!';
            } else {
                alertBox.className = 'mb-4 p-3 rounded-xl text-xs font-semibold bg-red-50 border border-red-200 text-red-700';
                alertBox.textContent = data.error || 'Failed to send OTP.';
                alertBox.classList.remove('hidden');
                btn.disabled = false;
                btn.textContent = 'Resend Code';
            }
        } catch (e) {
            alertBox.className = 'mb-4 p-3 rounded-xl text-xs font-semibold bg-red-50 border border-red-200 text-red-700';
            alertBox.textContent = 'Network error.';
            alertBox.classList.remove('hidden');
            btn.disabled = false;
            btn.textContent = 'Resend Code';
        }
    }
    </script>

    <!-- Main Workspace Container -->
    <div class="container mx-auto px-4 sm:px-6 py-8 flex-1">
        
        <!-- Toast Feedback -->
        <?php if ($success): ?>
            <div class="admin-toast mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold rounded-2xl flex items-center justify-between shadow-sm transition-all duration-500">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle text-emerald-600 text-base"></i>
                    <span><?php echo htmlspecialchars($success); ?></span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">&times;</button>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="admin-toast mb-6 p-4 bg-red-50 border border-red-200 text-red-800 text-sm font-semibold rounded-2xl flex items-center justify-between shadow-sm transition-all duration-500">
                <div class="flex items-center gap-2">
                    <i class="fas fa-exclamation-triangle text-red-600 text-base"></i>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800">&times;</button>
            </div>
        <?php endif; ?>

        <nav class="bg-white rounded-2xl p-2 shadow-sm border border-slate-200/90 mb-8 flex flex-wrap gap-2">
            <button onclick="switchTab('notifications')" id="tab-btn-notifications" 
                class="tab-btn flex-1 min-w-[110px] py-2.5 px-3 rounded-xl text-xs sm:text-sm font-bold transition-all text-center flex items-center justify-center gap-1.5 bg-[#1a365d] text-white shadow cursor-pointer">
                <i class="fas fa-bullhorn text-xs"></i>
                <span>Notices</span>
            </button>
            <button onclick="switchTab('messages')" id="tab-btn-messages" 
                class="tab-btn flex-1 min-w-[110px] py-2.5 px-3 rounded-xl text-xs sm:text-sm font-bold transition-all text-center flex items-center justify-center gap-1.5 bg-slate-100 text-slate-700 hover:bg-slate-200 cursor-pointer">
                <i class="fas fa-envelope-open-text text-xs"></i>
                <span>Inquiries</span>
            </button>
            <?php if (in_array('logs', $allowedTabs, true)): ?>
            <button onclick="switchTab('logs')" id="tab-btn-logs" 
                class="tab-btn flex-1 min-w-[110px] py-2.5 px-3 rounded-xl text-xs sm:text-sm font-bold transition-all text-center flex items-center justify-center gap-1.5 bg-slate-100 text-slate-700 hover:bg-slate-200 cursor-pointer">
                <i class="fas fa-shield-alt text-xs"></i>
                <span>Audit Logs</span>
            </button>
            <?php endif; ?>
            <?php if (in_array('admin', $allowedTabs, true)): ?>
            <button onclick="switchTab('admin')" id="tab-btn-admin" 
                class="tab-btn flex-1 min-w-[110px] py-2.5 px-3 rounded-xl text-xs sm:text-sm font-bold transition-all text-center flex items-center justify-center gap-1.5 bg-slate-100 text-slate-700 hover:bg-slate-200 cursor-pointer">
                <i class="fas fa-users-cog text-xs"></i>
                <span>Admins</span>
            </button>
            <?php endif; ?>
        </nav>

        <div id="tab-notifications" class="tab-content active">
            <?php include __DIR__ . '/sections/notifications.php'; ?>
        </div>

        <div id="tab-messages" class="tab-content">
            <?php include __DIR__ . '/sections/messages.php'; ?>
        </div>

        <?php if (in_array('logs', $allowedTabs, true)): ?>
        <div id="tab-logs" class="tab-content">
            <?php include __DIR__ . '/sections/logs.php'; ?>
        </div>
        <?php endif; ?>

        <?php if (in_array('admin', $allowedTabs, true)): ?>
        <div id="tab-admin" class="tab-content">
            <?php include __DIR__ . '/sections/admin_users.php'; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-500">
        CCSA Portal Administrator Suite &bull; Dibrugarh University &bull; Version 2.0.0
    </footer>

    <!-- Interactive Tab Script -->
    <script>
    function switchTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(pane => pane.classList.remove('active'));
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('bg-[#1a365d]', 'text-white', 'shadow');
            btn.classList.add('bg-slate-100', 'text-slate-700');
        });

        const targetPane = document.getElementById('tab-' + tabName);
        const targetBtn = document.getElementById('tab-btn-' + tabName);

        if (targetPane && targetBtn) {
            targetPane.classList.add('active');
            targetBtn.classList.remove('bg-slate-100', 'text-slate-700');
            targetBtn.classList.add('bg-[#1a365d]', 'text-white', 'shadow');
            sessionStorage.setItem('activeAdminTab', tabName);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab') || sessionStorage.getItem('activeAdminTab') || 'notifications';
        switchTab(tabParam);

        document.querySelectorAll('.admin-toast').forEach(toast => {
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-8px)';
                setTimeout(() => toast.remove(), 500);
            }, 3500);
        });
    });
    </script>
</body>
</html>