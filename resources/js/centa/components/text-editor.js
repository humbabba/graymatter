/**
 * Globals
 */
let currentGeneration,selectionObject;
let ancestorTools = [];
let selectedTools = [];
let activeTools = [];
let toReverse = [];
let toAdd = [];
const openMarkerString = '<marker id="openMarker"></marker>';
const closeMarkerString = '<marker id="closeMarker"></marker>';
const tags = ['b','i','u','strike','sub','sup'];
const advancedTags = ['ol','ul','hr'];
const allTags = tags.concat(advancedTags);
const advancedFormat = ['ul','ol','hr','indent','outdent'];

/**
 * Define rich-text editing tools
 * @type {({title: string, class: string, tool: string}|{title: string, class: string, tool: string}|{title: string, class: string, tool: string}|{title: string, class: string, tool: string}|{title: string, class: string, tool: string})[]}
 */
const toolsArray = [
    {class:'fas fa-bold',tool: 'b',title: 'Bold (ctrl-b)'},
    {class:'fas fa-italic',tool: 'i',title: 'Italic (ctrl-i)'},
    {class:'fas fa-underline',tool: 'u',title: 'Underline (ctrl-u)'},
    {class:'fas fa-strikethrough toolbar-spacer',tool: 'strike',title: 'Strikethrough'},
    {class:'fas fa-image toolbar-spacer',tool: 'insertImage',title: 'Insert image'},
    {class:'fas fa-minus toolbar-spacer',tool: 'hr',title: 'Horizontal rule'},
    {class:'fas fa-link',tool: 'createLink',title: 'Link'},
    {class:'fas fa-unlink toolbar-spacer',tool: 'unlink',title: 'Unlink'},
    {class:'fas fa-indent',tool: 'indent',title: 'Indent'},
    {class:'fas fa-outdent toolbar-spacer',tool: 'outdent',title: 'Outdent'},
    {class:'fas fa-list-ol',tool: 'ol',title: 'Ordered list'},
    {class:'fas fa-list-ul toolbar-spacer',tool: 'ul',title: 'Unordered list'},
    {class:'fas fa-text-height',tool: 'fontSize',title: 'Font size'},
    {class:'fas fa-palette toolbar-spacer',tool: 'foreColor',title: 'Font color'},
    {class:'fas fa-align-center',tool: 'justifyCenter',title: 'Center'},
    {class:'fas fa-align-justify',tool: 'justifyFull',title: 'Justify'},
    {class:'fas fa-align-left',tool: 'justifyLeft',title: 'Aling left'},
    {class:'fas fa-align-right toolbar-spacer',tool: 'justifyRight',title: 'Align right'},
    {class:'fas fa-subscript',tool: 'sub',title: 'Subscript'},
    {class:'fas fa-superscript toolbar-spacer',tool: 'sup',title: 'Superscript'},
    {class:'fas fa-minus-circle',tool: 'clearFormat',title: 'Clear all formatting'},
    {class:'fas fa-code',tool: 'toggleCode',title: 'Toggle code view'},
];

/**
* Find all hidden inputs with text-editor class and replace them with rich-text editors.
*/
export const initTextEditors = (timeout = 0) => {
    //Turn hidden inputs with 'text-editor' class into rich-text editors
    $('input[type="hidden"]').each(function(index,item) {
        if($(item).hasClass('text-editor')) {
            //Bail if it's already a text-editor
            const itemParent = $(item).parent();
            if(itemParent.hasClass('textEditorMasterDiv')) {
                return;
            }

            const newElement = $(item).clone();
            const fancyEditor = makeTextEditor(newElement);
            const toolbar = fancyEditor.find('.toolbar');

            insertMoreTools(toolbar);
            $(item).replaceWith(fancyEditor);

            //Optional timeout gives makeTextEditor a few milliseconds to be completely added to DOM on first load
            if (timeout) {
                setTimeout(function() {
                    processToolbarForWidth(toolbar);
                    },timeout);
            } else {
                processToolbarForWidth(toolbar);
            }
        }
    });
};

/**
* Build the container that will hold buttons that don't fit.
* It's hidden till revealed (if necessary) in processToolbarForWidth.
*/
const insertMoreTools = toolbar => {
    const moreToolsContainer = $('<span class="more-tools-container">');
    moreToolsContainer.on('click',function() {
        $(this).next('.more-tools-holder').fadeToggle();
    });
    const moreToolsButton = $('<i class="toolbar-button fas fa-ellipsis-h" title="Show/hide tools">');
    const moreToolsHolder = $('<div class="more-tools-holder">')
    moreToolsContainer.append(moreToolsButton);
    moreToolsContainer.insertAfter(toolbar);
    moreToolsHolder.insertAfter(moreToolsContainer);
};

/**
* Depending on container width, hide tools that don't fit and display button to toggle them.
*/
const processToolbarForWidth = toolbar => {
    const moreToolsHolder = toolbar.parent().find('.more-tools-holder');
    let childrenWidth = 0;
    const children = toolbar.children();
    const childrenIndexMax = children.length - 1;
    const moreToolsContainer = toolbar.next('.more-tools-container');
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
};

/**
* Handle window resize events viz. text-editors.
* This will make sure the toolbars display correctly.
*/
$(window).resize(function() {
    //Find them all
    const textEditors = $('.textEditorMasterDiv');

    textEditors.each(function(index,el) {
        //Find the hidden input at the core of each
        const hiddenInput = $(el).find('input[type="hidden"]').first();
        //Reset hidden input in DOM instead of text-editor
        $(el).replaceWith(hiddenInput);
    });

    //Reinitialize all text editors
    initTextEditors();
});

