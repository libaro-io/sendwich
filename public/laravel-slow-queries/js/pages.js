function showCategory(category) {
    if(category === 'pageRequests'){
        console.log("going to pages");

        u(document.getElementsByClassName('pageRequestsTabs')).addClass('block').removeClass('hidden');
        u(document.getElementsByClassName('queriesTabs')).addClass('hidden').removeClass('block');

        u(document.getElementsByClassName('detailsLists')).addClass('block').removeClass('hidden');
        u(document.getElementsByClassName('queriesLists')).addClass('hidden').removeClass('block');

        u(document.getElementsByClassName('queriesButton')).removeClass('text-gray-800').addClass('text-gray-400');
        u(document.getElementsByClassName('pageRequestsButton')).removeClass('text-gray-300').addClass('text-gray-800');
    }

    if(category === 'queries'){
        console.log('going to queries');

        u(document.getElementsByClassName('queriesTabs')).addClass('block').removeClass('hidden');
        u(document.getElementsByClassName('pageRequestsTabs')).addClass('hidden').removeClass('block');

        u(document.getElementsByClassName('queriesLists')).addClass('block').removeClass('hidden');
        u(document.getElementsByClassName('detailsLists')).addClass('hidden').removeClass('block');

        u(document.getElementsByClassName('pageRequestsButton')).removeClass('text-gray-800').addClass('text-gray-400');
        u(document.getElementsByClassName('queriesButton')).removeClass('text-gray-300').addClass('text-gray-800');
    }

    // u(document.getElementsByClassName('categoriesButton')).removeClass('text-gray-800').addClass('text-gray-400');
    // u(document.getElementsByClassName(category + 'Button')).removeClass('text-gray-300').addClass('text-gray-800');


    // hide all lists
    // u(document.getElementsByClassName('detailsList')).addClass('hidden').removeClass('block');
    //
    // // show request list again
    // u(document.getElementsByClassName(list)).removeClass('hidden').addClass('block');
    //
    // // toggle button
    // u(document.getElementsByClassName('detailsListButton')).removeClass('text-gray-800').addClass('text-gray-400');
    // u(document.getElementsByClassName(list + 'Button')).removeClass('text-gray-300').addClass('text-gray-800');
}
