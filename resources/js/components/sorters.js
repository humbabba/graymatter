//Defines
let sorterSpans = $('span.sorters');
let upIcon = 'fa-caret-up';
let downIcon = 'fa-caret-down';

if(sorterSpans.length) {
  sorterSpans.each(function(index,el) {
    let sorterSpan = $(el);
    let sortKey = sorterSpan.data('key');

    //Create icons
    let sorterAsc = $('<i class="fas">').addClass(upIcon);
    let sorterDesc = $('<i class="fas">').addClass(downIcon);

    //Add click handlers to icons
    sorterAsc.add(sorterDesc).on('click',function() {
      addSortParams($(this),sortKey);
    });

    //Add icons to span
    sorterSpan.append(sorterAsc).append(sorterDesc);
  });
}

addSortParams = (el,key) => {
  let direction = '';
  if(el.hasClass(upIcon)) {
    direction = 'asc';
  }
  if(el.hasClass(downIcon)) {
    direction = 'desc';
  }
  const urlParams = new URLSearchParams(window.location.search);
  urlParams.set('orderBy', key);
  urlParams.set('direction', direction);
  window.location.search = urlParams;
}
