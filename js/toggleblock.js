function toggleBlock(toggleObj, target) {
    var targetObj = document.getElementById(target);
    if (toggleObj.checked) {
        targetObj.style.display = 'block';
    } else {
        targetObj.style.display = 'none';
    }
}