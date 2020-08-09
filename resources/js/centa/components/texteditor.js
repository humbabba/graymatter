

initTextEdtitors = () => {
  let textEditors = $('.text-editor');
  console.log('Found ' + textEditors.length + ' text editors.');
}

function makeTextEditor(el,callback) {
    //Div to hold editor-input combo
    var editor = $('<div>');

    //Toolbar div and tools
    var toolbar = $('<div>');
    var toolsArray = [
        {class:'fas fa-bold',tool: 'bold'},
        {class:'fas fa-italic',tool: 'italic'},
        {class:'fas fa-underline',tool: 'underline'},
        {class:'fas fa-strikethrough',tool: 'strikeThrough'},
        {class:'spacer',tool: 'none'},
        {class:'fas fa-minus',tool: 'insertHorizontalRule'},
        {class:'spacer',tool: 'none'},
        {class:'fas fa-link',tool: 'createLink'},
        {class:'fas fa-unlink',tool: 'unlink'},
        {class:'spacer',tool: 'none'},
        {class:'fas fa-indent',tool: 'indent'},
        {class:'fas fa-outdent',tool: 'outdent'},
        {class:'spacer',tool: 'none'},
        {class:'fas fa-list-ol',tool: 'insertOrderedList'},
        {class:'fas fa-list-ul',tool: 'insertUnorderedList'},
        {class:'spacer',tool: 'none'},
        {class:'fas fa-text-height',tool: 'fontSize'},
        {class:'fas fa-palette',tool: 'foreColor'},
        {class:'spacer',tool: 'none'},
        {class:'fas fa-align-center',tool: 'justifyCenter'},
        {class:'fas fa-align-justify',tool: 'justifyFull'},
        {class:'fas fa-align-left',tool: 'justifyLeft'},
        {class:'fas fa-align-right',tool: 'justifyRight'},
        {class:'spacer',tool: 'none'},
        {class:'fas fa-subscript',tool: 'subscript'},
        {class:'fas fa-superscript',tool: 'superscript'},
        {class:'spacer',tool: 'none'},
        {class:'fas fa-minus-circle',tool: 'clearFormat'},
        {class:'fas fa-code',tool: 'toggleCode'},
    ];
    $(toolsArray).each(function(index,item) {
        if('spacer' === item.class) {
            var spacer = $('<span class="spacer">');
            toolbar.append(spacer);
            return true;
        }
        var tool = $('<i class="pointer toolbar-button">');
        tool.addClass(item.class);
        tool.on('mousedown',function(e) {
            e.preventDefault();
            var input = null;
            switch(item.tool) {
                case 'createLink':
                    input = prompt('Enter URL:');
                    if(input) {
                      document.execCommand(item.tool,false,input);
                    }
                    break;
                case 'fontSize':
                    input = prompt('Text size:','(Integer from 1 to 7)');
                    if(input) {
                      document.execCommand(item.tool,false,input);
                    }
                    break;
                case 'foreColor':
                    input = prompt('Hexidecimal color value for text:','#000000');
                    if(input) {
                      document.execCommand(item.tool,false,input);
                    }
                    break;
                case 'clearFormat':
                    var copyDiv = $(this).parent().parent().find('.fancy-text-div').first();
                    var codeDiv = $(this).parent().parent().find('.code-editor').first();
                    var numberOfLinks = copyDiv.find('a').length;
                    var targetInput = copyDiv.next('.richText');
                    var x = 0;
                    while(copyDiv.find('*').length > numberOfLinks) {
                        copyDiv.children().each(function(index,el) {
                            stripTags(el);
                        });
                        x++;
                        if(x>30) {
                            break;
                        }
                    }
                    copyDiv.html('<p>' + copyDiv.html().replace(/&nbsp;/g, ' ') + '</p>'); //Put the stripped code back inside a p tag
                    codeDiv.val(copyDiv.html()); //Put the stripped code into the editor
                    targetInput.val(copyDiv.html());
                    showUnsavedFlag();
                    break;
                case 'toggleCode':
                    var copyDiv = $(this).parent().parent().find('.fancy-text-div').first();
                    var codeDiv = $(this).parent().parent().find('.code-editor').first();
                    var buttonsToToggle = $(this).parent().parent().find('.toolbar-button').not('.fa-code');
                    var spacersToToggle = $(this).parent().parent().find('.spacer');
                    buttonsToToggle.toggle();
                    spacersToToggle.toggle();
                    copyDiv.toggle();
                    codeDiv.toggle();
                    break;
                default:
                    document.execCommand(item.tool,false,input);
                    break;
            }
        });
        toolbar.append(tool);
    });

    //Make edit elements
    var codeEditArea = $('<textarea style="display:none">');
    codeEditArea.addClass('form-control');
    codeEditArea.addClass('code-editor');
    var editArea = $('<div contenteditable="true">');
    editArea.addClass('form-control');
    editArea.addClass('fancy-text-div');
    editor.append(toolbar);
    editor.append(codeEditArea);
    editor.append(editArea);

    //The code-editable area
    if(el.val()) {
        //Get the existing value in there
        codeEditArea.val(el.val());
    } else {
        //Start editArea with a P element so the first line gets wrapped
        codeEditArea.val('<p><br></p>');
    }
    //Make it so updates to the editArea affect the original el's value
    codeEditArea.on('input',function() {
        el.val($(this).val());
        editArea.html($(this).val());
    });
    if(callback) {
        codeEditArea.on('keydown',function() {
          this.editAreaContent = $(this).val();
        }).on('keyup',function() {
          this.newEditAreaContent = $(this).val();
          if(this.editAreaContent != this.newEditAreaContent) { //We have changes to content, so run the callback
            callback();
          }
        });
    }

    //The editable area
    if(el.val()) {
        //Get the existing value in there
        editArea.html(el.val());
    } else {
        //Start editArea with a P element so the first line gets wrapped
        editArea.html($('<p><br></p>'));
    }
    //Make it so updates to the editArea affect the original el's value
    editArea.on('input',function() {
        el.val($(this).html());
        codeEditArea.val($(this).html());
    });
    if(callback) {
        editArea.on('keydown',function() {
          this.editAreaContent = $(this).html();
        }).on('keyup',function() {
          this.newEditAreaContent = $(this).html();
          if(this.editAreaContent != this.newEditAreaContent) { //We have changes to content, so run the callback
            callback();
          }
        });
    }
    editor.append(el);
    return editor;
}

function stripTags(el) {
    if('A' === el.tagName) {
        //Remove style and any data attr
        $(el).removeAttr('style');
        $.each($(el).data(), function (i) {
            $(el).removeAttr("data-" + i);
        });
        while((currentGeneration = $(el).children()).length) {
            currentGeneration.each(function(index,currentEl) {
                stripTags(currentEl);
            });
        }
    } else if('BR' === el.tagName) {
        $(el).remove();
    } else {
        $(el).replaceWith($(el).contents());
    }
}

//Init on load, then whenever called
initTextEdtitors();
