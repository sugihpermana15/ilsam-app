<!-- Begin Footer -->
<footer class="footer">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center gap-2">
            {{ __('common.footer.copyright', ['year' => now()->year]) }}
            <div class="text-sm-end d-none d-sm-block">
                {{ __('common.footer.by_it_team') }}
            </div>
        </div>
    </div>
</footer>
<!-- END Footer -->