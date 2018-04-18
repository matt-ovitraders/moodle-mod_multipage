define(['jquery', 'core/log'], function($, log) {
    "use strict";
    log.debug('multipage js module loaded');
    return {
        init: function() {
              $('.mod_multipage_button').click(function() {  
                  log.debug('toggling now');       
                  $('.mod_multipage_panel_div').toggle();
              });
        }
   }
});