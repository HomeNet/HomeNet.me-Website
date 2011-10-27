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
    
    
    
    $.cmsBlocks['cms.core'] = {
            options: {
                title: "CMS Block",
                className: "cms-block"
            },
            data: {},
            element: undefined,
            
            init: function(data){
                //only take predefined vars
                for(var i in this.data){
                    if(data[i] != undefined){
                        this.data[i] = data[i];
                    }
                }
            },
            
            
            collapse: function(){
              // console.log('default compact');
               this.element.empty();
            },
            expand: function(){
               var self =this;  
               //  var block = $(this).data('block');
               //     block.element = $(this);
               //     $(this).data('block', block);
                    
             // console.log(['default expand', this]);    

                // this.create.call(this);
                this.create();
                
      
                this.element.children().wrapAll('<div class="ui-widget-content ui-state-default"/>');
                
                var title = "";
                if( this.data.title){
                    title = ' - <span class="title">'+this.data.title+'</span>';
                }
                
                
                this.element.prepend('<div class="ui-widget-header handle"><span class="ui-icon ui-icon-carat-2-n-s"></span> '+this.options.title+title+'</div>');
            
                //data.order = this.itemCount;
                //self.data(block.data);
            
//                this.element.bind('edit',   block.edit);
//                this.element.bind('update', block.update);
//                this.element.bind('save',   block.save);
//                this.element.bind('remove', block.remove);
//                this.element.bind('collapse',block.collapse);
                //this.element.unbind('expand');
                //this.element.bind('expand', block.expand);


                var edit = $('<a class="ui-state-default ui-corner-all edit ui-button"><span class="ui-icon ui-icon-pencil"></span></a>');
                var del = $('<a class="ui-state-default ui-corner-all delete ui-button"><span class="ui-icon ui-icon-closethick"></span></a>');
                this.element.append(edit,del);
                
                
 
                edit.bind("click.block", $.proxy(this.configure,this));
                
                del.bind("click.selectimage", function(e){
                    $('<div>Are you sure you want to delete<br /> &quot;'+self.options.title+'&quot;</div>')
                    .dialog({
                        resizable: false,
                        title: "Delete",
                        modal: true,
                        buttons: {
                            Delete: function(){
                                self.remove();
                                $(this).dialog( "close" );
                            },
                            Cancel: function() {
                                $(this).dialog( "close" );
                            }
                        }
                    });                    
                }); 
                
                this.save();

            },
            create: function(){ },
            configure: function(){},
            save: function(data){
                
              //  console.log(['default save'], data, this.data);
                
                if(data){
                    this.data = $.extend(this.data, data);
                }
                
                for(var i in this.data){
                    this.element.attr('data-'+i, this.data[i]);
                }
              //  $this.data('block',block);
                    
            },
            update: function(data){

            },
            remove: function(){
              this.element.remove();
            }
        }
})(jQuery);