function isMobile() {
    return window.innerWidth <= 767;
}

// Function to make the change after 10 seconds
function changeHTML() {
    // Code you want to change
    var element = document.querySelector('.callOutCallWaiterButtonBottom')

    // Check if it's a mobile device and update background accordingly
    if (element && isMobile()) {
        element.classList.remove('bg-gradient-red');
        // element.classList.add('bg-gradient-onsolutions');

        // Remove text and change class after 10 secondsRemove text and change class after 10 seconds
        setTimeout(function () {
            element.classList.remove('rounded-callW');
            element.classList.add('rounded-circle');
            element.classList.remove('callOutCallWaiterButtonBottom');
            element.classList.add('callOutCallWaiterButtonBottomMobile');
            element.querySelector('.nav-link-inner--text').style.display = 'none';

            // Search for and remove mr-1 class within element
            var elementIcon = element.querySelector('.btn-inner--icon');
            if (elementIcon) {
                elementIcon.classList.remove('mr-1');
            }
        }, 10000);
    }

    if (element && !isMobile()) {
        // Remove text and change class after 10 secondsRemove text and change class after 10 seconds
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
    changeHTML(); // Call the function immediately upon page load
    // setTimeout(changeHTML, 100); // Call the function with a slight delay after page load
};
