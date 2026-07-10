
<!doctype html>
<html lang="en">



    <head>

        @include('components.frontend.head')
    </head>

<body class="ty-page-body">
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