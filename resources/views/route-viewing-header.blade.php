@if (config('mr4lc-route-viewing.enable', true))
    <script>
        var sessionId = "{{ \Illuminate\Support\Facades\Session::getId() }}";
        var wsUrl = "{{ config('mr4lc-route-viewing.ws_url') }}";
        var wsConfig = "{{ $config ?? '0' }}";
    </script>
    <link rel="stylesheet" href="{{ asset('vendor/mr4-lc/route-viewing/css/mr4-lc-route-viewing.css') }}">
    <template id="mr4-lc-route-viewing-popup">
        <div class="route-viewing-popup" id="route-viewing-popup">
            <div class="route-viewing-message">|||message|||</div>
        </div>
    </template>
@endif
