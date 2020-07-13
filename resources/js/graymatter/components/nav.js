//Declare globals
let allNavItems,
  dropdownNavItems,
  mainLink,
  navHamburger,
  hamburgerLinks,
  hamburgerLinksContainer;

//Define globals
allNavItems = $('.nav-links ul');
navHamburger = $('.nav-links-mobile .nav-links-hamburger');
hamburgerLinksContainer = navHamburger.find('.nav-links-hamburger-links-container');
dropdownNavItems = $('li.nav-links-dropdown');

//Set handlers
setHandlers();

function setHandlers() {

  //Dropdown handlers
  if(dropdownNavItems.length) {
    dropdownNavItems.each(function(index,el) {
      addDropdownHandler($(el));
    });
  }

  if(navHamburger.length) {
    addHamburgerHandler(navHamburger);
    addWindowClickHandler(hamburgerLinksContainer);
  }

}

function addDropdownHandler(el) {
  //Make sure clicks on the nav item don't go through to the window
  el.on('click',function(e) {
      e.stopPropagation();
  })
  //Define target elements - the main link and any ULs within the nav item
  mainLink = el.find('a').first();
  let dropdownLinksUl = el.find('ul');
  addWindowClickHandler(dropdownLinksUl);
  //Handle cliks on the main link to show or hide dropdown items
  mainLink.on('click',function(e) {
    if('none' === dropdownLinksUl.css('display')) {
      dropdownLinksUl.fadeIn(400);
    } else {
      dropdownLinksUl.fadeOut(400);
    }
  });

  //Remove if window is resized (or mobile orientation changes)
  $(window).resize(function() {
    if('none' !== dropdownLinksUl.css('display')) {
      dropdownLinksUl.fadeOut(400);
    }
  });
}


  //Remove if window is resized (or mobile orientation changes)
  $(window).resize(function() {
    hamburgerLinksContainer.fadeOut(400);
  });

function addHamburgerHandler(el) {
  //Make sure clicks on the hamburger don't go through to the window
  el.on('click',function(e) {
      e.stopPropagation();
      let hamburgerLinksContainer = $('.nav-links-hamburger-links-container');
      if(hamburgerLinksContainer.length) {
        if('none' === hamburgerLinksContainer.css('display')) {
          hamburgerLinksContainer.fadeIn();
        } else {
          hamburgerLinksContainer.fadeOut(400);
        }
      }
  })
}

function addWindowClickHandler(el) {
  //Clicks anywhere else will hide open dropdowns
  $(window).on('click',function() {
    if('none' !== el.css('display')) {
      el.fadeOut(400);
    }
  });
}
