<?php
// Usar el mismo entorno por defecto
require_once 'app/config/seciones.php';
require_once 'app/view/layout/header.php';
require_once 'app/view/layout/navbar.php';
?>

<style>
/* 404 Glitch Effect Styles */
.glitch-wrapper {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    height: 60vh;
    text-align: center;
}

.glitch-text {
    font-size: 8rem;
    font-weight: 900;
    line-height: 1;
    color: var(--ctp-text);
    position: relative;
    margin-bottom: 20px;
    letter-spacing: -5px;
    z-index: 1;
}

.glitch-text::before,
.glitch-text::after {
    content: "404";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: transparent;
}

.glitch-text::before {
    left: 4px;
    text-shadow: -2px 0 var(--ctp-red);
    clip: rect(24px, 550px, 90px, 0);
    animation: glitch-anim-2 3s infinite linear alternate-reverse;
    z-index: -1;
}

.glitch-text::after {
    left: -4px;
    text-shadow: -2px 0 var(--ctp-blue);
    clip: rect(85px, 550px, 140px, 0);
    animation: glitch-anim 2.5s infinite linear alternate-reverse;
    z-index: -2;
}

@keyframes glitch-anim {
    0% { clip: rect(74px, 9999px, 86px, 0); }
    5% { clip: rect(13px, 9999px, 3px, 0); }
    10% { clip: rect(4px, 9999px, 81px, 0); }
    15% { clip: rect(27px, 9999px, 31px, 0); }
    20% { clip: rect(117px, 9999px, 89px, 0); }
    25% { clip: rect(100px, 9999px, 51px, 0); }
    30% { clip: rect(76px, 9999px, 126px, 0); }
    35% { clip: rect(104px, 9999px, 48px, 0); }
    40% { clip: rect(43px, 9999px, 20px, 0); }
    45% { clip: rect(79px, 9999px, 54px, 0); }
    50% { clip: rect(52px, 9999px, 93px, 0); }
    55% { clip: rect(38px, 9999px, 2px, 0); }
    60% { clip: rect(113px, 9999px, 58px, 0); }
    65% { clip: rect(96px, 9999px, 82px, 0); }
    70% { clip: rect(138px, 9999px, 137px, 0); }
    75% { clip: rect(61px, 9999px, 29px, 0); }
    80% { clip: rect(54px, 9999px, 87px, 0); }
    85% { clip: rect(46px, 9999px, 42px, 0); }
    90% { clip: rect(119px, 9999px, 57px, 0); }
    95% { clip: rect(46px, 9999px, 68px, 0); }
    100% { clip: rect(80px, 9999px, 4px, 0); }
}

@keyframes glitch-anim-2 {
    0% { clip: rect(65px, 9999px, 100px, 0); }
    5% { clip: rect(52px, 9999px, 74px, 0); }
    10% { clip: rect(79px, 9999px, 85px, 0); }
    15% { clip: rect(75px, 9999px, 5px, 0); }
    20% { clip: rect(67px, 9999px, 61px, 0); }
    25% { clip: rect(14px, 9999px, 79px, 0); }
    30% { clip: rect(1px, 9999px, 66px, 0); }
    35% { clip: rect(86px, 9999px, 30px, 0); }
    40% { clip: rect(23px, 9999px, 98px, 0); }
    45% { clip: rect(85px, 9999px, 72px, 0); }
    50% { clip: rect(71px, 9999px, 75px, 0); }
    55% { clip: rect(2px, 9999px, 48px, 0); }
    60% { clip: rect(30px, 9999px, 16px, 0); }
    65% { clip: rect(59px, 9999px, 50px, 0); }
    70% { clip: rect(41px, 9999px, 62px, 0); }
    75% { clip: rect(2px, 9999px, 82px, 0); }
    80% { clip: rect(47px, 9999px, 73px, 0); }
    85% { clip: rect(3px, 9999px, 27px, 0); }
    90% { clip: rect(26px, 9999px, 55px, 0); }
    95% { clip: rect(42px, 9999px, 97px, 0); }
    100% { clip: rect(38px, 9999px, 49px, 0); }
}
</style>

<main class="container py-5 min-vh-80">
    <div class="row align-items-center">
        <div class="col-12 animate-in">
            <div class="glitch-wrapper">
                <div class="glitch-text">404</div>
                <h2 style="color: var(--ctp-red); font-weight: 700; margin-bottom: 20px;">¡Oops! Te perdiste.</h2>
                <p style="color: var(--ctp-subtext0); max-width: 500px; font-size: 1.1rem; line-height: 1.6;">
                    La página que estás buscando parece que fue movida, borrada o tal vez nunca existió en el espacio digital de BELAMITECH.
                </p>
                <div class="d-flex gap-3 mt-4">
                    <a href="/" class="btn btn-primary btn-lg" style="border-radius: 12px;">
                        <i class="bi bi-house-door me-2"></i>Volver al Inicio
                    </a>
                    <button onclick="history.back()" class="btn btn-outline-secondary btn-lg" style="border-radius: 12px; color: var(--ctp-text); background: rgba(255,255,255,0.05); border-color: var(--ctp-surface1);">
                        <i class="bi bi-arrow-left me-2"></i>Regresar
                    </button>
                </div>
                
                <div class="mt-5 pt-4 text-center">
                    <img src="/assets/img/logo.png" alt="BELAMITECH Logo" style="height: 60px; object-fit: contain; filter: drop-shadow(0 0 10px rgba(0,0,0,0.2)); opacity: 0.8;">
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once 'app/view/layout/footer.php'; ?>
