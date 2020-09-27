//Globals
//For processEditAreaCode
let elObj,elObjParentObj,elTagName,elParentTagName,currentGeneration,selectionObject;

const openMarkerString = '<marker id="openMarker"></marker>';
const closeMarkerString = '<marker id="closeMarker"></marker>';
const tags = ['b','i','u','strike'];

//Define rich-text-editing tools.
const toolsArray = [
    {class:'fas fa-bold',tool: 'b',title: 'Bold'},
    {class:'fas fa-italic',tool: 'i',title: 'Italic'},
    {class:'fas fa-underline',tool: 'u',title: 'Underline'},
    {class:'fas fa-strikethrough toolbar-spacer',tool: 'strike',title: 'Strikethrough'},
    {class:'fas fa-image toolbar-spacer',tool: 'inserImage',title: 'Insert image'},
    {class:'fas fa-minus toolbar-spacer',tool: 'insertHorizontalRule',title: 'Horizontal rule'},
    {class:'fas fa-link',tool: 'createLink',title: 'Link'},
    {class:'fas fa-unlink toolbar-spacer',tool: 'unlink',title: 'Unlink'},
    {class:'fas fa-indent',tool: 'indent',title: 'Indent'},
    {class:'fas fa-outdent toolbar-spacer',tool: 'outdent',title: 'Outdent'},
    {class:'fas fa-list-ol',tool: 'insertOrderedList',title: 'Ordered list'},
    {class:'fas fa-list-ul toolbar-spacer',tool: 'insertUnorderedList',title: 'Unordered list'},
    {class:'fas fa-text-height',tool: 'fontSize',title: 'Font size'},
    {class:'fas fa-palette toolbar-spacer',tool: 'foreColor',title: 'Font color'},
    {class:'fas fa-align-center',tool: 'justifyCenter',title: 'Center'},
    {class:'fas fa-align-justify',tool: 'justifyFull',title: 'Justify'},
    {class:'fas fa-align-left',tool: 'justifyLeft',title: 'Aling left'},
    {class:'fas fa-align-right toolbar-spacer',tool: 'justifyRight',title: 'Align right'},
    {class:'fas fa-subscript',tool: 'subscript',title: 'Subscript'},
    {class:'fas fa-superscript toolbar-spacer',tool: 'superscript',title: 'Superscript'},
    {class:'fas fa-minus-circle',tool: 'clearFormat',title: 'Clear all formatting'},
    {class:'fas fa-code',tool: 'toggleCode',title: 'Toggle code view'},
];

//Find all hidden inputs with text-editor class and replace them with rich-text editors.
initTextEdtitors = (callback = false) => {
  //Turn hidden inputs with 'text-editor' class into rich-text editors
  $('input[type="hidden"]').each(function(index,item) {
      if($(item).hasClass('text-editor')) {

        //Bail if it's already a text-editor
        let itemParent = $(item).parent();
        if(itemParent.hasClass('textEditorMasterDiv')) {
          return;
        }

        let newElement = $(item).clone();
        let fancyEditor = makeTextEditor(newElement,callback);
        let toolbar = fancyEditor.find('.toolbar');

        insertMoreTools(toolbar);

        $(item).replaceWith(fancyEditor);
        processToolbarForWidth(toolbar);
      }
  });
}

//Build the container that will hold buttons that don't fit.
//It's hidden till revealed (if necessary) in processToolbarForWidth.
insertMoreTools = toolbar => {
  let moreToolsContainer = $('<span class="more-tools-container">');
  moreToolsContainer.on('click',function() {
    $(this).next('.more-tools-holder').fadeToggle();
  });
  let moreToolsButton = $('<i class="toolbar-button fas fa-ellipsis-h" title="Show/hide tools">');
  let moreToolsHolder = $('<div class="more-tools-holder">')
  moreToolsContainer.append(moreToolsButton);
  moreToolsContainer.insertAfter(toolbar);
  moreToolsHolder.insertAfter(moreToolsContainer);
}

//Depending on container width, hide tools that don't fit and display button to toggle them.
processToolbarForWidth = toolbar => {
  let moreToolsHolder = toolbar.parent().find('.more-tools-holder');
  let toolbarWidth = toolbar.outerWidth();
  let childrenWidth = 0;
  let children = toolbar.children();
  let childrenIndexMax = children.length - 1;
  let moreToolsContainer = toolbar.next('.more-tools-container');
  let widthModifier = moreToolsContainer.outerWidth(true);
  children.each(function(index,item) {
    let child = $(item);
    childrenWidth += child.outerWidth(true);

    //We leave room for the moreToolsContainer, unless we're on the last tool.
    //If it fits, we don't need the "more" button
    if(index === childrenIndexMax) {
      widthModifier = 0;
    }
    if(childrenWidth > (toolbar.outerWidth() - widthModifier)) {
      moreToolsHolder.append(child);
      moreToolsContainer.show();
    }
  });
}

