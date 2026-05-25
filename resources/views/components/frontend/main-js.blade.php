 <!-- JS here -->
    <!-- <script src="assets/js/vendor/jquery-3.6.0.min.js"></script> -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{ asset('frontend/assets/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{ asset('frontend/assets/js/jquery.magnific-popup.min.js')}}"></script>
    <script src="{{ asset('frontend/assets/js/jquery.odometer.min.js')}}"></script>
    <script src="{{ asset('frontend/assets/js/jquery.appear.js')}}"></script>
    <script src="{{ asset('frontend/assets/js/swiper-bundle.min.js')}}"></script>
    <script src="{{ asset('frontend/assets/js/jquery.countdown.min.js')}}"></script>
    <script src="{{ asset('frontend/assets/js/svg-inject.min.js')}}"></script>
    <script src="{{ asset('frontend/assets/js/select2.min.js')}}"></script>
    <script src="{{ asset('frontend/assets/js/jquery-ui.min.js')}}"></script>
    <script src="{{ asset('frontend/assets/js/ajax-form.js')}}"></script>
    <script src="{{ asset('frontend/assets/js/wow.min.js')}}"></script>
    <script src="{{ asset('frontend/assets/js/aos.js')}}"></script>
    <script src="{{ asset('frontend/assets/js/main.js')}}"></script>
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>
    
    <script>
        const text = document.querySelector('.circle');
        text.innerHTML = text.textContent.replace(/\S/g, "<span>$&</span>");
        const element = document.querySelectorAll('.circle span');
        for (let i = 0; i < element.length; i++) {
            element[i].style.transform = "rotate(" + i * 14.5 + "deg)"
        }
    </script>

    <script>
        SVGInject(document.querySelectorAll("img.injectable"));
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let items = document.querySelectorAll(".service-item");

            items.forEach((item, index) => {
                if (index >= 8) {
                    item.classList.add("hidden");
                }
            });
        });
    </script>

    <script>
        document.getElementById("loadMoreBtn").addEventListener("click", function () {
            let hiddenItems = document.querySelectorAll(".service-item.hidden");
            hiddenItems.forEach((item, index) => {
                setTimeout(() => {
                    item.classList.remove("hidden");
                    item.classList.add("show");
                }, index * 100); // stagger effect
            });
            this.style.display = "none";
        });
    </script>

    <script>
        const toggleBtn = document.querySelector(".social-toggle-btn");
        const socialIcons = document.querySelector(".social-icons");

        toggleBtn.addEventListener("click", () => {
            socialIcons.classList.toggle("show");
            toggleBtn.classList.toggle("active");
        });
    </script>