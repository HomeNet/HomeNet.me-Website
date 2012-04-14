/* Author: 
 * @author Matthew Doll
 * 
 * /* jQuery.contentEditable Plugin
Copyright © 2011 FreshCode
http://www.freshcode.co.za/

*/ 
if (typeof Object.create === 'undefined') {
    Object.create = function (o) { 
        function F() {} 
        F.prototype = o; 
        return new F(); 
    };
}



(function($) {
   
    // The jQuery.aj namespace will automatically be created if it doesn't exist
    $.widget("cms.wysiwyg", {
        options: {
            toolbar: "bold italic strike removeformat | insertLink | insertImage insertGallery insertHtml insertDivider "+
        "| unorderedlist orderedlist superscript subscript " +
        "section paragraph h2 h3 h4 | htmlView" //h1 //h5//blockquote code //fontcolor
        },
        actions: {
            bold: {
                title: "Bold",
                iconClass: 'cms-icon-bold',
                hotkey: 'ctrl+b',
                action: function(){
                    document.execCommand("bold", false, null);
                }
            },
            italic: {
                title: 'Italicize',
                iconClass: 'cms-icon-italic',
                hotkey: 'ctrl+i',
                action: function(){
                    document.execCommand('italic', false, null);
                }
            },
            underline: {
                title: 'Underline',
                iconClass: 'cms-icon-underline',
                hotkey: 'ctrl+u',
                action: function(){
                    document.execCommand('underline', false, null);
                }
            },
            strike: {
                title: "Strike",
                iconClass: 'cms-icon-strike',
                action: function(){
                    document.execCommand('strikeThrough', false, null);
                }
            },
            removeformat: {
                title: 'Remove Formating',
                iconClass: 'cms-icon-erase',
                hotkey: 'ctrl+m',
                action: function(){
                    document.execCommand('removeFormat', false, null);
                }
            },
            /////////////////////////////////
            insertLink: {
                title: "Insert Link to a web page",
                iconClass: 'cms-icon-link',
                hotkey: 'ctrl+l',
                action: function(){
                    //   var urlPrompt = prompt("Enter URL:", "http://");
                    // document.execCommand("createLink", false, urlPrompt);
                    alert(this.getSelectionContainerElement());
                    return false;
                }
            },
            blockquote: {
                title: "Blockquote",
                iconClass: 'cms-icon-quote',
                hotkey: 'ctrl+q',
                action: function(){
                    document.execCommand("FormatBlock", null, '<blockquote>');
                }
            },
            code: {
                title: "Code",
                iconClass: 'cms-icon-code',
                hotkey: 'ctrl+alt+k',
                action: function(){
                    document.execCommand("FormatBlock", null, '<pre>');
                }
            },
            ///////////////////////////////
            unorderedlist: {
                title: "Unordered List",
                iconClass: 'cms-icon-ul',
                hotkey: 'ctrl+alt+u',
                action: function(){
                    document.execCommand("InsertUnorderedList", false, null);
                }
            },
            orderedlist: {
                title: "Ordered List",
                iconClass: 'cms-icon-ol',
                hotkey: 'ctrl+alt+o',
                action: function(){
                    document.execCommand("InsertOrderedList", false, null);
                }
            },
            indent: {
                title: "Indent",
                iconClass: 'cms-icon-indent',
                hotkey: 'tab',
                action: function(){
                    document.execCommand("indent", false, null);
                }
            },
            outdent: {
                title: "Outdent",
                iconClass: 'cms-icon-outdent',
                hotkey: 'shift+tab',
                action: function(){
                    document.execCommand("outdent", false, null);
                }
            },
            superscript: {
                title: "Superscript",
                iconClass: 'cms-icon-superscript',
                hotkey: 'ctrl+.',
                action: function(){
                    document.execCommand("superscript", false, null);
                }
            },
            subscript: {
                title: "Subscript",
                iconClass: 'cms-icon-subscript',
                hotkey: 'ctrl+shift+.',
                action: function(){
                    document.execCommand("subscript", false, null);
                }
            },
            //////////////////////////////
            section: {
                title: "Section",
                iconClass: 'cms-icon-section',
                action: function(){ 
                    // document.execCommand("FormatBlock", null, '<div>');
             
                   // console.log('wrapping');
                  //  console.log($().wrapSelection().parentsUntil( this.editor));
                    document.createElement('section');//this fixes html5 issue in ie8
                    $().wrapSelection().parentsUntil('.editor').filter('p, h1, h2, h3, h4, h5, hr, ul, ol').wrapAll('<div class="section"/>'); //div class="section"
                    //  $().wrapSelection().parentsUntil('.editor').wrapAll('<div class="section" />');
                    this.editor.find('.selection').replaceWith(function() {
                        return $(this).contents();
                    }); //remove selection tags
                }
            },
            paragraph: {
                title: "Paragraph",
                iconClass: 'cms-icon-paragraph',
                hotkey: 'ctrl+alt+0',
                action: function(){
                    document.execCommand("FormatBlock", null, '<p>');
                }
            },
            h1: {
                title: "Heading 1",
                iconClass: 'cms-icon-h1',
                hotkey: 'ctrl+alt+1',
                action: function(){
                    document.execCommand("FormatBlock", null, '<h1>');
                }
            },
            h2: {
                title: "Heading 2",
                iconClass: 'cms-icon-h2',
                hotkey: 'ctrl+alt+2',
                action: function(){
                    document.execCommand("FormatBlock", null, '<h2>');
                }
            },
            h3: {
                title: "Heading 3",
                iconClass: 'cms-icon-h3',
                hotkey: 'ctrl+alt+3',
                action: function(){
                    document.execCommand("FormatBlock", null, '<h3>');
                }
            },
            h4: {
                title: "Heading 4",
                iconClass: 'cms-icon-h4',
                hotkey: 'ctrl+alt+4',
                action: function(){
                    document.execCommand("FormatBlock", null, '<h4>');
                }
            },
            h5: {
                title: "Heading 5",
                iconClass: 'cms-icon-h5',
                hotkey: 'ctrl+alt+5',
                action: function(){
                    document.execCommand("FormatBlock", null, '<h5>');
                }
            },
            ////////////////////////
            undo: {
                title: "Undo",
                iconClass: 'cms-icon-undo',
                action: function(){
                    document.execCommand("FormatBlock", null, '<h4>');
                }
                
            },
            redo: {
                title: "Redo",
                iconClass: 'cms-icon-redo',
                action: function(){
                    document.execCommand("FormatBlock", null, '<h5>');
                }
            },
            
            fontcolor: {
                title: "Font Color",
                iconClass: 'cms-icon-fontcolor',
                action: function(){
                   // $().wrapSelection().removeClass('selection').css('color','#f00');
                // this.editor.find('.selection').unwrap();
                    this.insertBlock('cms.html', {}, true);
                    
                }
            },
           insertImage: {
                title: "Insert Image",
                iconClass: 'cms-icon-image',
                hotkey: 'ctrl+g',
                init: function(){
                    var self = this;
                    this.editor.filemanager({
                        maxItems: 20, 
                        //folder: this.element.data('folder'), 
                        id: this.element.data('id'),
                        url: this.element.data('url'), 
                       // hash: this.element.data('hash'),
                        
                    
                       selected: function(event,data){
                             self.insertBlock('cms.image', data);
                       }
                    });
              
                },
                action: function(){
                    this.editor.filemanager("show");
                }
            },
            
            insertGallery: {
                title: "Insert Gallery",
                iconClass: 'cms-icon-gallery',
                action: function(){
                    this.insertBlock('cms.gallery', {}, true);
                }
            },
            insertHtml: {
                title: "Insert HTML",
                iconClass: 'cms-icon-html',
                action: function(){
                    this.insertBlock('cms.html', {}, true);
                }
            },
            
            insertDivider: {
                title: "Insert Read More Divder",
                iconClass: 'cms-icon-pagebreak',
                action: function(){
                    if(this.editor.find('div[data-block="cms.divider"]').length == 0){
                        this.insertBlock('cms.divider');
                    } else {
                        alert('Only one divider per article');
                    }
                }
            },
            
            
            
            
            
            htmlView:{
                title: "View HTML Source",
                iconClass: 'cms-icon-code',
                init: function(){
                    this.htmlView = false;
                },
                action: function(e){
                    var element = $(e.target).parent();
                    if(this.htmlView == true){
                        element.removeClass('ui-state-highlight');
                        element.unbind('.wysiwyg');
                        this.convertToEditor();
                        this.enable();
                        this.htmlView = false;
                    } else {
                        element.addClass('ui-state-highlight');
                        this.menubar.find('a').not(element).addClass('ui-state-disabled').unbind('.wysiwyg');
                        this.convertToTextarea();
                        this.htmlView = true;
                    }
                }
            }
        },
        
     
        
        blocks: {
            
            
        },
            
        _create: function() {
            var that = this;
            
            this.element.wrap('<div class="ui-widget cms-wysiwyg" />');
            this.container = this.element.parent();
            
            if(this.element.is("textarea")){
                this.textarea = this.element;
              //  console.log(['textarea', this.container]);
                this.textarea.hide();
                this.editor = $('<div />');
                this.editor.html(this.element.val());
                
                this.container.append(this.editor);
                this.element.parents('form').first().bind('submit',function(){
                    that.updateTextarea();
                  //  that.element.val(that.editor.html());
                });
                
            } else {
                this.editor = this.element;
                this.textarea = $('<textarea name="'+this.element.data('name')+'"></textarea>').appendTo(this.container).hide();
            }
            
            this._expandAll();
            
            this.editor.addClass("ui-widget-content");
 
            //build toolbar
            var sets = this.options.toolbar.split("|");
            
            this.menubar = $('<div class="toolbar" class="" />');
            for(var i in sets){
                var seg = $('<span class="ui-buttonset" />').appendTo(this.menubar);
                var bits = sets[i].split(" ");
                for(var j in bits){
                    var item = this.actions[bits[j]];
                    if(item != undefined){
                        //call init
                        
                        var button = $('<a href="#" class="ui-button ui-widget ui-state-default ui-state-disabled ui-button-icon-only" title="'+(item.title?item.title:'')+(item.hotkey?' ('+item.hotkey+')':'')+'"><span class="cms-icon '+(item.iconClass?item.iconClass:'')+'"></span></a>');
                        if(item.init){
                            $.proxy(item.init,this, button[0])();
                        }
   
                        button.bind("click", bits[j],$.proxy(this._action,this))
                        .appendTo(seg);
                    }
                }
            }

            this.menubar.children().each(function(index, element){
                var items = $(element).children();
                // items.disableSelection();
                items.first().addClass("ui-corner-left");
                items.last().addClass("ui-corner-right");
            });
            this.menubar.disableSelection();

            this.container.prepend(this.menubar.wrap('<div class="toolbar-wrapper"></div>'));
            this.menubar.wrap('<div class="toolbar-wrapper"></div>')
            
            var toolbarStart = that.menubar.offset().top;
            $(window).scroll(function () {
                var docTop = $(window).scrollTop();
                
                if (docTop >= toolbarStart) {
                    that.menubar.css({
                        "position": "fixed", 
                        "top": "0"
                    });
                } else  {
                    that.menubar.css("position", "relative");
                }
            });
            this.enable();
              
            this.editor.sortable({ 
                //containment:this.editor, 
                items: 'p, h2, h3, ul, ol, div.block', 
                placeholder: 'ui-state-highlight', 
                forcePlaceholderSize: true,
                handle: '.handle',
                axis: 'y',
                // cursorAt: 'left',
                cursor: 'move'
//                start: function(e, ui){
//                    
//                    if(ui.item.hasClass('sort-level1-only')){
//                        console.log(this);
//                    
//                       // alert('level1 only');
//                       //  $(this).sortable('option','cancel','p,h2,h3,ul,ol,div.block');
//                    } else {
//                       // $(this).sortable('option','cancel','');
//                    }
//                }
            });
            try {
            document.execCommand('2D-Position' , false, false);
            } catch (e){}
            
        //              this.editor.find('p, h2, h3, ul, ol').live({
        //                  load: function() {
        //                  },
        //                  
        //                  mouseenter: function() {
        //                   $(this).append('<div class="handle">handle</div>')
        //                  },
        //                  mouseleave: function() {
        //                     $(this).find('.handle').remove();
        //                  }});
                      
        },
        enabled: false,
        enable : function(){
            
            var items = this.menubar.find('a');
            items.bind("mouseover.wysiwyg", function(){
                $(this).addClass('ui-state-hover')
                });
            items.bind("mouseout.wysiwyg", function(){
                $(this).removeClass('ui-state-hover ui-state-active')
                });
            items.bind("mousedown.wysiwyg", function(){
                $(this).addClass('ui-state-active')
                });
            items.bind("mouseup.wysiwyg", function(){
                $(this).removeClass('ui-state-active')
                });
            
            
            items.removeClass('ui-state-disabled');
            this.editor.attr("contentEditable", "true").addClass("editor");
            this.enabled = true;
        },
        disable : function(){
            this.enabled = false;
            this.menubar.find('a').addClass('ui-state-disabled').unbind('.wysiwyg');
            this.editor.attr("contentEditable", false).removeClass("editor");
        },
        _init: function() {
        // this.dialog.dialog("open");
        },
        
       
            
        _action: function(event){
            event.stopPropagation();
            event.preventBubble = true;
            ;
            event.preventDefault();
           // this.editor.focus();
            if(this.enabled){
                this.actions[event.data].action.call(this, event);
            }
            return false;
        },
        
        //        _getSelection: function() {
        //            console.log(document.createRange());
        //            if (this.getRangeAt)
        //		return this.getRangeAt(0);
        //            else { // Safari!
        //		var range = document.createRange();
        //		range.setStart(this.anchorNode,selectionObject.anchorOffset);
        //		range.setEnd(this.focusNode,selectionObject.focusOffset);
        //		return range;
        //            }
        //            
        //            
        //            //           if ($.browser.msie) return document.selection;
        //            //            return document.getSelection();
        //        },

     
        


        insertBlock: function(name, data, configure){
          
         //  var 

           // console.log(["insert block",b]);
           var editor = this.element.data();
        //   console.log(['editordata',editor]);
           var self = this;
           
           
           data = $.extend({}, {url: editor.url, id:editor.id}, data);
           console.log(data, editor);
            var b =  $('<div data-block="'+name+'"/>');
            
            b.wysiwygblock({
                autoOpen: configure,
                type: name,
                data: data
            });
            
            this._insertBlock(b);
          
          
         // return b;

        },
        
        _insertBlock: function(block, prepend){
            
            
            try{
                document.execCommand("enableObjectResizing", false, false);
            } catch (e){}
            
            
            
            
           this.editor.focus();

            var element = this._getSelectionContainerElement();

            //$(element.parentNode).css('border','solid red');
            if($.browser.msie){
               // element = element.parentNode;
              //  document.selection
                document.execCommand('insertImage', false, '#replace');
               // this.editor.find('img[src="#replace"]').parentsUntil('.editor,section').css('border','solid red');
                this.editor.find('img[src="#replace"]').parentsUntil('.editor,section').last().after(block);
                this.editor.find('img[src="#replace"]').remove();
                return
            }

            if(element == this.editor.get(0)){
                return this.editor.prepend(block);
            }
            
            var container = $(element).parentsUntil('.editor,section').last();
            if(container.length == 0){
               container = $(element);
            }

            if(prepend){
                return container.before(block); //last() fixes issues with inserting into lists
            } else {
               // console.log(container);
                return container.after(block);
            }
        },


        _insertHtml: function(html){
           // this.editor.focus(); //fixes insert bug in IE
            if($.browser.msie){
               
                var range = document.selection.createRange();
                    range.collapse();

                if ($(range.parentElement()).parents('.editor').is('*')) {
                    try {
                        // Overwrite selection with provided html
                        range.pasteHTML(html);
                    } catch (e) { }
                } else {
                    this.editor.append(html);
                }
            //document.selection.createRange().collapse(); 

            //  
            //  document.createRange().pasteHTML(html);
            } else {
                window.getSelection().collapseToStart();
                document.execCommand("insertHtml", false, html)
            }
        },
    
        _getSelectionContainerElement: function() {
            var range, sel, container;
            if ($.browser.msie) {
                // IE case
                range = document.selection.createRange();
                range.collapse();
                return range.parentElement();
            //   return range.parentElement();
            } else if (window.getSelection) {
                sel = window.getSelection();
                sel.collapseToStart();
                if (sel.getRangeAt) {
                    if (sel.rangeCount > 0) {
                        range = sel.getRangeAt(0);
                    }
                } else {
                    // Old WebKit selection object has no getRangeAt, so
                    // create a range from other selection properties
                    range = document.createRange();
                    range.setStart(sel.anchorNode, sel.anchorOffset);
                    range.setEnd(sel.focusNode, sel.focusOffset);

                    // Handle the case when the selection was selected backwards (from the end to the start in the document)
                    if (range.collapsed !== sel.isCollapsed) {
                        range.setStart(sel.focusNode, sel.focusOffset);
                        range.setEnd(sel.anchorNode, sel.anchorOffset);
                    }
                }
                return range.commonAncestorContainer;
            }
        },
        
        _getDomTree: function(){
            var range = this. _getSelectionContainerElement();
            var tree = [];
            if (range) {
                

                // Check if the container is a text node and return its parent if so
                if(container.nodeType === 3){
                    container =  container.parentNode
                }
                while (container != this.editor.get(0)){
                    tree.push(container.tagName);
                    container =  container.parentNode;  
                } 
   
                var path = tree.pop();
                tree = tree.reverse()
                for(var i in tree){
                    path += ' > '+tree[i];
                }
                return path;
            } 
            return null;
        },
        _collapseAll: function(){
            this.editor.find('.block').wysiwygblock('collapse');
        },
        _expandAll: function(){
            this.editor.find('div[data-block]').wysiwygblock();
        },

        convertToTextarea: function(){
            this.textarea.width(this.editor.width());
            this.textarea.height(this.editor.height());
            
            this.editor.hide();
////            this._collapseAll();
//            
//            this.textarea.val(this.editor.html());

            this.updateTextarea();

            this.textarea.show();
        },
        convertToEditor: function(){
            this.editor.html(this.textarea.val());
            this.textarea.hide();
            this._expandAll();
            
            this.editor.show();
        },
        
        
        updateTextarea: function(){
            //console.log(['collpase',this.htmlView]);
            if(this.htmlView == true){
                return;
            }
                var temp = this.editor.clone().detach();
               // temp.find('.block').each(function(index, element){ console.log('found block');$(element).wysiwygblock({collapse:true});});
                temp.find('.block').wysiwygblock({collapse:true});
               // console.log(['html dump', temp.html()]);
               // temp.find('.block').wysiwygblock('collapse');
                this.textarea.val(temp.html());//
           //   this._collapseAll();
           //   this.textarea.val(this.editor.html());
           //   this._expandAll();
    
        },
        
        
        //        replaceSelection: function(text) {
        //
        //            if($.browser.msie){
        //                this.focus();
        //                document.selection.createRange().text = text;
        //                return this;
        //            } else {
        //                this.innerHTML = this.value.substr(0, this.selectionStart) + text + this.value.substr(this.selectionEnd, this.value.length);
        //                return this;
        //            }
        //            
        //            
        //            
        //        },
        
        
        // getRange - gets the current text range object
        //  function getRange(editor) {
        //    if (ie) return getSelection(editor).createRange();
        //    return getSelection(editor).getRangeAt(0);
        //  }
        //
        //  // getSelection - gets the current text range object
        //  function getSelection(editor) {
        //    if (ie) return editor.doc.selection;
        //    return editor.$frame[0].contentWindow.getSelection();
        //  }
        // selectedHTML - returns the current HTML selection or and empty string
        //  function selectedHTML(editor) {
        //    restoreRange(editor);
        //    var range = getRange(editor);
        //    if (ie)
        //      return range.htmlText;
        //    var layer = $("<layer>")[0];
        //    layer.appendChild(range.cloneContents());
        //    var html = layer.innerHTML;
        //    layer = null;
        //    return html;
        //  }
        //
        //  // selectedText - returns the current text selection or and empty string
        //  function selectedText(editor) {
        //    restoreRange(editor);
        //    if (ie) return getRange(editor).text;
        //    return getSelection(editor).toString();
        //  }

        
        

        destroy: function() {
            //this.dialog.dialog("destroy");
            $.Widget.prototype.destroy.call(this);
        }
    });
})(jQuery);