//Handle window resize events viz. text-editors.
//This will make sure the toolbars display correctly.
$(window).resize(function() {
  //Find them all
  let textEditors = $('.textEditorMasterDiv');

  textEditors.each(function(index,el) {
    //Find the hidden input at the core of each
    let hiddenInput = $(el).find('input[type="hidden"]').first();
    //Reset hidden input in DOM instead of text-editor
    $(el).replaceWith(hiddenInput);
  });

  //Reinitialize all text editors
  initTextEdtitors(textEditorDefaultCallback);
});

//Build rich-text editors to replace hidden inputs with.
//Loops through tools defined above and assigns click events.
makeTextEditor = (el,callback = false) => {
    //Div to hold editor-input combo
    let editor = $('<div class="textEditorMasterDiv">');

    //Toolbar div and tools
    let toolbar = $('<div class="toolbar">');
    $(toolsArray).each(function(index,item) {
        let tool = $('<i class="toolbar-button">');
        tool.addClass(item.class);
        tool.attr('data-tool',item.tool);
        tool.prop('title',item.title);
        tool.on('mousedown',function(e) {
            e.preventDefault();
            let input = null;
            let copyDiv,codeDiv;
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
                    copyDiv = $(this).parent().parent().find('.fancy-text-div').first();
                    codeDiv = $(this).parent().parent().find('.code-editor').first();
                    let numberOfLinks = copyDiv.find('a').length;
                    let targetInput = copyDiv.next('.text-editor');
                    let x = 0;
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
                    copyDiv = $(this).parent().parent().find('.fancy-text-div').first();
                    codeDiv = $(this).parent().parent().find('.code-editor').first();
                    let buttonsToToggle = $(this).parent().parent().find('.toolbar-button').not('.fa-code');
                    let spacersToToggle = $(this).parent().parent().find('.spacer');
                    buttonsToToggle.toggle();
                    spacersToToggle.toggle();
                    copyDiv.toggle();
                    codeDiv.toggle();
                    break;
                default:
                    execTool(item.tool,editArea);
                    break;
            }
        });
        toolbar.append(tool);
    });

    //Make edit elements
    let codeEditArea = $('<textarea style="display:none">');
    codeEditArea.addClass('code-editor');
    let editArea = $('<div contenteditable="true">');
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

    //Only check for changes in codeEditArea if we have a callback.
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

    //Only check for changes in editArea if we have a callback.
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

execTool = (tool,editArea) => {
    let range = window.getSelection().getRangeAt(0);
    const openMarker = document.createElement('marker');
    $(openMarker).attr('id','openMarker');
    const closeMarker = document.createElement('marker');
    $(closeMarker).attr('id','closeMarker');
    range.insertNode(openMarker);
    range.collapse(false);
    range.insertNode(closeMarker);
    console.log('BEFORE: editArea.html()');
    console.log(editArea.html());
    editArea.children().each(function() {
        if('MARKER' === this.tagName) {
            if('openMarker' === $(this).prop('id')) {
                let that = $(this).next();
                that.prepend($(this));
            }
            if('closeMarker' === $(this).prop('id')) {
                let that = $(this).prev();
                that.append($(this));
            }
        }
    });

    getSelectionObject(tool,editArea);
    console.log('SELECTION OBJECT:');
    console.log(selectionObject);

    let tagImbalance = selectionObject.tagBalance.find(tagObj => tagObj.value !== 0);
    if('undefined' !== typeof tagImbalance) {
        rebalanceTag(tagImbalance,editArea);
    }

    internalizeTool(tool,editArea);

    console.log('AFTER: editArea.html()');
    console.log(editArea.html());

    getSelectionObject(tool,editArea);

    console.log('SELECTION OBJECT 2:');
    console.log(selectionObject);

    wrapTags(editArea);

    // processEditAreaCode(editArea);

    cleanRedundantCode(editArea);
    replaceMarkersWithSelection(editArea);
    removeEmptyTags(editArea);

    console.log('FINAL: editArea.html()');
    console.log(editArea.html());
};

