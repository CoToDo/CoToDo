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


    jQuery.each(jQuery('.auto-expand'), function() {
        var offset = this.offsetHeight - this.clientHeight;
        autoResizeTextArea(this, offset);
    });

});



jQuery.each(jQuery('.auto-expand'), function() {
    var offset = this.offsetHeight - this.clientHeight;

    jQuery(this).on('keyup input', function() { autoResizeTextArea(this, offset); });
});

function autoResizeTextArea(el, offset) {
    jQuery(el).css('height', 'auto').css('height', el.scrollHeight + offset);
}