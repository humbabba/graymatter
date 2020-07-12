console.log('Graymatter JS loaded.');

//Establish vars
let dropdownNavItems,
  mainLink,
  dropdownLinksUl,
  navHamburger;

//Define vars
navHamburger = $('nav-links-mobile svg');
console.log(navHamburger);
dropdownNavItems = $('li.dropdown');


//Set handlers
setHandlers();

function setHandlers() {
  dropdownNavItems.each(function(index,el) {
    addDropdownHandlers($(el));
  });

  addHamburgerHandler(navHamburger);

  windowClickHandlers();
}

function addDropdownHandlers(el) {
  //Make sure clicks on the nav item don't go through to the window
  el.on('click',function(e) {
      e.stopPropagation();
  })

  //Define target elements - the main link and any ULs within the nav item
  mainLink = el.find('a').first();
  dropdownLinksUl = el.find('ul');

  //Handle cliks on the main link to show or hide dropdown items
  mainLink.on('click',function(e) {
    if('none' === dropdownLinksUl.css('display')) {
      dropdownLinksUl.fadeIn(400);
    } else {
      dropdownLinksUl.fadeOut(400);
    }
  });
}

function addHamburgerHandler(hamburger) {
  hamburger.on('click',function () {
    console.log('Clicked');
  });
}

function windowClickHandlers() {
  //Clicks anywhere else will hide open dropdowns
  $(window).on('click',function() {
    if('none' !== dropdownLinksUl.css('display')) {
      dropdownLinksUl.fadeOut(400);
    }
  });
}
