(function($) {
    // The jQuery.aj namespace will automatically be created if it doesn't exist
    $.widget("cms.slugelement", {
        options: {
            separator: '-',
            length: 32,
            source: ''
        },
        disabled: false,
        
        
        _create: function() {
            this._loadOptions();
            
            if(this.options.source == '' ){
                console.log('Missing Source Selector');
            } else if($(this.options.source).length > 1){
                console.log('Invalid Source Selector');
            }
            this.enable();
        },
        
        _loadOptions: function() {
           // var data = this.element.data();
           // console.log()
            for(var i in this.options){
                
                var data = this.element.data(i)
               // console.log(['data',i,data]);
                if(data !== undefined){
                    this.options[i] = data;
                }
            }
        },
        
        enable: function(){
            this.disabled = false;
            if(this.element.val() == ''){
                var self = this;
                $(this.options.source).first().keyup(function(){
                    
                    if(self.disabled == true){
                        return;
                    }
                    
                    //based on http://dense13.com/blog/2009/05/03/converting-string-to-slug-javascript/
                    var slug = $(this).val();
                    var separator = self.options.separator;                    
                    
                    slug = slug.replace(/^\s+|\s+$/g, ''); // trim
                    slug = slug.toLowerCase();
  
                    // remove accents, swap ñ for n, etc
                    var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;";
                    var to   = "aaaaeeeeiiiioooouuuunc------";
                    for (var i=0, l=from.length ; i<l ; i++) {
                        slug = slug.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
                    }

                    slug = slug.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
                    .replace(/\s+/g, '-') // collapse whitespace and replace by -
                    .replace(/-+/g, separator); // collapse dashes

                    self.element.val(slug);
                });
                
                this.element.change(function(){
                    if((self.disabled == true) && (self.element.val() == '')){
                        self.enable()
                        return;
                    }
                    if(!self.disabled){
                        self.disable()
                    }
                });
            } else {
                this.disable();
            }
        },
        disable: function(){
            this.disabled = true;
            $(this.options.source).unbind('keyup');
        },
        
        destroy: function(){
            $.Widget.prototype.destroy.call( this );
        }
    });
})(jQuery);