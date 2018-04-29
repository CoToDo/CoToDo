var $collectionHolder;

// setup an "add a tag" link
var $addTagLink = $('<a href="#" class="btn btn-sm btn-outline-primary my-1">Add a tag</a>');
var $newLinkButton = $('<div class="my-1"></div>').append($addTagLink);

jQuery(document).ready(function() {
    // Get the ul that holds the collection of tags
    $collectionHolder = $('div.tags');
    console.log($collectionHolder);

    // add the "add a tag" anchor and li to the tags ul
    $collectionHolder.append($newLinkButton);

    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addTagLink.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new tag form (see next code block)
        addTagForm($collectionHolder, $newLinkButton);
    });
});

//adds new prototype tag when Add Tag button is clicked
function addTagForm($collectionHolder, $newLinkLi) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');
    // get the new index
    var index = $collectionHolder.data('index');

    var newForm = prototype;

    newForm = newForm.replace(/__name__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a tag" link li
    $newLinkLi.before(newForm);
}

jQuery(document).ready(function($) {
    $(".clickable-row").click(function() {
        window.location = $(this).data("href");
    });

    //This calls function autoResizeTextArea on document ready
    jQuery.each(jQuery('.auto-expand'), function() {
        var offset = this.offsetHeight - this.clientHeight;
        autoResizeTextArea(this, offset);
    });

});

//This calls function autoResizeTextArea everytime when textarea registers user input
//Used for auto expanding comments in task show template
jQuery.each(jQuery('.auto-expand'), function() {
    var offset = this.offsetHeight - this.clientHeight;

    jQuery(this).on('keyup input', function() { autoResizeTextArea(this, offset); });
});
//Automatically resizes text area for comments
function autoResizeTextArea(el, offset) {
    jQuery(el).css('height', 'auto').css('height', el.scrollHeight + offset);
}

jQuery(document).ready(function(){
    $("#myInput").on("keyup", function() {
        var value = $(this).val().toLowerCase();
        $("#myTable tr").filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
});

function sortTable(n) {
    var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
    table = document.getElementById("myTable2");
    switching = true;
    // Set the sorting direction to ascending:
    dir = "asc";
    /* Make a loop that will continue until
    no switching has been done: */
    while (switching) {
        // Start by saying: no switching is done:
        switching = false;
        rows = table.getElementsByTagName("TR");
        /* Loop through all table rows (except the
        first, which contains table headers): */
        for (i = 1; i < (rows.length - 1); i++) {
            // Start by saying there should be no switching:
            shouldSwitch = false;
            /* Get the two elements you want to compare,
            one from current row and one from the next: */
            x = rows[i].getElementsByTagName("TD")[n];
            y = rows[i + 1].getElementsByTagName("TD")[n];
            /* Check if the two rows should switch place,
            based on the direction, asc or desc: */
            if (dir == "asc") {
                if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
                    // If so, mark as a switch and break the loop:
                    shouldSwitch= true;
                    break;
                }
            } else if (dir == "desc") {
                if (x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) {
                    // If so, mark as a switch and break the loop:
                    shouldSwitch= true;
                    break;
                }
            }
        }
        if (shouldSwitch) {
            /* If a switch has been marked, make the switch
            and mark that a switch has been done: */
            rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
            switching = true;
            // Each time a switch is done, increase this count by 1:
            switchcount ++;
        } else {
            /* If no switching has been done AND the direction is "asc",
            set the direction to "desc" and run the while loop again. */
            if (switchcount == 0 && dir == "asc") {
                dir = "desc";
                switching = true;
            }

        }
    }
    //list of all icons with sorting arrows
    var arrowIcons = rows[0].getElementsByTagName("I");
    var arrowIcon;

    //set default sorting icon to all columns
    for (i = 0; i < arrowIcons.length; i++) {
        arrowIcon = $(arrowIcons[i]);
        if(arrowIcon.hasClass("fa-sort-down")) arrowIcon.removeClass("fa-sort-down");
        if(arrowIcon.hasClass("fa-sort-up")) arrowIcon.removeClass("fa-sort-up");
        arrowIcon.addClass("fa-sort");
    }


    var sortedArrowIcon = $(arrowIcons[n]);
    if(sortedArrowIcon.hasClass("fa-sort")) sortedArrowIcon.removeClass("fa-sort");
    if(sortedArrowIcon.hasClass("fa-sort-down")) sortedArrowIcon.removeClass("fa-sort-down");
    if(sortedArrowIcon.hasClass("fa-sort-up")) sortedArrowIcon.removeClass("fa-sort-up");

    //set sorting icon to a column that is currently used for sorting
    if(dir == 'asc'){
        sortedArrowIcon.addClass("fa-sort-up");
    }else{
        sortedArrowIcon.addClass("fa-sort-down");
    }

}