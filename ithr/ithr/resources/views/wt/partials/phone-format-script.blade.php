<script>
    (function () {
        function isPhoneField(field) {
            if (!field || field.tagName !== 'INPUT') return false;

            const type = (field.getAttribute('type') || 'text').toLowerCase();
            if (!['text', 'tel', 'search'].includes(type)) return false;

            const key = [
                field.name || '',
                field.id || '',
                field.placeholder || '',
                field.getAttribute('aria-label') || '',
                field.dataset.phoneFormat || ''
            ].join(' ').toLowerCase();

            return key.includes('phone') || key.includes('telefon');
        }

        function formatPhoneValue(value) {
            let digits = String(value || '').replace(/\D/g, '');

            if (digits === '') return '';

            if (digits.startsWith('1')) {
                digits = `0${digits}`;
            }

            if (digits.length <= 3) {
                return digits;
            }

            return `${digits.slice(0, 3)}-${digits.slice(3, 14)}`;
        }

        function formatPhoneField(field) {
            if (!isPhoneField(field)) return;

            const formatted = formatPhoneValue(field.value);
            if (field.value === formatted) return;

            field.value = formatted;
            const length = field.value.length;
            if (document.activeElement === field && typeof field.setSelectionRange === 'function') {
                field.setSelectionRange(length, length);
            }
        }

        function bindPhoneFields(root) {
            (root || document).querySelectorAll('input').forEach(formatPhoneField);
        }

        document.addEventListener('input', function (event) {
            formatPhoneField(event.target);
        }, true);

        document.addEventListener('change', function (event) {
            formatPhoneField(event.target);
        }, true);

        document.addEventListener('submit', function (event) {
            const form = event.target.closest('form');
            if (!form) return;

            form.querySelectorAll('input').forEach(formatPhoneField);
        }, true);

        document.addEventListener('DOMContentLoaded', function () {
            bindPhoneFields(document);

            new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    mutation.addedNodes.forEach(function (node) {
                        if (node.nodeType !== Node.ELEMENT_NODE) return;
                        if (node.matches && node.matches('input')) {
                            formatPhoneField(node);
                        }
                        if (node.querySelectorAll) {
                            bindPhoneFields(node);
                        }
                    });
                });
            }).observe(document.body, {
                childList: true,
                subtree: true
            });
        });
    })();
</script>

