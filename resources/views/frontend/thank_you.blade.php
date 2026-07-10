
<!doctype html>
<html lang="en">



    <head>
   
        @include('components.frontend.head')

        <style>
            body {
                background: url('./assets/img/bg/backdrop-bg.webp');
                color: #0F0F0F;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 40px 20px;
            }

            .ty-card {
                background: #ffffff;
                max-width: 650px;
                width: 100%;
                border-radius: 10px;
                box-shadow: 0 20px 60px rgba(220, 116, 108, 0.12);
                overflow: hidden;
                text-align: center;
                animation: rise .6s ease both;
            }

            @keyframes rise {
                from {
                    opacity: 0;
                    transform: translateY(24px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .ty-top {
                background: #DC746C;
                padding: 20px 20px 65px;
                position: relative;
            }

            .ty-logo {
                background: #fff;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 14px 22px;
                border-radius: 14px;
                box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
            }

            .ty-logo img {
                max-height: 50px;
                width: auto;
                display: block;
            }

            .ty-check {
                width: 86px;
                height: 86px;
                background: #fff;
                border-radius: 50%;
                margin: -43px auto 0;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: 0 10px 26px rgba(220, 116, 108, 0.28);
                position: relative;
                z-index: 2;
            }

            .ty-check svg {
                width: 44px;
                height: 44px;
            }

            .ty-check circle {
                fill: #DC746C;
            }

            .ty-check path {
                stroke: #fff;
                stroke-width: 4;
                fill: none;
                stroke-dasharray: 40;
                stroke-dashoffset: 40;
                animation: draw .5s .5s ease forwards;
                stroke-linecap: round;
                stroke-linejoin: round;
            }

            @keyframes draw {
                to {
                    stroke-dashoffset: 0;
                }
            }

            .ty-body {
                padding: 20px 20px 20px;
            }

            .ty-body h1 {
                font-family: 'OpenSans-Bold';
                font-size: 26px;
                color: #DC746C;
                margin-bottom: 12px;
                letter-spacing: -0.3px;
            }

            .ty-body p {
                color: #0F0F0F;
                font-size: 16px;
                margin-bottom: 0;
            }

            .ty-body .sub {
                font-size: 16px;
                color: #0F0F0F;
            }

            .ty-actions {
                margin-top: 20px;
                display: flex;
                gap: 12px;
                flex-wrap: wrap;
                justify-content: center;
            }

            .ty-actions .btn img {
                filter: brightness(0) invert(1);
                width: 18px;
            }

            .btn-ghost {
                background: #f3eeec;
                color: var(--accent-dark);
            }

            .ty-foot {
                border-top: 1px solid #f0eceb;
                padding: 20px;
                font-size: 14px;
                color: #0F0F0F;
            }

            .ty-foot a {
                color: #DC746C;
                text-decoration: none;
            }

            @media (max-width: 480px) {
                .ty-body {
                    padding: 20px;
                }

                .btn {
                    width: 100%;
                    justify-content: center;
                }
                
                .ty-body p {
                    font-size: 14px;
                }
            }

            .ty-note {
                margin-top: 20px;
                padding: 14px 16px;
                background: #fbeeed;
                border-left: 5px solid #DC746C;
                border-radius: 5px;
                font-size: 14px;
                color: #DC746C;
                text-align: left;
            }

            .ty-note a {
                color: #DC746C;
                font-weight: 600;
                text-decoration: none;
            }
        </style>
    </head>

<body>
    <div class="ty-card">
        <div class="ty-top">
            <div class="ty-logo">
                <img src="{{ (' frontend/assets/img/logo/tata-trust-logo.webp') }}" alt="Small Animal Hospital Mumbai">
            </div>
        </div>

        <div class="ty-check">
            <svg viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg">
                <circle cx="30" cy="30" r="30"></circle>
                <path d="M18 31 L27 40 L43 22"></path>
            </svg>
        </div>

        <div class="ty-body">
            <h1>Thank You!</h1>
            <p>Your details have been submitted successfully.<br><br>
                Our team will review the details and contact you shortly to confirm your visit.
                A confirmation has been sent to your email address.
            </p>

            <div class="ty-actions">
                <a href="{{ route('frontend.index') }}" class="btn">Back to Home <img
                        src="{{ ('frontend/assets/img/icon/right_arrow.svg') }}" alt="Read More" class="injectable"></a>
                <a href="tel:02265383538" class="btn btn-ghost">Call Us: 022-6538-3538</a>
            </div>
        </div>

        <div class="ty-foot">
            Tata Trusts Small Animal Hospital, Mahalaxmi, Mumbai 400011<br>
            Need help? <a href="mailto:contactus@sahmumbai.com">contactus@sahmumbai.com</a>
        </div>
    </div>
</body>

</html>