<script>
    window.addEventListener('erihs:scroll-to', (ev) => {
        ev.stopPropagation();

        // setTimeout( (ev) => {


            const selector = ev?.detail?.query;

            if (!selector) {
            return;
            }

            const el = window.document.querySelector(selector);

            if (!el) {
            return;
            }

            try {
                el.parentElement.scrollIntoView({
                    behavior: 'smooth',
                });
            } catch {
            }

        // }, 500);

    }, false);
</script>


