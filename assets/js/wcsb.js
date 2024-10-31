jQuery(document).ready(function($){
    var wcsb_options = JSON.parse(ajax_object.wcsb_options);
    var show_after_time = 500;

    function hide_wcsb_toast() {
        var wcsb_options = JSON.parse(ajax_object.wcsb_options);
        var wpneo_toast = $('.wcsb-item-wrapper');

        switch (wcsb_options.hideMethod){
            case 'hide':
                wpneo_toast.hide();
                break;
            case 'fadeOut':
                wpneo_toast.fadeOut();
                break;
            case 'slideUp':
                wpneo_toast.slideUp();
                break;
        }
    }

    $(document).on('click', 'a.toastCloseBtn', function(e){
        e.preventDefault();
        hide_wcsb_toast();
    });
    var wcsb_toast_show_life = parseInt(wcsb_options.show_time_life) * 1000 + show_after_time;

    //Hover pause hide
    var wcsbToastHide = setTimeout(hide_wcsb_toast, wcsb_toast_show_life);
    $('.wcsb-item-wrapper').hover(
        function () { clearTimeout(wcsbToastHide); },
        function () { wcsbToastHide = setTimeout( hide_wcsb_toast, wcsb_toast_show_life); }
    );

    function show_wcsb_toast(){
        var wcsb_options = JSON.parse(ajax_object.wcsb_options);
        var wpneo_toast = $('.wcsb-item-wrapper');
        if (wpneo_toast.length > 0){
            switch (wcsb_options.showMethod){
                case 'show':
                    wpneo_toast.show();
                    break;
                case 'fadeIn':
                    wpneo_toast.fadeIn();
                    break;
                case 'slideDown':
                    wpneo_toast.slideDown();
                    break;
            }
        }
    }
    window.setTimeout(show_wcsb_toast, show_after_time);

});
