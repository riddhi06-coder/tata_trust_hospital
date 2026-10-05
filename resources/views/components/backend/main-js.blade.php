<!-- latest jquery-->
<script src="{{ asset('admin/assets/js/jquery.min.js') }}"></script>
    <!-- Bootstrap js-->
    <script src="{{ asset('admin/assets/js/bootstrap/bootstrap.bundle.min.js') }}"></script>
    <!-- feather icon js-->
    <script src="{{ asset('admin/assets/js/icons/feather-icon/feather.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/icons/feather-icon/feather-icon.js') }}"></script>
    <!-- scrollbar js-->
    <script src="{{ asset('admin/assets/js/scrollbar/simplebar.js') }}"></script>
    <script src="{{ asset('admin/assets/js/scrollbar/custom.js') }}"></script>
    <!-- Sidebar jquery-->
    <script src="{{ asset('admin/assets/js/config.js') }}"></script>
    <!-- Plugins JS start-->
    <script src="{{ asset('admin/assets/js/sidebar-menu.js') }}"></script>
    <script src="{{ asset('admin/assets/js/sidebar-pin.js') }}"></script>
    <script src="{{ asset('admin/assets/js/slick/slick.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/slick/slick.js') }}"></script>
    <script src="{{ asset('admin/assets/js/header-slick.js') }}"></script>
    <script src="{{ asset('admin/assets/js/editors/quill.js') }}"></script>
    <script src="{{ asset('admin/assets/js/notify/bootstrap-notify.min.js') }}"></script>
    <!-- calendar js-->
    <!-- <script src="{{ asset('admin/assets/js/dashboard/default.js') }}"></script> -->
    <script src="{{ asset('admin/assets/js/notify/index.js') }}"></script>
    <script src="{{ asset('admin/assets/js/typeahead/handlebars.js') }}"></script>
    <script src="{{ asset('admin/assets/js/typeahead/typeahead.bundle.js') }}"></script>
    <script src="{{ asset('admin/assets/js/typeahead/typeahead.custom.js') }}"></script>
    <script src="{{ asset('admin/assets/js/typeahead-search/handlebars.js') }}"></script>
    <script src="{{ asset('admin/assets/js/typeahead-search/typeahead-custom.js') }}"></script>
    <script src="{{ asset('admin/assets/js/height-equal.js') }}"></script>
    <!-- Plugins JS Ends-->

    <script src="{{ asset('admin/assets/js/datatable/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admin/assets/js/datatable/datatables/datatable.custom.js') }}"></script>
    
    <!-- Theme js-->
    <script src="{{ asset('admin/assets/js/script.js') }}"></script>

    <script>new WOW().init();</script>

    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

    <script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>

<script>
  $(document).ready(function() {
    $('#summernote').summernote({
      height: 200, // Adjust height as needed
      focus: true   // Focus the editor when initialized
    });
  });
</script>



