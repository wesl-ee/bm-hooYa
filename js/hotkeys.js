function hotKeys(e) {if (e.altKey) switch(e.keyCode) {
    // alt + l goes to next page
    case (76):
    break;
    // alt + h goes to previous page
    case (13):
    break;
    } }
document.addEventListener("keydown", hotKeys);