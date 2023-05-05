var coll = document.getElementsByClassName("collapsible");
var i;

for (i = 0; i < coll.length; i++) {
    coll[i].addEventListener("click", function () {
        this.classList.toggle("active");
    });
}
function showList(list) {
    // hide lists
    if(list === 'queriesByLatest' || list === 'queriesByDuration'){
        u(document.getElementsByClassName('queriesList')).addClass('hidden').removeClass('block');
    } else {
        u(document.getElementsByClassName('detailsList')).addClass('hidden').removeClass('block');
    }

    // show request list again
    u(document.getElementsByClassName(list)).removeClass('hidden').addClass('block');

    // toggle button
    u(document.getElementsByClassName('detailsListButton')).removeClass('text-gray-800').addClass('text-gray-400');
    u(document.getElementsByClassName(list + 'Button')).removeClass('text-gray-300').addClass('text-gray-800');
}
