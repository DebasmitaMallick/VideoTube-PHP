function getUrlFunction() {
    let url = window.location.href;
    let urlInput = document.getElementById('urlInput');
    urlInput.value = url;
}
$(document).ready(getUrlFunction);

function copyTextFunction() {
    /* Get the text field */
    var copyText = document.getElementById("urlInput");
    /* Select the text field */
    copyText.select();
    copyText.setSelectionRange(0, 99999); /* For mobile devices */

    /* Copy the text inside the text field */
    navigator.clipboard.writeText(copyText.value);

    document.getElementById('iscopied').innerHTML = "Copied!"

}