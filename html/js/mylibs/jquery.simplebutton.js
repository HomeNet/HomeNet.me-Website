(function($) {
    // The jQuery.aj namespace will automatically be created if it doesn't exist
    $.widget("cms.simplebutton", {
        
        options:{
            icon: null,
            text: true,
            disabled: false,
            label: null
        },

        _create: function() {
            //alert('My button');
            if(this.element.data('icon')){
                this.options.icon = this.element.data('icon');
            }
            
            if(this.element.data('text') != null){
                this.options.text = this.element.data('text');
            }
            
            var options = this.options;
            if ( options.label === null ) {
                options.label = this.element.html();
            }
            
            
            this.element
            .addClass( 'ui-button ui-widget ui-state-default ui-corner-all' )
            .attr( "role", "button" )
            .bind( "mouseenter.button", function() {
                if ( options.disabled ) {
                    return;
                }
                $( this ).addClass( "ui-state-hover" );
                
            })
            .bind( "mouseleave.button", function() {
                if ( options.disabled ) {
                    return;
                }
                $( this ).removeClass( 'ui-state-hover' );
            })
            .bind( "click.button", function( event ) {
                if ( options.disabled ) {
                    event.preventDefault();
                    event.stopImmediatePropagation();
                }
            })
            .bind( "mousedown.button", function() {
                if ( options.disabled ) {
                    return false;
                }
                $( this ).addClass( "ui-state-active" );
            })
            .bind( "mouseup.button", function() {
                if ( options.disabled ) {
                    return false;
                }
                $( this ).removeClass( "ui-state-active" );
            });
            
            if(options.text == true){
                this.element.html('<span class="ui-button-text">'+options.label+'</span>')
            } else {
                 this.element.empty();
            }
            
            if (options.icon != null) {
                if ( options.text ) {
                    this.element.addClass('ui-button-text-icon-primary');
                } else {
                    this.element.addClass('ui-button-text-icon-only');            
                }

                this.element.prepend( "<span class='ui-button-icon-primary ui-icon " + options.icon + "'></span>" );
 
                
            } else {
                 this.element.addClass('ui-button-text-only');
            }
            
            
            
            
           
        },
        _destroy: function(){ 
            this.element.unbind('.button');
            this.removeClass('ui-button ui-widget ui-state-default ui-corner-all');
        }
        
    });
})(jQuery);