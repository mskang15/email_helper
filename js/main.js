/**
 * Created by MoonSeokKang on 2/21/20.
 */

function getErrorMessageForAlert(response) {
    var message = "";
    if(typeof response.responseJSON == "undefined"){
        message = response.status + " " + response.statusText;
    } else {
        message = response.responseJSON.message;
    }
    return message;
}