internalizeTool = (tool,editArea) => {
  let editAreaString = editArea.html();
  let openTag = '<' + tool + '>';
  let closeTag = '</' + tool + '>';
  let openMarkerPattern = new RegExp(openTag + openMarkerString);
  let closeMarkerPattern = new RegExp(closeMarkerString + closeTag);
  editAreaString = editAreaString.replace(openMarkerPattern, openMarkerString + openTag).replace(closeMarkerPattern, closeTag + closeMarkerString);
  editArea.html(editAreaString);
  console.log('editArea.contents()');
  console.log(editArea.contents());
};

rebalanceTag = (tagImbalance,editArea) => {
    let tag = tagImbalance.name;
    if(tagImbalance.value > 0) {
        editArea.html(editArea.html().replace(closeMarkerString + '</' + tag + '>','</' + tag + '>' + closeMarkerString));
        editArea.html(editArea.html().replace(openMarkerString + '</' + tag + '>','</' + tag + '>' + openMarkerString));
    } else {
        editArea.html(editArea.html().replace(openMarkerString + '<' + tag + '>','<' + tag + '>' + openMarkerString));
        editArea.html(editArea.html().replace(closeMarkerString + '<' + tag + '>','<' + tag + '>' + closeMarkerString));
    }
};

getSelectionObject = (tool,editArea) => {
    selectionObject = {};
    selectionObject.openAncestor = false;
    selectionObject.closeAncestor = false;
    selectionObject.containsOpenTag = false;
    selectionObject.containsCloseTag = false;
    selectionObject.openAncestor = false;
    selectionObject.closeAncestor = false;
    selectionObject.sameAncestor = false;
    selectionObject.allFormatted = false;
    let editAreaString = editArea.html();
    let openTool = '<' + tool + '>';
    let closeTool = '</' + tool + '>';
    let openMarker = editArea.find('#openMarker');
    let openMarkerAncestor = openMarker.closest(tool);
    let openMarkerAncestorParagraph = openMarker.closest('p');
    let closeMarker = editArea.find('#closeMarker');
    let closeMarkerAncestor = closeMarker.closest(tool);
    let closeMarkerAncestorParagraph = closeMarker.closest('p');
    let patternString = openMarkerString + '(.*)' + closeMarkerString;
    let pattern = new RegExp(patternString);
    let contentString = editAreaString.match(pattern)[1];
    selectionObject.tagBalance = [];
    if(tool) {
        selectionObject.tool = tool;
        selectionObject.openTool = openTool;
        selectionObject.closeTool = closeTool;

        tags.forEach(tag => {
            let openTag = '<' + tag + '>';
            let closeTag = '</' + tag + '>';
            let tagReport = {};
            tagReport.name = tag;
            let pattern = new RegExp(openTag,'gi');
            tagReport.value = (contentString.match(pattern) || []).length;
            pattern = new RegExp(closeTag,'gi');
            tagReport.value -= (contentString.match(pattern) || []).length;
            selectionObject.tagBalance.push(tagReport);
        });
    }

    if(contentString) {
        selectionObject.contentString = contentString;
    }
    if(contentString.indexOf(openTool) > -1) {
        selectionObject.containsOpenTag = true;
    }

    if(contentString.indexOf(closeTool) > -1) {
        selectionObject.containsCloseTag = true;
    }
    if(openMarkerAncestor.length) {
        selectionObject.openAncestor = openMarkerAncestor;
    }
    if(closeMarkerAncestor.length) {
        selectionObject.closeAncestor = closeMarkerAncestor;
    }
    if(selectionObject.openAncestor && selectionObject.closeAncestor) {
        selectionObject.sameAncestor = openMarkerAncestor[0] === closeMarkerAncestor[0];
    }
    selectionObject.sameAncestorParagraph = openMarkerAncestorParagraph[0] === closeMarkerAncestorParagraph[0];

    //     selectionObject.allFormatted = true;
    // }

    //Determine if all text in contentString is already wrapped in this tool
    if((selectionObject.containsOpenTag || selectionObject.containsCloseTag) ||
      (selectionObject.openAncestor || selectionObject.closeAncestor)) {
      contentString = selectionObject.openTool + contentString + selectionObject.closeTool;
      console.log('DETERMINING 1 (contentString):');
      console.log(contentString);
      let openTagPattern = new RegExp(selectionObject.openTool + selectionObject.openTool, 'gi');
      let closeTagPattern = new RegExp(selectionObject.closeTool + selectionObject.closeTool, 'gi');
      contentString = contentString.replace(openTagPattern,selectionObject.openTool).replace(closeTagPattern,selectionObject.closeTool);
      console.log('DETERMINING 2 (contentString):');
      console.log(contentString);
      let contentStringObj = $('<span>');
      contentStringObj.html(contentString);
      console.log('DETERMINING 3 (contentStringObj.html()):');
      console.log(contentStringObj.html());
      let tools = contentStringObj.find(tool);
      console.log('DETERMINING 4 (tools):');
      console.log(tools);
      let wrappedText = '';
      tools.each(function() {
        if(0 === $(this).find(tool).length) {
          wrappedText += $(this).text();
        }
      });
      if(contentStringObj.text() === wrappedText) {
          selectionObject.allFormatted = true;
      }
    }
}

