// Function to make the change after 7 seconds
function alterarHTML() {
    // Code you want to change
    var element = document.querySelector('.callOutCallWaiterButtonBottom')

    if (element) {
        // Remove text and change class after 7 secondsRemove text and change class after 7 seconds
        setTimeout(function () {
            element.classList.remove('rounded-callW');
            element.classList.add('rounded-circle');
            element.querySelector('.nav-link-inner--text').style.display = 'none';

            // Search for and remove mr-1 class within element
            var elementIcon = element.querySelector('.btn-inner--icon');
            if (elementIcon) {
                elementIcon.classList.remove('mr-1');
            }
        }, 10000);
    }
}

// Call function after page load
window.onload = function () {
    alterarHTML(); // Call the function immediately upon page load
};