<script>
    ClassicEditor.create(document.querySelector('#editor'), {
        toolbar: [
            'heading', 
            '|',
            'bold', 'italic', 'underline', 'strikethrough', 'subscript', 'superscript',
            'link', 'blockQuote', 'codeBlock',
            'bulletedList', 'numberedList', 'todoList',
            '|',
            'alignment', 'outdent', 'indent',
            '|',
            'fontColor', 'fontBackgroundColor', 'fontSize', 'fontFamily',
            '|',
            'insertTable', 'imageUpload', 'mediaEmbed', 'horizontalLine', 'pageBreak',
            '|',
            'undo', 'redo', 'removeFormat', 'highlight', 'specialCharacters'
        ],
        heading: {
            options: [
                { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                { model: 'heading6', view: 'h6', title: 'Heading 6', class: 'ck-heading_heading6' }
            ]
        },
        fontFamily: {
            options: [
                'default', 'Arial, Helvetica, sans-serif', 'Courier New, Courier, monospace',
                'Georgia, serif', 'Lucida Sans Unicode, Lucida Grande, sans-serif',
                'Tahoma, Geneva, sans-serif', 'Times New Roman, Times, serif',
                'Trebuchet MS, Helvetica, sans-serif', 'Verdana, Geneva, sans-serif'
            ]
        },
        fontSize: {
            options: [ 'tiny', 'small', 'default', 'big', 'huge' ]
        },
        alignment: {
            options: [ 'left', 'center', 'right', 'justify' ]
        }
    })
    .catch(error => { console.error(error); });
</script>





   <!-- Toastr Messages-->
    @if (session('message'))
    <script>
        (function ($) {
            "use strict";
            var notify = $.notify(
                '<i class="fa fa-bell-o"></i><strong>{{ session('message') }}</strong>',
                {
                    type: "theme",
                    allow_dismiss: true,
                    delay: 5000,
                    showProgressbar: true,
                    timer: 300,
                    animate: {
                        enter: "animated fadeInDown",
                        exit: "animated fadeOutUp",
                    },
                }
            );
        })(jQuery);
    </script>
@endif

@if ($errors->any())
    <script>
        (function ($) {
            "use strict";
            var notify = $.notify(
               '<i class="fa fa-bell-o"></i><strong>@foreach ($errors->all() as $error) {{ $error }}<br> @endforeach</strong>',
                {
                    type: "theme",
                    allow_dismiss: true,
                    delay: 5000,
                    showProgressbar: true,
                    timer: 300,
                    animate: {
                        enter: "animated fadeInDown",
                        exit: "animated fadeOutUp",
                    },
                }
            );
        })(jQuery);
    </script>
@endif

{{-- Live activity notifications (header bell) --}}
@auth
<script>
(function ($) {
    "use strict";
    var $bell = $('#anBellToggle');
    if (!$bell.length) return;

    var POLL_URL   = "{{ route('admin.notifications.poll') }}";
    var READALL_URL= "{{ route('admin.notifications.read-all') }}";
    var INDEX_URL  = "{{ route('admin.notifications.index') }}";
    var TOKEN      = $('meta[name=csrf-token]').attr('content') || '';
    var POLL_MS    = 20000;      // live refresh every 20s
    var REMIND_MS  = 300000;     // in-app nudge every 5 min while unread remain

    var $badge = $('#anBadge'), $list = $('#anList'), $dd = $('#anDropdown');
    var lastMaxId = 0, firstPoll = true, lastRemind = 0;

    function esc(s){ return $('<div>').text(s == null ? '' : s).html(); }

    // Clean custom toast (white card, accent bar, icon, title + detail, close).
    var $toastWrap = $('<div class="an-toast-wrap"></div>').appendTo('body');
    var TOAST_MS = 7000;
    var TOAST_ICONS = { success: 'bell', warning: 'clock', info: 'info', theme: 'bell' };

    function toast(o){
        var type = o.type || 'info';
        var $t = $(
            '<div class="an-toast an-toast--' + type + '">' +
              '<span class="an-toast__ic"><i data-feather="' + (TOAST_ICONS[type] || 'bell') + '"></i></span>' +
              '<div class="an-toast__body">' +
                '<div class="an-toast__title">' + o.title + '</div>' +
                (o.body ? '<div class="an-toast__sub">' + esc(o.body) + '</div>' : '') +
              '</div>' +
              '<button type="button" class="an-toast__close" aria-label="Dismiss">&times;</button>' +
              '<span class="an-toast__bar" style="animation:anBar ' + TOAST_MS + 'ms linear forwards;"></span>' +
            '</div>'
        );
        $toastWrap.append($t);
        if (window.feather) feather.replace();
        requestAnimationFrame(function(){ $t.addClass('in'); });

        var timer = setTimeout(remove, TOAST_MS);
        function remove(){ clearTimeout(timer); $t.addClass('out'); setTimeout(function(){ $t.remove(); }, 420); }

        $t.on('click', function(){ if (o.url) window.location.href = o.url; });
        $t.find('.an-toast__close').on('click', function(e){ e.stopPropagation(); remove(); });
    }

    // One shared audio context, unlocked on the first user gesture (browsers
    // block audio until the user interacts with the page).
    var audioCtx = null;
    function getCtx(){
        try { if (!audioCtx){ var C = window.AudioContext || window.webkitAudioContext; if (C) audioCtx = new C(); } } catch (e) {}
        return audioCtx;
    }
    function unlockAudio(){
        var c = getCtx();
        if (c && c.state === 'suspended') c.resume();
        document.removeEventListener('click', unlockAudio);
        document.removeEventListener('keydown', unlockAudio);
    }
    document.addEventListener('click', unlockAudio);
    document.addEventListener('keydown', unlockAudio);

    function chime(){
        try {
            var c = getCtx(); if (!c) return;
            if (c.state === 'suspended') c.resume();
            var t = c.currentTime;
            [[880, 0], [1174.7, 0.13]].forEach(function (n){        // two-note "ding-dong"
                var o = c.createOscillator(), g = c.createGain();
                o.connect(g); g.connect(c.destination); o.type = 'sine'; o.frequency.value = n[0];
                g.gain.setValueAtTime(0.0001, t + n[1]);
                g.gain.exponentialRampToValueAtTime(0.12, t + n[1] + 0.02);
                g.gain.exponentialRampToValueAtTime(0.0001, t + n[1] + 0.35);
                o.start(t + n[1]); o.stop(t + n[1] + 0.36);
            });
        } catch (e) {}
    }

    function render(items){
        if (!items.length) { $list.html('<div class="an-empty">No notifications yet.</div>'); return; }
        var html = '';
        items.forEach(function (n){
            html += '<a class="an-item ' + (n.unread ? 'unread' : '') + '" href="' + n.url + '">'
                 +    '<span class="an-ic ' + esc(n.color) + '"><i data-feather="' + esc(n.icon || 'bell') + '"></i></span>'
                 +    '<span class="an-it-body">'
                 +      '<span class="an-it-title">' + esc(n.title) + '</span>'
                 +      '<span class="an-it-sub">' + esc(n.body) + '</span>'
                 +      '<span class="an-it-ago">' + esc(n.ago) + '</span>'
                 +    '</span>'
                 +  '</a>';
        });
        $list.html(html);
        if (window.feather) feather.replace();
    }

    function setBadge(n){
        if (n > 0){ $badge.text(n > 99 ? '99+' : n).show(); $bell.addClass('has-unread'); }
        else { $badge.hide(); $bell.removeClass('has-unread'); }
    }

    function poll(){
        $.ajax({ url: POLL_URL, method: 'GET', dataType: 'json' }).done(function (res){
            setBadge(res.unread);
            render(res.items);

            var maxId = res.items.reduce(function (m, n){ return Math.max(m, n.id); }, 0);
            if (!firstPoll && maxId > lastMaxId){
                var fresh = res.items.filter(function (n){ return n.id > lastMaxId; });
                if (fresh.length === 1) toast({ title: '<b>' + esc(fresh[0].title) + '</b>', body: fresh[0].body, type: 'success', url: fresh[0].url });
                else if (fresh.length > 1) toast({ title: '<b>' + fresh.length + ' new notifications</b>', body: 'Click to view them all', type: 'success', url: INDEX_URL });
                chime();
            }
            lastMaxId = Math.max(lastMaxId, maxId);
            firstPoll = false;

            // Periodic in-app reminder while unread remain (click to open the list).
            var now = Date.now();
            if (res.unread > 0 && (now - lastRemind) > REMIND_MS){
                lastRemind = now;
                toast({ title: '<b>' + res.unread + '</b> unread notification' + (res.unread > 1 ? 's' : ''), body: 'Click to review them', type: 'warning', url: INDEX_URL });
            }
        });
    }

    $bell.on('click', function (e){ e.stopPropagation(); $dd.toggleClass('show'); });
    $(document).on('click', function (e){ if (!$(e.target).closest('.an-notif-wrap').length) $dd.removeClass('show'); });

    $('#anMarkAll').on('click', function (e){
        e.preventDefault(); e.stopPropagation();
        $.ajax({ url: READALL_URL, method: 'POST', headers: { 'X-CSRF-TOKEN': TOKEN } }).done(function (){
            setBadge(0);
            $list.find('.an-item').removeClass('unread');
        });
    });

    poll();
    setInterval(poll, POLL_MS);
})(jQuery);
</script>
@endauth


