let shifts = $('*[class*="shift"]');

//Confirm elements are Graymatter shifts, then process
shifts.each(function(index,el) {
  var classes = $(el).prop('class').split(/\s+/);
  $(classes).each(function(index,elClass) {
      if(elClass.startsWith('shift+')) {
        let elSplitClass = elClass.split('+');
        if(2 === elSplitClass.length && !isNaN(elSplitClass[1])) {
          let target = elSplitClass[1];
          if(0 > target) {
            minusShift(el,target);
          } else {
            plusShift(el,target);
          }
        }
      }
    });
});

function plusShift(el,degree) {
  console.log('Plus shifting:');
  console.log(el);
  console.log(degree);
}

function minusShift(el,degree) {
  console.log('Minus shifting:');
  console.log(el);
  console.log(degree);
}
