/**
 * GadgetKart - Main Frontend Scripts
 */

document.addEventListener('DOMContentLoaded', () => {
    // -------------------------------------------------------------
    // Mobile Navigation Toggle
    // -------------------------------------------------------------
    const toggle = document.getElementById('navToggle');
    const nav    = document.getElementById('mainNav');

    if (toggle && nav) {
        toggle.addEventListener('click', () => {
            nav.classList.toggle('open');
        });
    }

    // -------------------------------------------------------------
    // Confirmation Dialogs for Destructive Actions
    // -------------------------------------------------------------
    document.querySelectorAll('[data-confirm]').forEach((el) => {
        el.addEventListener('click', (e) => {
            if (!confirm(el.dataset.confirm)) {
                e.preventDefault();
            }
        });
    });

    // -------------------------------------------------------------
    // Password Visibility Toggle
    // -------------------------------------------------------------
    const showPass = document.getElementById('showPass');
    if (showPass) {
        showPass.addEventListener('change', () => {
            const inputs = document.querySelectorAll('#regPassword, input[name="confirm_password"]');
            inputs.forEach((input) => {
                input.type = showPass.checked ? 'text' : 'password';
            });
        });
    }

    // -------------------------------------------------------------
    // Registration Form Password Confirmation Validation
    // -------------------------------------------------------------
    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', (e) => {
            const p  = registerForm.querySelector('input[name="password"]');
            const cp = registerForm.querySelector('input[name="confirm_password"]');

            if (p && cp && p.value !== cp.value) {
                e.preventDefault();
                alert('Passwords do not match.');
                cp.focus();
            }
        });
    }

    // -------------------------------------------------------------
    // Auto-dismiss Alerts
    // -------------------------------------------------------------
    document.querySelectorAll('.alert').forEach((alert) => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-6px)';
            setTimeout(() => alert.remove(), 400);
        }, 4500);
    });

    // -------------------------------------------------------------
    // Hero 3D Interactive Parallax for hero-keyboard.png
    // -------------------------------------------------------------
    const heroSection = document.getElementById('heroSection') || document.querySelector('.hero');
    const heroWrap    = document.getElementById('heroImageWrap');
    const keyboardImg = document.getElementById('heroKeyboardImg');

    if (keyboardImg && heroWrap) {
        let targetTiltX  = 0;
        let targetTiltY  = 0;
        let currentTiltX = 0;
        let currentTiltY = 0;

        let targetTransX  = 0;
        let targetTransY  = 0;
        let currentTransX = 0;
        let currentTransY = 0;

        let targetRotZ  = -4.5;
        let currentRotZ = -4.5;

        let targetScale  = 1;
        let currentScale = 1;

        let isHovering  = false;
        let animFrameId = null;

        function renderParallax() {
            const ease = isHovering ? 0.12 : 0.08;

            currentTiltX  += (targetTiltX - currentTiltX) * ease;
            currentTiltY  += (targetTiltY - currentTiltY) * ease;
            currentTransX += (targetTransX - currentTransX) * ease;
            currentTransY += (targetTransY - currentTransY) * ease;
            currentRotZ   += (targetRotZ - currentRotZ) * ease;
            currentScale  += (targetScale - currentScale) * ease;

            // 3D Tilt, Parallax Translation & Elevation
            keyboardImg.style.transform = 
                `perspective(1000px) ` +
                `rotateX(${currentTiltX.toFixed(2)}deg) ` +
                `rotateY(${currentTiltY.toFixed(2)}deg) ` +
                `rotateZ(${currentRotZ.toFixed(2)}deg) ` +
                `translate3d(${currentTransX.toFixed(2)}px, ${currentTransY.toFixed(2)}px, 40px) ` +
                `scale(${currentScale.toFixed(3)})`;

            // Dynamic reactive shadow based on light angle
            const shadowX = (-currentTransX * 0.8).toFixed(1);
            const shadowY = (25 - currentTransY * 0.6).toFixed(1);
            keyboardImg.style.filter = 
                `drop-shadow(${shadowX}px ${shadowY}px 34px rgba(20, 25, 55, 0.28)) ` +
                `drop-shadow(0 0 32px rgba(108, 77, 246, 0.25))`;

            const delta = 
                Math.abs(targetTiltX - currentTiltX) +
                Math.abs(targetTiltY - currentTiltY) +
                Math.abs(targetTransX - currentTransX) +
                Math.abs(targetTransY - currentTransY) +
                Math.abs(targetScale - currentScale);

            if (isHovering || delta > 0.01) {
                animFrameId = requestAnimationFrame(renderParallax);
            } else {
                animFrameId = null;
                if (!isHovering) {
                    keyboardImg.classList.add('idle-float');
                    keyboardImg.style.transform = '';
                    keyboardImg.style.filter    = '';
                }
            }
        }

        function onMouseMove(e) {
            const rect    = keyboardImg.getBoundingClientRect();
            const centerX = rect.left + rect.width / 2;
            const centerY = rect.top + rect.height / 2;

            // Normalized coordinates from -1.3 to 1.3
            const normX = Math.max(-1.3, Math.min(1.3, (e.clientX - centerX) / (rect.width / 2)));
            const normY = Math.max(-1.3, Math.min(1.3, (e.clientY - centerY) / (rect.height / 2)));

            targetTiltX  = -normY * 18;      // Pitch (degrees)
            targetTiltY  = normX * 22;       // Yaw (degrees)
            targetTransX = normX * 26;       // Horizontal displacement (px)
            targetTransY = normY * 18;       // Vertical displacement (px)
            targetRotZ   = -4.5 + normX * 4; // Roll (degrees)
            targetScale  = 1.05;

            if (!animFrameId) {
                animFrameId = requestAnimationFrame(renderParallax);
            }
        }

        const triggerArea = heroSection || heroWrap;

        triggerArea.addEventListener('mouseenter', () => {
            isHovering = true;
            keyboardImg.classList.remove('idle-float');
            if (!animFrameId) {
                animFrameId = requestAnimationFrame(renderParallax);
            }
        });

        triggerArea.addEventListener('mousemove', onMouseMove);

        triggerArea.addEventListener('mouseleave', () => {
            isHovering   = false;
            targetTiltX  = 0;
            targetTiltY  = 0;
            targetTransX = 0;
            targetTransY = 0;
            targetRotZ   = -4.5;
            targetScale  = 1;

            if (!animFrameId) {
                animFrameId = requestAnimationFrame(renderParallax);
            }
        });

        // Touch interactions for mobile devices
        triggerArea.addEventListener('touchmove', (e) => {
            if (e.touches && e.touches[0]) {
                onMouseMove(e.touches[0]);
            }
        }, { passive: true });

        triggerArea.addEventListener('touchend', () => {
            isHovering   = false;
            targetTiltX  = 0;
            targetTiltY  = 0;
            targetTransX = 0;
            targetTransY = 0;
            targetRotZ   = -4.5;
            targetScale  = 1;

            if (!animFrameId) {
                animFrameId = requestAnimationFrame(renderParallax);
            }
        });
    }
});
