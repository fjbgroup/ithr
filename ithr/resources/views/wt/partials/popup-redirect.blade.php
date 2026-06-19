@if(session('popup_success') && session('popup_redirect'))
<style>
    #submitSuccessModal {
        background: rgba(15, 23, 42, 0.48);
    }
    #submitSuccessModal .submit-success-card {
        border: 1px solid #e2e8f0;
        background: #ffffff;
        color: #0f172a;
        box-shadow: 0 22px 60px rgba(15, 23, 42, 0.22);
    }
    #submitSuccessModal .submit-success-icon {
        background: #d1fae5;
        color: #059669;
    }
    #submitSuccessModal .submit-success-title {
        color: #0f172a;
    }
    #submitSuccessModal .submit-success-message {
        color: #475569;
    }
    #submitSuccessModal .submit-success-footer {
        border-top: 1px solid #e2e8f0;
        background: #f8fafc;
    }
    #submitSuccessModal .submit-success-ok {
        background: #8B5E3C;
        color: #ffffff;
        box-shadow: 0 10px 22px rgba(139, 94, 60, 0.18);
    }
    #submitSuccessModal .submit-success-ok:hover {
        background: #724D31;
    }
    html.dark #submitSuccessModal,
    .dark #submitSuccessModal {
        background: rgba(2, 6, 23, 0.68);
    }
    html.dark #submitSuccessModal .submit-success-card,
    .dark #submitSuccessModal .submit-success-card {
        border-color: #334155;
        background: #172236;
        color: #e2e8f0;
        box-shadow: 0 22px 60px rgba(2, 6, 23, 0.42);
    }
    html.dark #submitSuccessModal .submit-success-icon,
    .dark #submitSuccessModal .submit-success-icon {
        background: rgba(16, 185, 129, 0.16);
        color: #6ee7b7;
    }
    html.dark #submitSuccessModal .submit-success-title,
    .dark #submitSuccessModal .submit-success-title {
        color: #f8fafc;
    }
    html.dark #submitSuccessModal .submit-success-message,
    .dark #submitSuccessModal .submit-success-message {
        color: #cbd5e1;
    }
    html.dark #submitSuccessModal .submit-success-footer,
    .dark #submitSuccessModal .submit-success-footer {
        border-top-color: #334155;
        background: #0f172a;
    }
    html.dark #submitSuccessModal .submit-success-ok,
    .dark #submitSuccessModal .submit-success-ok {
        background: #B38A5A;
        color: #111827;
        box-shadow: 0 10px 22px rgba(179, 138, 90, 0.18);
    }
    html.dark #submitSuccessModal .submit-success-ok:hover,
    .dark #submitSuccessModal .submit-success-ok:hover {
        background: #d6ad79;
    }
</style>
<div id="submitSuccessModal" class="fixed inset-0 z-[9999] flex items-center justify-center px-4 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="submitSuccessTitle">
    <div class="submit-success-card w-full max-w-md overflow-hidden rounded-2xl">
        <div class="px-6 py-6 text-center">
            <div class="submit-success-icon mx-auto flex h-14 w-14 items-center justify-center rounded-full">
                <i class="fas fa-circle-check text-2xl"></i>
            </div>
            <h3 id="submitSuccessTitle" class="submit-success-title mt-4 text-sm font-black uppercase tracking-[0.16em]">Request Submitted</h3>
            <p class="submit-success-message mt-3 text-[12px] font-bold leading-6">{{ session('popup_success') }}</p>
        </div>
        <div class="submit-success-footer px-6 py-4">
            <button type="button" id="submitSuccessOk" class="submit-success-ok w-full rounded-xl px-5 py-3 text-[10px] font-black uppercase tracking-[0.18em] transition focus:outline-none focus:ring-4 focus:ring-[#8B5E3C]/20">
                OK
            </button>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const redirectUrl = @json(session('popup_redirect'));
        const okButton = document.getElementById('submitSuccessOk');

        okButton?.focus();
        okButton?.addEventListener('click', function () {
            window.location.href = redirectUrl;
        });
    });
</script>
@endif

