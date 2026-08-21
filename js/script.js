const contentBox = document.getElementById("content");
const characterCount = document.getElementById("character-count");

if (contentBox && characterCount) {

    contentBox.addEventListener("input", function () {

        characterCount.textContent =
            contentBox.value.length + " characters";

    });

}
/* =====================================================
   PROFILE DROPDOWN
   ===================================================== */

const profileButton = document.getElementById("profileButton");
const profileMenu = document.getElementById("profileMenu");

if (profileButton && profileMenu) {

    profileButton.addEventListener("click", function (event) {

        event.stopPropagation();

        profileMenu.classList.toggle("show");

    });


    document.addEventListener("click", function () {

        profileMenu.classList.remove("show");

    });

}