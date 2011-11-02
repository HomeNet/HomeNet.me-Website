(function($) {
    // The jQuery.aj namespace will automatically be created if it doesn't exist
    
    // var cmsImageEditor = undefined;
    if($.cmsBlocks == undefined){
        $.cmsBlocks = {};
    }
    
    
    
    $.cmsBlocks['cms.divider'] = 
    {
        options: {
            title: "Content Divider",
            className: "cms-block-divider",
            edit: false
        },
        create: function(){
            this.element.addClass('sort-level1-only');
        }
    }
            
})(jQuery);