/**
* Build rich-text editors to replace hidden inputs with.
* Loops through tools defined above and assigns click events.
*/
const makeTextEditor = el => {
    //Div to hold editor-input combo
    const editor = $('<div class="textEditorMasterDiv">');

    //Toolbar div and tools
    const toolbar = $('<div class="toolbar">');
    $(toolsArray).each(function(index,item) {
        const tool = $('<i class="toolbar-button">');
        tool.addClass(item.class);
        tool.attr('data-tool',item.tool);
        tool.prop('title',item.title);
        tool.on('mousedown',function(e) {
            e.preventDefault();
            let input = null;
            let copyDiv,codeDiv;
            switch(item.tool) {
                case 'insertImage':
                    insertImage(editArea);
                    break;
                case 'createLink':
                    input = prompt('Enter URL:');
                    if(input) {
                        copyDiv = $(this).closest('.textEditorMasterDiv').find('.fancy-text-div').first();
                        codeDiv = $(this).closest('.textEditorMasterDiv').find('.code-editor').first();
                        unlinkSelection(copyDiv,codeDiv,el);
                        const props = {
                          "href":input,
                          "target":"_blank"
                        };
                        execFormattingTool('a',editArea,true,props);
                    }
                    break;
                case 'unlink':
                    copyDiv = $(this).closest('.textEditorMasterDiv').find('.fancy-text-div').first();
                    codeDiv = $(this).closest('.textEditorMasterDiv').find('.code-editor').first();
                    unlinkSelection(copyDiv,codeDiv,el);
                    break;
                case 'ol':
                case 'ul':
                    execFormattingTool(item.tool,editArea,false);
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
                    copyDiv = $(this).closest('.textEditorMasterDiv').find('.fancy-text-div').first();
                    codeDiv = $(this).closest('.textEditorMasterDiv').find('.code-editor').first();
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
                    copyDiv.html('<p>' + copyDiv.html().replace(/&nbsp;/g, ' ').replace(/\s\s/g, ' ').trim() + '</p>'); //Put the stripped code back inside a p tag
                    codeDiv.val(copyDiv.html()); //Put the stripped code into the editor
                    targetInput.val(copyDiv.html());
                    showUnsavedFlag();
                    break;
                case 'toggleCode':
                    copyDiv = $(this).closest('.textEditorMasterDiv').find('.fancy-text-div').first();
                    codeDiv = $(this).closest('.textEditorMasterDiv').find('.code-editor').first();
                    let buttonsToToggle = $(this).closest('.textEditorMasterDiv').find('.toolbar-button').not('.fa-code');
                    let spacersToToggle = $(this).closest('.textEditorMasterDiv').find('.spacer');
                    buttonsToToggle.toggle();
                    spacersToToggle.toggle();
                    copyDiv.toggle();
                    codeDiv.toggle();
                    break;
                default:
                    execFormattingTool(item.tool,editArea);
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

    //Start codeEditArea with a P element so the first line gets wrapped
    codeEditArea.val(paragraphize(el.val()));

    //Only check for changes in codeEditArea if we have a callback.
    if(textEditorOnChangeCallback) {
        codeEditArea.on('keydown',function() {
          this.editAreaContent = $(this).val();
        }).on('keyup',function() {
          this.newEditAreaContent = $(this).val();
          if(this.editAreaContent != this.newEditAreaContent) { //We have changes to content, so run the callback
              textEditorOnChangeCallback();
          }
        });
    }

    //Start editArea with a P element so the first line gets wrapped
    editArea.html(paragraphize(el.val()));

    //Make it so updates to the editArea affect the original el's value and the code editor
    editArea.on('input change',function() {
        let code = jQuery(this).html();
        //We need to check is an operation like breaking a list inserted a div and paragraphize it while keeping the selection where it is supposed to be
        if(-1 < code.indexOf('<div></div>') || -1 < code.indexOf('<div><br></div>')) {
            code = code.replace('<div></div>','<p>' + openMarkerString + closeMarkerString + '<br></p>').replace('<div><br></div>','<p>' + openMarkerString + closeMarkerString + '<br></p>');
            editArea.html(code);
            replaceMarkersWithSelection(editArea);
            code = editArea.html();
        }
        el.val(code);
        codeEditArea.val(code);
    });

    //Update original el and editArea on changes in code editor
    codeEditArea.on('input change',function() {
        const code = jQuery(this).val();
        el.val(code);
        editArea.html(code);
    });

    //Only check for changes in editArea if we have a callback.
    if(textEditorOnChangeCallback) {
        editArea.on('keydown',function() {
          this.editAreaContent = $(this).html();
        }).on('keyup',function() {
          this.newEditAreaContent = $(this).html();
          if(this.editAreaContent != this.newEditAreaContent) { //We have changes to content, so run the callback
              textEditorOnChangeCallback();
          }
        });
    }

    //Make sure we inactivate tools when exiting editArea
    editArea.on('focusout',function() {
      inactivateAllToolsDisplay($(this));
    });

    //If we've got a selected tool waiting to execute, run it on first input
    editArea.on('input',function(e) {
      if(toAdd.length || toReverse.length) {
        let range = window.getSelection().getRangeAt(0);
        if(range.startOffset > 0) { //We have room to go back a character
          range.setStart(range.startContainer, range.startOffset - 1);
        }
        //We concat both those waiting to add and to reverse
        const tools = toAdd.concat(toReverse.filter((item) => toAdd.indexOf(item) < 0));
        tools.forEach(function(tool,index) {
          execFormattingTool(tool,editArea);
        });
        range = window.getSelection().getRangeAt(0);
        range.collapse(false);
        toAdd = [];
        toReverse = [];
        selectedTools = [];
      }
    });

    //Reset selected tools, evaluate formatting on arrow keys
    editArea.on('keydown',function(e) {
      if(37 === e.which || //Left
       39 === e.which || //Right
       38 === e.which || //Up
       40 === e.which  //Down
     ) {
      selectedTools = [];
      evaluateFormatting($(this),e);
    }
    });

    //Reset selected tools, evaluate formatting on mouseup, presumably following click or selection
    editArea.on('mouseup',function(e) {
      selectedTools = [];
      evaluateFormatting($(this),e);
    });

    //Evaluate formatting on keyup, to catch deletions and so forth
    editArea.on('keyup',function(e) {
      evaluateFormatting($(this),e);
    });

    editor.append(el);
    return editor;
};

const insertImage = (editArea) => {
  return false;
};

/**
 * Remove all links in the selection
 * @param copyDiv
 * @param codeDiv
 * @param hiddenInput
 */
const unlinkSelection = (copyDiv,codeDiv,hiddenInput) => {
    const originalCode = copyDiv.html();

    //Get the selection range
    let range = window.getSelection().getRangeAt(0);
    if(range.collapsed) {
        return;
    }

    const unlinkWrapper = jQuery('<unlink>');

    range = insertOpenAndCloseMarkers(range);

    // Manually wrap code inside selection with temp 'unlink' element
    const openMarkerPattern = new RegExp(openMarkerString);
    const closeMarkerPattern = new RegExp(closeMarkerString);
    let codeToWrap = copyDiv.html();
    codeToWrap = codeToWrap.replace(openMarkerPattern,openMarkerString + '<unlink>').replace(closeMarkerPattern,'</unlink>' + closeMarkerString);
    copyDiv.html(codeToWrap);

    let unlinkElement = copyDiv.find('unlink').first();

    let inside = unlinkElement.find('a').closest('a');
    let outside = unlinkElement.closest('a');

    //Merge together tags in the selection and those in the ancestry
    let allAnchors = inside.add(outside);

    allAnchors.each(function() {
        jQuery(this).replaceWith(jQuery(this).html());
    });

    //Have to reset unlinkElement case the above replacement hoses it in Chrome
    unlinkElement = copyDiv.find('unlink').first();
    unlinkElement.replaceWith(unlinkElement.html());
    replaceMarkersWithSelection(copyDiv);

    const updatedCode = copyDiv.html();
    codeDiv.html(updatedCode);
    hiddenInput.val(updatedCode);

    if(updatedCode !== originalCode) {
        console.log('showFlag()');
    }
};

/**
 * Add the custom "marker" tags for selection-setting purposes
 * @param range
 * @returns {*}
 */
const insertOpenAndCloseMarkers = (range) => {
    //We create and will insert custom tags to act as "markers," so we can reset the selection after all formatting
    const openMarker = document.createElement('marker');
    jQuery(openMarker).attr('id','openMarker');
    const closeMarker = document.createElement('marker');
    jQuery(closeMarker).attr('id','closeMarker');
    range.insertNode(openMarker);

    //Collapse the range to the end, so we can insert the closeMarker in the proper spot
    range.collapse(false);
    range.insertNode(closeMarker);
    return range;
};

/**
* For basic text formatting
*/
const execFormattingTool = (tool,editArea,format = true,props = false) => {

    logVitals('execFormattingTool');

    //Get the selection range - since this varies browser to browser, we're going to have to do some normalizing
    let range = window.getSelection().getRangeAt(0);

    let emptySelection = range.collapsed;

    range = insertOpenAndCloseMarkers(range);

    //This makes sure markers are *inside* top-level P tags
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

    //We get a special object representing some key info about the selection for later use
    getSelectionObject(tool,editArea,emptySelection,props);

    //For empty selects, we need to update active tools display
    if(selectionObject.emptySelection && -1 === advancedFormat.indexOf(selectionObject.tool)) {
        toggleSelectedTools(selectionObject.tool);
    } else if(format) { //Format may be false in the case of evaluateFormatting, where we just want to get a selectionObject based on mere selection, not a formatting button click
      //Go through the logic to apply (or reverse) formatting on selection
      wrapTags(editArea);
    } else if(-1 < advancedFormat.indexOf(selectionObject.tool)) { //For advanced formatting
        switch (selectionObject.tool) {
            case 'ol':
                listifySelectedElement('ordered',editArea);
                break;
            case 'ul':
                listifySelectedElement('unordered',editArea);
                break;
            case 'indent':
                handleIndentation('indent',editArea);
                break;
        }
    }

    //Make sure we've got paragraphs in there
    editArea.html(paragraphize(editArea.html()));

    //Remove any nested instances of formatting
    cleanRedundantCode(editArea);

    //Reset the selection since the above will destroy the original selection
    replaceMarkersWithSelection(editArea);

    //Update hidden and code divs to match
    const masterDiv = editArea.closest('.textEditorMasterDiv');
    const codeDiv = masterDiv.find('.code-editor');
    const hiddenInput = masterDiv.find('.text-editor');
    const code = editArea.html();
    codeDiv.val(code);
    hiddenInput.val(code);

    logVitals('execFormattingTool',true);

};

/**
* Tool clicked on empty selection - decide whether we need to add formatting or reverse it for next character typed
*/
const reverseOrAddOnEmpty = () => {

  logVitals('reverseOrAddOnEmpty');

  toReverse = ancestorTools.filter(x => !activeTools.includes(x));
  toAdd = activeTools.filter(x => !ancestorTools.includes(x));

  logVitals('reverseOrAddOnEmpty',true);

};

/**
* Turn tools on or off based on button click for empty selection
*/
const toggleSelectedTools = tool => {

  logVitals('toggleSelectedTools');

  const targetToolIndex = selectedTools.indexOf(tool);
  if(targetToolIndex > -1) {
    selectedTools.splice(targetToolIndex,1);
  } else if(null !== tool) {
    selectedTools.push(tool);
  }

  logVitals('toggleSelectedTools',true);

};

/**
* Get some info about the selection in an object we can reference in code later on.
*/
const getSelectionObject = (tool,editArea,emptySelection,props) => {

  logVitals('getSelectionObject');

    selectionObject = {
        openAncestor:false,
        closeAncestor:false,
        allFormatted: false,
        tool: tool,
        openTool: '<' + tool + '>',
        closeTool: '</' + tool + '>',
        emptySelection: emptySelection
    };
    let editAreaString = editArea.html();
    const patternString = openMarkerString + '(.*)' + closeMarkerString;
    const pattern = new RegExp(patternString);
    let contentString = '';
    const matches = editAreaString.match(pattern);
    if(matches && matches.length > 1) {
      contentString = matches[1];
    }
    const openMarker = editArea.find('#openMarker');
    const openMarkerAncestor = openMarker.closest(tool);
    const closeMarker = editArea.find('#closeMarker');
    const closeMarkerAncestor = closeMarker.closest(tool);
    let containsOpenTag = false;
    let containsCloseTag = false;

    if(contentString) {
        selectionObject.contentString = contentString;
    }
    if(contentString.indexOf(selectionObject.openTool) > -1) {
        containsOpenTag = true;
    }

    if(contentString.indexOf(selectionObject.closeTool) > -1) {
        containsCloseTag = true;
    }
    if(openMarkerAncestor.length) {
        selectionObject.openAncestor = openMarkerAncestor;
    }
    if(closeMarkerAncestor.length) {
        selectionObject.closeAncestor = closeMarkerAncestor;
    }

    //See whether any props have been passed and add them to the open tool
    if(props) {
      let toolWithProps = '<' + tool;
      const keys = Object.keys(props);
      keys.forEach((key, index) => {
        toolWithProps += ` ${key}="${props[key]}"`;
      });
      toolWithProps += '>';
      selectionObject.openTool = toolWithProps;
    }

    //Determine if all text in contentString is already wrapped in this tool
    if((containsOpenTag || containsCloseTag) ||
      (selectionObject.openAncestor || selectionObject.closeAncestor)) { //Only necessary if we've got an tag inside the selection or either marker has an ancestor of the selected tool

      //We are 'faking' proper HTML by wrapping the (possibly) partial HTML in the selected formatting tool.
      contentString = selectionObject.openTool + contentString + selectionObject.closeTool;

      //Search for and remove any doubles of tags created by the step above
      const openTagPattern = new RegExp(selectionObject.openTool + selectionObject.openTool, 'gi');
      const closeTagPattern = new RegExp(selectionObject.closeTool + selectionObject.closeTool, 'gi');
      contentString = contentString.replace(openTagPattern,selectionObject.openTool).replace(closeTagPattern,selectionObject.closeTool);

      //Create new JQuery object containing the cleaned HTML of the selected content
      let contentStringObj = $('<span>');
      contentStringObj.html(contentString);

      //Find any instances of the selected tool as *children* of our JQuery object
      const tools = contentStringObj.find(tool);
      let wrappedText = ''; //A placeholder string for all text already wrapped in tool
      tools.each(function() {
        if(0 === $(this).find(tool).length) { //This allows us only to give this treatment to the bottom-most children
          wrappedText += $(this).text(); //We "unwrap" them by simply replacing them with their own inner HTML and that to our placeholder string
        }
      });

      if(contentStringObj.text() === wrappedText) { //So if placeholder sting equals the text of the overall object, then we know all text had already been wrapped in the tool.
          selectionObject.allFormatted = true;
      }
    }

    logVitals('getSelectionObject',true);

};

/**
* If selected text is already all formatted, reverse it. Otherwise apply selected tool.
*/
const wrapTags = editArea => {

  logVitals('wrapTags');
  if(null === selectionObject.tool) {
      return;
  }

    let editAreaString = editArea.html();
    if(selectionObject.allFormatted) {
        editAreaString = reverseFormatting(editAreaString);
        inactivateToolDisplay(editArea,selectionObject.tool);
    } else {
        editAreaString = addFormatting(editAreaString);
        activateToolDisplay(editArea,selectionObject.tool);
    }
    editArea.html(editAreaString);

    logVitals('wrapTags',true);
};

/**
* Simply wrap selected content in tags for tool.
*/
const addFormatting = editAreaString => {

    logVitals('addFormatting');

    let betweenMarkersContent = getBetweenMarkersContent(editAreaString);

    //In the case of links, check for unbalanced formatting tags
    if('a' === selectionObject.tool) {
        editAreaString = correctTagBalance(editAreaString,betweenMarkersContent);
        //Have to rest betweenMarkersContent since it will have changed due to tag balancing
        betweenMarkersContent = getBetweenMarkersContent(editAreaString);
    }

    return editAreaString.replace(openMarkerString + betweenMarkersContent + closeMarkerString, selectionObject.openTool + openMarkerString + betweenMarkersContent + closeMarkerString + selectionObject.closeTool);

    logVitals('addFormatting',true);
};

/**
 * An anchor tag spanning part of a formatting tag will be broken in two unless we manually a balance the formatting tag
 * @param editAreaString
 * @param betweenMarkersContent
 * @returns {*}
 */
const correctTagBalance = (editAreaString,betweenMarkersContent) => {
    //See if we have any close tags w/o open tags between the markers
    tags.forEach((tag) => {
       const openTool = '<' + tag + '>';
       const closeTool = '</' + tag + '>';
       const openToolIndex = betweenMarkersContent.indexOf(openTool);
       const closeToolIndex = betweenMarkersContent.indexOf(closeTool);
       if(-1 < closeToolIndex) {
           if(-1 === openToolIndex || closeToolIndex < openToolIndex) {
               editAreaString = editAreaString.replace(openMarkerString + betweenMarkersContent + closeMarkerString,closeTool + openMarkerString + openTool + betweenMarkersContent + closeMarkerString);
               betweenMarkersContent = openTool + betweenMarkersContent;
           }
       }
    });
    return editAreaString;
};

/**
* This is the more-complex case. Sometimes we need to wrap selected content in tags in reverse order,
* sometimes we need to just close formatting at the beginning of the selection.
*/
const reverseFormatting = editAreaString => {
  logVitals('reverseFormatting');
  const openMarkerPattern = new RegExp(openMarkerString);
  const closeMarkerPattern = new RegExp(closeMarkerString);

  //In the case of no ancestor elements of the markers for the selected tool, or only one for the open marker, we just close the formatting early.
  if((selectionObject.openAncestor && !selectionObject.closeAncestor) || !selectionObject.openAncestor && !selectionObject.closeAncestor) {
      editAreaString = editAreaString.replace(openMarkerPattern,'~~makeClose~~');
  } else { //Otherwise, we have formatted content BEYOND the selection and need to repoen the formatting after reversing it for the selection.
      editAreaString = editAreaString.replace(openMarkerPattern,'~~makeClose~~').replace(closeMarkerPattern,'~~makeOpen~~');
  }

  editAreaString = editAreaString.replace('~~makeClose~~',selectionObject.closeTool + openMarkerString).replace('~~makeOpen~~',closeMarkerString + selectionObject.openTool);

  //Get rid of any tools in between markers that survived the above, so we're just left with the close-then-open or simply close tag.
  const betweenMarkersContent = getBetweenMarkersContent(editAreaString);
  const cleanBetweenMarkersContent = getCleanContent(betweenMarkersContent);

  return editAreaString.replace(betweenMarkersContent,cleanBetweenMarkersContent);
    logVitals('reverseFormatting',true);
};

/**
* On each click or keydown, we check the formatting of the selection and adjust active tools accordingly
*/
const evaluateFormatting = (editArea,e) => {

  logVitals('evaluateFormatting');

  setTimeout(function() {
    const range = window.getSelection().getRangeAt(0);
    const emptySelection = range.collapsed;
    if(emptySelection) {
      const emptyMarker = $('<empty>'); //A fake element for the purposes finding ancestor elements with jQuery
      range.surroundContents(emptyMarker[0]);
      ancestorTools = [];
      allTags.forEach(function(tag,index) {
        const ancestor = emptyMarker.closest(tag);
        if(ancestor.length) {
          ancestorTools.push(tag);
        }
      });
        emptyMarker.remove();
        inactivateNonSelectedToolsDisplay(editArea);
        reconcileToolsDisplay(editArea);
    } else {
      tags.forEach(function(tool,index) {
        if(e.shiftKey && 37 === e.which) { //This prevents weird selection behavior on this key combo
          return;
        } else {
          execFormattingTool(tool,editArea,false);
        }
        if(selectionObject.allFormatted) {
          activateToolDisplay(editArea,tool);
        } else {
          inactivateToolDisplay(editArea,tool);
        }
      });
    }
  },5);

  logVitals('evaluateFormatting',true);
}

/**
* Toggle tool-active display
*/
const activateToolDisplay = (editArea,tool) => {
    logVitals('activateToolDisplay');
    editArea.closest('.textEditorMasterDiv').find(`[data-tool='${tool}']`).addClass('active');
    const targetToolIndex = activeTools.indexOf(tool);
    if(targetToolIndex === -1 && null !== tool) {
      activeTools.push(tool);
    }
    logVitals('activateToolDisplay',true);
};
const inactivateToolDisplay = (editArea,tool) => {
  logVitals('inactivateToolDisplay');
  editArea.closest('.textEditorMasterDiv').find(`[data-tool='${tool}']`).removeClass('active');
  const targetToolIndex = activeTools.indexOf(tool);
  if(targetToolIndex > -1) {
    activeTools.splice(targetToolIndex,1);
  }
  logVitals('inactivateToolDisplay',true);
};
const inactivateAllToolsDisplay = editArea => editArea.closest('.textEditorMasterDiv').find('.active').removeClass('active');
const inactivateNonSelectedToolsDisplay = editArea => {
  logVitals('inactivateNonSelectedToolsDisplay');
  tags.forEach(function(tool,index) {
      if(selectedTools.indexOf(tool) === -1) {
        inactivateToolDisplay(editArea,tool);
      }
  });
  logVitals('inactivateNonSelectedToolsDisplay',true);
};

/**
* Reconcile user intention for tool-button status based on existing formatting and formatting commands from  clicks or ctrl-command
*/
const reconcileToolsDisplay = editArea => {
  logVitals('reconcileToolsDisplay');
  $('.toolbar-button').removeClass('active');
  //If the selection has ancestor formatting, we make that tool button active unless a formatting command is reversing it
  ancestorTools.forEach(function(tool,index) {
    inactivateToolDisplay(editArea,tool);
    if(selectedTools.indexOf(tool) === -1) {
      activateToolDisplay(editArea,tool);
    }
  });
  //For all formatting commands, we activate that the corresponding button unless reversed by ancestor formatting
  selectedTools.forEach(function(tool,index) {
    const toolButton = editArea.closest('.textEditorMasterDiv').find(`[data-tool='${tool}']`);
    inactivateToolDisplay(editArea,tool);
    if(ancestorTools.indexOf(tool) === -1) {
      activateToolDisplay(editArea,tool);
    }
  });
  //For all active buttons, we inactivate 'em if they've been selected as the formatting action
  activeTools.forEach(function(tool,index) {
    const toolButton = editArea.closest('.textEditorMasterDiv').find(`[data-tool='${tool}']`);
    if(selectedTools.indexOf(tool) === -1 && ancestorTools.indexOf(tool) === -1) {
      inactivateToolDisplay(editArea,tool);
    }
  });
  reverseOrAddOnEmpty();

  logVitals('reconcileToolsDisplay',true);
};

/**
* For selecting the content between the markers for manuplulation.
*/
const getBetweenMarkersContent = editAreaString => {
    logVitals('getBetweenMarkersContent');
    const betweenMarkersPattern = new RegExp(openMarkerString + '(.*)' + closeMarkerString);
    return editAreaString.match(betweenMarkersPattern)[1];
    logVitals('getBetweenMarkersContent',true);
};

/**
* For removing any instances of the tool in a given piece of content
*/
const getCleanContent = content => {
  logVitals('getCleanContent');
    const openTagPattern = new RegExp(selectionObject.openTool,'gi');
    const closeTagPattern = new RegExp(selectionObject.closeTool,'gi');
    return content.replace(openTagPattern,'').replace(closeTagPattern,'');
      logVitals('getCleanContent',true);
};

/**
* It's possible we've got "redundant" formatting tags left over after the above, as in a B tag with B children. Clean 'em up.
*/
const cleanRedundantCode = editArea => {
  logVitals('cleanRedundantCode');
    tags.forEach(function(tag) {
        const inspectedElements = editArea.find(tag);
        if(inspectedElements.length) {
            inspectedElements.each(function() {
                const inspectedElementsDescendants = $(this).find(tag);
                if(inspectedElementsDescendants.length) {
                    inspectedElementsDescendants.each(function() {
                        $(this).replaceWith($(this).html());
                    });
                }
            });
        }
    });

    //Find specified redundancies we know of due to experience.
    let editAreaString = editArea.html();
    tags.forEach(function(tag) {
        const openTag = '<' + tag + '>';
        const closeTag = '</' + tag + '>';
        //Case: Tag closes and immediately opens again, with a marker between.
        let redundantCloseOpen = new RegExp(closeTag + openMarkerString + openTag,'gi');
        editAreaString = editAreaString.replace(redundantCloseOpen,openMarkerString);
        redundantCloseOpen = new RegExp(closeTag + closeMarkerString + openTag,'gi');
        editAreaString = editAreaString.replace(redundantCloseOpen,closeMarkerString);

        //Case: Tag closes and immediately opens again, without a marker in between
        redundantCloseOpen = new RegExp(closeTag + openTag,'gi');
        editAreaString = editAreaString.replace(redundantCloseOpen,'');

        //Case: An empty tag.
        redundantCloseOpen = new RegExp(openTag + closeTag,'gi');
        editAreaString = editAreaString.replace(redundantCloseOpen,'');
    });

    //Case: <strong> and <em> tags perhaps pasted in from elsewhere.
    editAreaString = editAreaString.replace(/<strong>/gi,'<b>').replace(/<\/strong>/gi,'</b>')
        .replace(/<em>/gi,'<i>').replace(/<\/em>/gi,'</i>');

    //Case: Empty paragraphs
    editAreaString = editAreaString.replace(/<p><\/p>/gi,'');

    //Case: Divs
    editAreaString = editAreaString.replace(/<div/gi,'<p').replace(/<\/div>/gi,'</p>');

    //Case: Trailing BR tags
    editAreaString = editAreaString.replace(/(?<!<p>)<br><\/p>/gi,'</p>');

    editArea.html(editAreaString);

    //Last check for loose text nodes
    editArea.contents().filter(function () {
        return this.nodeType === 3; //Text node
    }).wrap('<p></p>');

    logVitals('cleanRedundantCode',true);
};

/**
* Make sure our editorDiv and codeDiv have P tags
*/
const paragraphize = text => {
    if(text && '' !== text) {
        const openTagPattern = new RegExp('<div', 'gi');
        const closeTagPattern = new RegExp('</div>', 'gi');
        text = text.replace(openTagPattern,'<p').replace(closeTagPattern,'</p>');
        if(0 !== text.indexOf('<p>')) {
            text = '<p>' + text + '</p>';
        }
    } else {
        text = '<p><br></p>';
    }
    return text;
};

/**
* We've kept our marker tags throughout all the manipulation above, so we can reset the selection in a way that will be visually identical to what the user originally selected.
*/
const replaceMarkersWithSelection = editArea => {
    //First we deal with empty paragraph tags without ancestors, which can confound selection
    if(!ancestorTools.length) {
      let editAreaString = editArea.html();
      const pattern = new RegExp('<p>' + openMarkerString + closeMarkerString + '</p>', 'gi');
      editAreaString = editAreaString.replace(pattern,'<p>' + openMarkerString + '<br>' + closeMarkerString + '</p>');
      editArea.html(editAreaString);
    }

    logVitals('replaceMarkersWithSelection');
    //Make brand-new range
    const range = document.createRange();
    //Clean up any old selection
    const selection = window.getSelection();
    selection.removeAllRanges();

    //Add new range as selection
    selection.addRange(range);

    //Set start and end on new range
    range.setStartAfter(editArea.find('#openMarker')[0]);
    range.setEndBefore(editArea.find('#closeMarker')[0]);

    //Remove marker tags
    editArea.find('marker').remove();
    logVitals('replaceMarkersWithSelection',true);
};

/**
 * Remove HTML (except links) from copy.
 * @param el
 */
const stripTags = el => {
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
        $(el).text($(el).text() + ' '); //Adds a space to end of each text node so we end up with spaces between paragraphs.
        $(el).replaceWith($(el).contents());
    }

    activeTools = [];
    selectedTools = [];
    ancestorTools = [];
    const editArea = $(':focus');
    if(editArea.hasClass('fancy-text-div')) {
        reconcileToolsDisplay(editArea);
    }

};

/**
 * Handles insertion/updating of ordered and unordered lists
 * @param type
 * @param editArea
 */
const listifySelectedElement = (type = 'ordered', editArea) => {
    let listTag = '<ol>';
    let listNodeName = 'OL';
    if('unordered' === type) {
        listTag = '<ul>';
        listNodeName = 'UL';
    }

    const openMarker = editArea.find('#openMarker');
    const openMarkerParent = openMarker.parent();
    const openMarkerGrandparent = openMarkerParent.parent();
    const openMarkerGrandparentNodeName = openMarkerGrandparent[0].nodeName;
    //See whether we only mean to change format between ordered and unordered
    if(('UL' === openMarkerGrandparentNodeName || 'OL' === openMarkerGrandparentNodeName) && openMarkerGrandparentNodeName !== listNodeName) {
        const newOrderedList = jQuery(listTag);
        newOrderedList.html(openMarkerGrandparent.html());
        openMarkerGrandparent.replaceWith(newOrderedList);
        inactivateToolDisplay(editArea,'ul');
        inactivateToolDisplay(editArea,'ol');
        activateToolDisplay(editArea,selectionObject.tool);
        return;
    }

    //Let us gather all the elements that need listifying
    let eligibleElements = [openMarkerParent];
    let moreEligible = true;
    if(openMarkerParent.find('#closeMarker').length) {
        moreEligible = false;
    }
    let currentEl = openMarkerParent;
    while(moreEligible) {
        const nextEl = currentEl.next();
        if(nextEl.length) {
            currentEl = nextEl;
            eligibleElements.push(currentEl);
            if(currentEl.find('#closeMarker').length) {
                moreEligible = false;
            }
        } else {
            moreEligible = false;
        }
    }

    //Listify each item
    eligibleElements.forEach((el) => {
        const elParent = el.parent();
        const elNodeName = el[0].nodeName;
        if('LI' === elNodeName) {
            const prevSibling = el.prev();
            const nextSibling = el.next();
            const newParagraph = jQuery('<p>');
            newParagraph.html(el.html());
            if(prevSibling.length && nextSibling.length) { //We're in the middle of the list
                const newOrderedList = jQuery(listTag);
                const afterSiblings = el.nextAll().detach();
                newOrderedList.append(afterSiblings);
                el.remove();
                elParent.after(newOrderedList).after(newParagraph);
            } else if(prevSibling.length && !nextSibling.length) { //We're at the end of the list
                el.remove();
                elParent.after(newParagraph);
            } else if(!prevSibling.length && nextSibling.length) { //We're at the beginning of the list
                el.remove();
                elParent.before(newParagraph);
            } else { //We are a list of one item
                elParent.replaceWith(newParagraph);
            }
            inactivateToolDisplay(editArea,selectionObject.tool);
        } else {
            const newListItem = jQuery('<li>');
            newListItem.html(el.html());
            const prevSibling = el.prev();
            const nextSibling = el.next();

            //Check whether we should add item to neighboring list
            if(prevSibling.length && listNodeName === prevSibling[0].nodeName) { //Append to previous list
                el.remove();
                prevSibling.append(newListItem);
            } else if(nextSibling.length && listNodeName === nextSibling[0].nodeName) { //Prepend to following list
                el.remove();
                nextSibling.prepend(newListItem);
            } else { //Start new list
                const newOrderedList = jQuery(listTag);
                newOrderedList.append(newListItem);
                el.replaceWith(newOrderedList);
            }
            activateToolDisplay(editArea,selectionObject.tool);
        }
    });
};

const handleIndentation = (direction = 'indent',editArea) => {
    if('outdent' === direction) {
        //Well?
    }

    const openMarker = editArea.find('#openMarker');
    const openMarkerParent = openMarker.parent();
    const openMarkerParentNodeName = openMarkerParent[0].nodeName;
    console.log('We here:');
    console.log('openMarkerParentNodeName:');
    console.log(openMarkerParentNodeName);
    return;
    const openMarkerGrandparent = openMarkerParent.parent();
    const openMarkerGrandparentNodeName = openMarkerGrandparent[0].nodeName;
    //See whether we only mean to change format between ordered and unordered
    if(('UL' === openMarkerGrandparentNodeName || 'OL' === openMarkerGrandparentNodeName) && openMarkerGrandparentNodeName !== listNodeName) {
        const newOrderedList = jQuery(listTag);
        newOrderedList.html(openMarkerGrandparent.html());
        openMarkerGrandparent.replaceWith(newOrderedList);
        inactivateToolDisplay(editArea,'ul');
        inactivateToolDisplay(editArea,'ol');
        activateToolDisplay(editArea,selectionObject.tool);
        return;
    }

    //Let us gather all the elements that need listifying
    let eligibleElements = [openMarkerParent];
    let moreEligible = true;
    if(openMarkerParent.find('#closeMarker').length) {
        moreEligible = false;
    }
    let currentEl = openMarkerParent;
    while(moreEligible) {
        const nextEl = currentEl.next();
        if(nextEl.length) {
            currentEl = nextEl;
            eligibleElements.push(currentEl);
            if(currentEl.find('#closeMarker').length) {
                moreEligible = false;
            }
        } else {
            moreEligible = false;
        }
    }

    //Listify each item
    eligibleElements.forEach((el) => {
        const elParent = el.parent();
        const elNodeName = el[0].nodeName;
        if('LI' === elNodeName) {
            const prevSibling = el.prev();
            const nextSibling = el.next();
            const newParagraph = jQuery('<p>');
            newParagraph.html(el.html());
            if(prevSibling.length && nextSibling.length) { //We're in the middle of the list
                const newOrderedList = jQuery(listTag);
                const afterSiblings = el.nextAll().detach();
                newOrderedList.append(afterSiblings);
                el.remove();
                elParent.after(newOrderedList).after(newParagraph);
            } else if(prevSibling.length && !nextSibling.length) { //We're at the end of the list
                el.remove();
                elParent.after(newParagraph);
            } else if(!prevSibling.length && nextSibling.length) { //We're at the beginning of the list
                el.remove();
                elParent.before(newParagraph);
            } else { //We are a list of one item
                elParent.replaceWith(newParagraph);
            }
            inactivateToolDisplay(editArea,selectionObject.tool);
        } else {
            const newListItem = jQuery('<li>');
            newListItem.html(el.html());
            const prevSibling = el.prev();
            const nextSibling = el.next();

            //Check whether we should add item to neighboring list
            if(prevSibling.length && listNodeName === prevSibling[0].nodeName) { //Append to previous list
                el.remove();
                prevSibling.append(newListItem);
            } else if(nextSibling.length && listNodeName === nextSibling[0].nodeName) { //Prepend to following list
                el.remove();
                nextSibling.prepend(newListItem);
            } else { //Start new list
                const newOrderedList = jQuery(listTag);
                newOrderedList.append(newListItem);
                el.replaceWith(newOrderedList);
            }
            activateToolDisplay(editArea,selectionObject.tool);
        }
    });
};

/**
 * Handle keyboard shortcuts for text editor
 */
$(document).on('keydown', function (e) {
    if ((e.metaKey || e.ctrlKey)) {
        let tool = false;
        switch(e.which) {
          case 17:
            return;
            break;
          case 66:
            tool = 'b';
            break;
          case 73:
            tool = 'i';
            break;
          case 75:
            tool = 'a'; //Link
            break;
          case 85:
            tool = 'u';
            break;
        }
        if(tool) {
          const editArea = $(':focus');
          if(tags.indexOf(tool) > -1) {
              if(editArea.hasClass('fancy-text-div')) {
                  e.preventDefault();
                  execFormattingTool(tool,editArea);
              }
          } else if('a' === tool) {
              e.preventDefault();
              let input = prompt('Enter URL:');
              if(input) {
                  const codeEditArea = editArea.closest('.textEditorMasterDiv').find('.code-editor').first();
                  const hiddenInput = editArea.closest('.textEditorMasterDiv').find('.text-editor').first();
                  unlinkSelection(editArea,codeEditArea,hiddenInput);
                  const props = {
                      "href":input,
                      "target":"_blank"
                  };
                  execFormattingTool('a',editArea,true,props);
              }
          }
        }
    }
});

/**
 * Init on load
 */
initTextEditors(50);

const logVitals = (func,leaving = false) => {
    return;
    console.log('----------------------');
    if(leaving) {
        console.log('LEAVING FUNCTION: ' + func);
    } else {
        console.log('ENTERING FUNCTION: ' + func);
    }
    if(toAdd.length) {
        console.log('toAdd');
        console.log(toAdd);
    }
    if(toReverse.length) {
        console.log('toReverse');
        console.log(toReverse);
    }
    if(selectedTools.length) {
        console.log('selectedTools');
        console.log(selectedTools);
    }
    if(activeTools.length) {
        console.log('activeTools');
        console.log(activeTools);
    }
    if(ancestorTools.length) {
        console.log('ancestorTools');
        console.log(ancestorTools);
    }
    const editArea = $(':focus');
    if(editArea.hasClass('fancy-text-div')) {
        console.log('editArea.html()');
        console.log(editArea.html());
    }
}