wrapTags = editArea => {
    let editAreaString = editArea.html();
    if(selectionObject.allFormatted) {
      editAreaString = reverseFormatting(editAreaString);
    } else {
      editAreaString = addFormatting(editAreaString);
    }
    console.log('editAreaString after wrapTags:');
    console.log(editAreaString);
    editArea.html(editAreaString);
};

addFormatting = editAreaString => {
    console.log('Adding formatting.');
    let betweenMarkersContent = getBetweenMarkersContent(editAreaString);
    return editAreaString.replace(betweenMarkersContent, selectionObject.openTool + betweenMarkersContent + selectionObject.closeTool);
}

reverseFormatting = editAreaString => {
  console.log('Reversing formatting.');
  let openMarkerPattern = new RegExp(openMarkerString);
  let closeMarkerPattern = new RegExp(closeMarkerString);

  if((selectionObject.openAncestor && !selectionObject.closeAncestor) || !selectionObject.openAncestor && !selectionObject.closeAncestor) {
      console.log('CASE: Open ancestor only or no ancestors.');
      editAreaString = editAreaString.replace(openMarkerPattern,'~~makeClose~~');
  } else {
      console.log('CASE: Close ancestor only or both ancestors');
      editAreaString = editAreaString.replace(openMarkerPattern,'~~makeClose~~').replace(closeMarkerPattern,'~~makeOpen~~');
  }
  
  editAreaString = editAreaString.replace('~~makeClose~~',selectionObject.closeTool + openMarkerString).replace('~~makeOpen~~',closeMarkerString + selectionObject.openTool);

  //Get rid of any open tools in between markers
  let betweenMarkersContent = getBetweenMarkersContent(editAreaString); //Need to redefine since we've mauled it above
  console.log('betweenMarkersContent in reverseFormatting');
  console.log(betweenMarkersContent);
  let openTagPattern = new RegExp(selectionObject.openTool, 'gi');
  let closeTagPattern = new RegExp(selectionObject.closeTool, 'gi');
  cleanBetweenMarkersContent = betweenMarkersContent.replace(openTagPattern,'').replace(closeTagPattern,'');
  console.log('cleanBetweenMarkersContent in reverseFormatting after replace');
  console.log(cleanBetweenMarkersContent);
  return editAreaString.replace(betweenMarkersContent,cleanBetweenMarkersContent);
}

getBetweenMarkersContent = editAreaString => {
  let betweenMarkersPattern = new RegExp(openMarkerString + '(.*)' + closeMarkerString);
  return editAreaString.match(betweenMarkersPattern)[1];
}

getCleanBetweenMarkersContent = betweenMarkersContent => {
  let openTagPattern = new RegExp(selectionObject.openTool,'gi');
  let closeTagPattern = new RegExp(selectionObject.closeTool,'gi');
  return betweenMarkersContent.replace(openTagPattern,'').replace(closeTagPattern,'');
}

areTagsBetween = (tool,editAreaString) => {
  let patternString = openMarkerString + '(.*)' + closeMarkerString;
  let pattern = new RegExp(patternString);
  let contentString = editAreaString.match(pattern)[1];

  let toolOpen = '<' + tool + '>';
  let toolClose = '</' + tool + '>';
  if(contentString.indexOf(toolOpen) > -1) {
    return true;
  }
  return false;
};

removeException = (tool,editAreaString) => {
  let patternString = openMarkerString + '(.*)' + closeMarkerString;
  let pattern = new RegExp(patternString);
  let contentString = editAreaString.match(pattern)[1];

  patternString = '<' + tool + '>';
  pattern = new RegExp(patternString,'gi');
  let updatedContentString = contentString.replace(pattern,'');

  patternString = '</' + tool + '>';
  pattern = new RegExp(patternString,'gi');
  let finalContentString = updatedContentString.replace(pattern,'');

  editAreaString = editAreaString.replace(contentString,finalContentString);

  return editAreaString;
};

