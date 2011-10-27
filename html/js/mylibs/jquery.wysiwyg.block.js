(function($) {

    // var cmsImageEditor = undefined;
    if($.cmsBlocks == undefined){
        $.cmsBlocks = {};
    }
    
    $.widget("cms.wysiwygblock", {
        options: {
            autoOpen: false,
            scriptPath: '/js/blocks',
            type: '',
            data: {},
            editor: {},
            collapse: false
        },
        
        // module
        block: null,
        created: false,

        _create: function() {
            
            this.options.data = $.extend(this.element.data(), this.options.data);
            
//            if(!this.element){
//                this.element = 
//            }
          //  console.log(['element',this.element]);
            
            this.element.addClass('ui-widget block');
            
            var self = this;
            var type = this.element.data('block');
            if(type){
                this.options.type = type;
            }
            if($.cmsBlocks['cms.core'] === undefined){
                $.cmsBlocks['cms.core'] = null;
                $.getScript(this.options.scriptPath+'/jquery.block.cms.core.js', function(data, textStatus){
                   // console.log('cms.core was loaded');
                 //   self._initBlock();
                });
            }
           
            if($.cmsBlocks[this.options.type] === undefined){
                $.cmsBlocks[this.options.type] = null;
                $.getScript(this.options.scriptPath+'/jquery.block.'+ this.options.type+'.js', function(data, textStatus){
                    //console.log(self.options.type+' was loaded');
                  //  self._initBlock();
                });

            } 
             if((!$.cmsBlocks['cms.core']) || (!$.cmsBlocks[this.options.type]) ){
            
            //{
               var timer =  setInterval(function(){
                    if(($.cmsBlocks[self.options.type] !== null) && ($.cmsBlocks['cms.core'] !== null)){
                        clearInterval(timer);
                        self._initBlock();
                    }
                
                
                },100);
             } else {
               //  console.log('already loaded');
                 self._initBlock();
             }
        },
        
        
        _initBlock: function(){
           // console.log('_initblock');
//            if((!$.cmsBlocks['cms.core']) || (!$.cmsBlocks[this.options.type]) ){
//                return;
//            }
          //  this.block = Object.create($.cmsBlocks['cms.core']);
          
            var self = this;
            
            this.block = $.extend(true, {}, Object.create($.cmsBlocks['cms.core']), $.cmsBlocks[this.options.type]);
            this.block.element = this.element;
            this.block.init(this.options.data);
            this.element.bind('save', function(e, data){
                if(!self.created){
                   // console.log('trigger create');
                    self.created = true;
                    self._trigger('add', {target: self.element}, data);
                } else {
                    self._trigger('update', {target: self.element}, data);
                }
            });
            
            this.element.bind('cancel', function(e, data){
                if(!self.created){
                    self.remove();
                } 
            });
            
            this.element.addClass(this.block.options.className);
            
            if(this.options.collapse == true){
                this.collapse();
            } else {
                this.expand();
            }
            
            if(this.options.autoOpen){
                this.configure();
            }
        },
        
        
        
        _init: function(){
            
        },
        
        configure: function(){
           // console.log('configure');
            this.block.configure();
        },
        
        expand: function(){
            if(!this.block || this.element.hasClass('expanded')){return;}
           //console.log(['expand',this.block]);
            this.element.addClass('expanded');
            this.block.expand();
            this.element.find('*').andSelf().attr('contentEditable',false);
            this.element.disableSelection();
            
            //prevent resize border in ie from showing
            this.element.mousedown(function(e){
                e.preventDefault();
                //return false;
            })
        },
        collapse: function(){
            if(!this.block){return;}
            
            this.element.removeAttr('class');
            this.element.removeAttr('style');
            this.block.collapse();  
            this.element.find('*').andSelf().removeAttr('contentEditable');
           // console.log(['collapse', this.element.html()]);
        },
        
        edit: function(){
            if(!this.block){return;}
            this.block.edit(); 
           
        },
        update: function(data){
            if(!this.block){return;}
            
            this.block.update(data); 
        },
        
        
        remove: function(){
            if(!this.block){return;}
            
            this.block.remove(); 
        },
     
        destroy: function() {
            $.Widget.prototype.destroy.call(this);
        }
    });
})(jQuery);