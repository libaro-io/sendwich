function copyToClipboard(text) {
    // create a temporary input element
    var input = document.createElement('input');
    // set the input element's value to the specified text
    input.setAttribute('value', text);
    // add the input element to the DOM
    document.body.appendChild(input);
    // select the input element's contents
    input.select();
    // copy the selected text to the clipboard
    document.execCommand('copy');
    // remove the input element from the DOM
    document.body.removeChild(input);
}