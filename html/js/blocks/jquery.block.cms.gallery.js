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
    
    
    
    $.cmsBlocks['cms.gallery'] = 
    {
        options: {
            title: "Gallery Block",
            className: "cms-block-gallery"
        },
        data: {
            title: " ",
            rest:"",
            folder:"",
            hash:""
        },
        expand: function(){
          $.cmsBlocks['cms.core'].expand.call(this);
           var add = $('<a class="ui-state-default ui-corner-all add ui-button" href="#"><span class="ui-icon ui-icon-plusthick"></span></a>');
             this.element.append(add);
             add.click($.proxy(this.add,this));
        },
             
        collapse: function(){
            //this.element.children().empty();
             this.element.html(this.element.find('.item-container').children().removeAttr('class').removeAttr('style').empty());
        },
        create: function(){
            //console.log(['data dump',this.data])
            this.element.addClass('ui-widget cms-filemanager');
            this.gallery = $('<div />');
            this.gallery.html(this.element.html());
            this.gallery.galleryblockhelper({
                rest: this.data.rest,
                folder: this.data.folder,
                hash: this.data.hash
            });
            this.element.append(this.gallery);
//            
//            
//
//            if(this.element.html() != ""){
//                this.element.contents().wrapAll(this.gallery);
//        
//               // this.element.html('<div class="contents">'+this.element.html()+'</div>');
//            } else {
//                this.element.append(this.gallery);
//            }
        },
        add: function(){
            this.gallery.galleryblockhelper('selectImages');
            return false;
        },
        
       configure: function(){
            var self = this;
            $('<div class="cms-block-gallery"><div><label>Title:</label><input type="text" class="title" value="'+this.data.title+'"></div></div>').dialog({
                autoOpen: true,
                resizable: false,
                width:550,
                height:150,
                title: "Insert Gallery",
                modal: true,
                buttons: {
                    Save: function(){ 
                        var first = false;
                        if(self.data.title == " "){
                            first = true;
                        }
                        
                        self.update({
                            title: $(this).find('.title').val()
                        });
                        self.element.trigger('save');
                        
                        $(this).dialog('close');
                       if(first){
                         self.gallery.galleryblockhelper('selectImages');
                       }
                    },
                    Cancel: function(){
                        self.element.trigger('cancel');
                        $(this).dialog('close')
                    }
                }
            }).find('textarea').val(this.element.find('.contents').html());//.focus();
            },
        update: function(data){                   
           
            this.save(data);
         //   console.log(['update html'], this.element, data.contents);
            this.element.find('.title').text(data.title);
        }
    }
})(jQuery);