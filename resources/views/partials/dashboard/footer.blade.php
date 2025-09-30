</body>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
@vite(['resources/js/flash.min.js', 'resources/js/flash.jquery.min.js'])
@stack('script')
<script>
$('document').ready(function () {
    $('.jq-msg').flashjs();
});
</script>
</html>