replaceMarkersWithSelection = editArea => {
  const range = document.createRange();
  const selection = window.getSelection();
  selection.removeAllRanges();
  selection.addRange(range);
  range.setStartAfter(editArea.find('#openMarker')[0]);
  range.setEndBefore(editArea.find('#closeMarker')[0]);
  editArea.find('marker').remove();
}

processEditAreaCode = editArea => {
    let elObjDescendents = editArea.find('*');
    elObjDescendents.each(function() {
        let element = this;
        let elementObj = $(this);
        let elementTagName = element.tagName;
        let tool = elementTagName.toLowerCase();
        let elementParentObject = elementObj.parent();
        let elementAncestorObject = elementParentObject.closest(tool);
        if('MARKER' === elementTagName) {
            return;
        }
        let elementParentTagName = elementParentObject[0].tagName;
        if(elementTagName === elementParentTagName) {
            if (elementObj.text() === elementParentObject.text()) {
                let elementParentObjectString = elementParentObject.html();
                let replaceString = '';
                if(elementParentObjectString.indexOf(openMarkerString) > -1) {
                    elementParentObject.html(elementParentObject.html().replace(openMarkerString,''));
                    replaceString += openMarkerString;
                }
                elementObj.html(elementObj.html().replace(openMarkerString,''));
                replaceString += elementObj.html();
                if(elementParentObjectString.indexOf(closeMarkerString) > -1) {
                    elementParentObject.html(elementParentObject.html().replace(closeMarkerString,''));
                    if(replaceString.indexOf(closeMarkerString) === -1) {
                        replaceString += closeMarkerString;
                    }
                }
                elementParentObject.replaceWith(replaceString);
            } else {
                let newContent = '<' + tool + '>';
                elementParentObject.contents().each(function() {
                    if('#text' === this.nodeName) {
                        newContent += this.textContent;
                    } else if(elementTagName === this.nodeName) {
                        if(null !== this.previousSibling && (this.previousSibling.nodeName === this.nodeName || 'MARKER' === this.previousSibling.nodeName)) {
                            newContent += '</' + tool + '>';
                        }
                        newContent += this.innerHTML;
                        if(null !== this.nextSibling && (this.nextSibling.nodeName === this.nodeName || 'MARKER' === this.nextSibling.nodeName)) {
                            newContent += '<' + tool + '>';
                        }
                    } else if ('MARKER' === this.nodeName) {
                        newContent += this.outerHTML;
                    } else {
                        if('undefined' !== typeof this.innerHTML) {
                            newContent += this.innerHTML;
                        }
                    }
                });
                newContent += '</' + tool + '>';
                if(elementParentObject[0].parentNode) {
                    elementParentObject[0].outerHTML = newContent;
                }
            }
        } else {
            console.log('Tag name mismatch.');
        }
    });
};

cleanRedundantCode = editArea => {
    tags.forEach(function(tag) {
      let inspectedElements = editArea.find(tag);
      if(inspectedElements.length) {
        inspectedElements.each(function() {
          let inspectedElementsDescendants = $(this).find(tag);
          if(inspectedElementsDescendants.length) {
            inspectedElementsDescendants.each(function() {
              $(this).replaceWith($(this).html());
            });
          }
        });
      }
    });
    //Find specified redundancies
    let editAreaString = editArea.html();
    tags.forEach(function(tag) {
        let openTag = '<' + tag + '>';
        let closeTag = '</' + tag + '>';
        let redundantCloseOpen = new RegExp(closeTag + openMarkerString + openTag,'gi');
        editAreaString = editAreaString.replace(redundantCloseOpen,openMarkerString);
        redundantCloseOpen = new RegExp(closeTag + closeMarkerString + openTag,'gi');
        editAreaString = editAreaString.replace(redundantCloseOpen,closeMarkerString);
        redundantCloseOpen = new RegExp(closeTag + openTag,'gi');
        editAreaString = editAreaString.replace(redundantCloseOpen,'');
        redundantCloseOpen = new RegExp(openTag + closeTag,'gi');
        editAreaString = editAreaString.replace(redundantCloseOpen,'');
    });
    editArea.html(editAreaString);
};


removeEmptyTags = editArea => {
    editArea.children().each(function() {
        $(this).find('*').each(function() {
            if(0 === this.childNodes.length) {
                $(this).remove();
            } else if(' ' === $(this).html()) {
                $(this).replaceWith(' ');
            }
        });
    });
};

//Remove HTML (except links) from copy.
stripTags = el => {
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
};

//Init on load; include default defined in centa.js as callback.
initTextEdtitors(textEditorDefaultCallback);
