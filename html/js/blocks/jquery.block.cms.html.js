/*
 * jQuery File Upload User Interface Plugin 5.0.17
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://creativecommons.org/licenses/MIT/
 */

/*jslint nomen: true, unparam: true, regexp: true */
/*global window, document, URL, webkitURL, FileReader, jQuery */

(function($) {
    // The jQuery.aj namespace will automatically be created if it doesn't exist
    
    // var cmsImageEditor = undefined;
    if($.cmsBlocks == undefined){
        $.cmsBlocks = {};
    }
    
    $.cmsBlocks['cms.html'] = 
    {
        options: {
            title: "HTML Block",
            className: "cms-block-html"
        },
        data: {
          //  contents:""
        },
            
      
        collapse: function(){
            var contents = this.element.find('.contents').html();
            this.element.html(contents);
        },
        create: function(){
           
                    
   
            if(this.element.html() != ""){
                this.element.contents().wrapAll('<div class="contents"></div>');
        
               // this.element.html('<div class="contents">'+this.element.html()+'</div>');
            } else {
                this.element.append('<div class="contents"></div>');
            }
        },
        // create: function(e, block){  },
        configure: function(){
            var self = this;
            $('<div class="cms-block-html"><textarea></textarea></div>').dialog({
                autoOpen: true,
                resizable: false,
                width:550,
                height:600,
                title: "Edit HTML",
                modal: true,
                buttons: {
                    Save: function(){ 
                        var c = $(this).find('textarea').val();
                       // console.log(['html save'],c, this);
                        self.update({
                            contents: c
                        });
                        self.element.trigger('save');
                        $(this).dialog('close');
                    },
                    Cancel: function(){
                        self.element.trigger('cancel');
                        $(this).dialog('close')
                    }
                }
            }).find('textarea').val(this.element.find('.contents').html())
            },
        update: function(data){                   
           
           // this.save(data);
         //   console.log(['update html'], this.element, data.contents);
            this.element.find('.contents').html(data.contents);
        }
    }
})(jQuery);