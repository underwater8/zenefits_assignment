$(function(){
    // Script level variables
    var ajax_results;

    // Config
    min_chars = 3;
    max_suggestion = 3;
    page_size = 10;

    text_search = $('#text-seach');
    btn_submit = $('#btn-submit');
    ul_results = $('#results');
    div_pagination = $('#pages');

    // Bind the handler for one click to avoid duplicate clicks
    btn_submit.one('click', submit_handler);

    // No form submit.
    $('#ajax-form').submit(function(event) {
        event.preventDefault();
    });

    function submit_handler (event) {
        search_str = $.trim(text_search.val());

        // Fire an AJAX call if the submit button is clicked
        $.getJSON('./ajax_handlers/search_result.php', {'s': search_str}, function(data) {
            // If there is only one match and it is as same as the search string, redirect.
            if (data.length === 1 && data[0]['domain'] === search_str) {
                window.location = 'http://' + search_str;

                return;
            }

            // Sort data according to the index
            data.sort(function(a, b) {
                return parseInt(a['index']) - parseInt(b['index']);
            });

            ajax_results = data;

            // Init the first page
            if (data.length === 0) {
                ul_results.html('<li class="list-group-item">Not found.</li>');
            } else {
                ul_results.html($.map(ajax_results.slice(0, page_size), function(item) {
                    return '<li class="list-group-item"><a href="http://' + item['domain'] +'" target="_blank">' + item['domain'] + '</a></li>';
                }));
            }

            // Page change event
            div_pagination.bootpag({
                'total': Math.ceil(ajax_results.length / page_size) || 1,
                'page': 1,
                'maxVisible': 12
            }).on("page", function(event, num){
                shown_items = ajax_results.slice((num - 1) * page_size, num * page_size)
                ul_results.html($.map(shown_items, function(item) {
                    return '<li class="list-group-item"><a href="http://' + item['domain'] +'" target="_blank">' + item['domain'] + '</a></li>';
                }));
            });

            // Bind the handler again
            btn_submit.one('click', submit_handler);
        })
    };

    // Auto completion. Only show top N.
    text_search.typeahead({
        source: function (search_str, process) {
            // Do not fire the AJAX call unless there are enough chars to search with.
            if (search_str.length < min_chars) {
                process([]);

                return;
            }

            $.getJSON('./ajax_handlers/search_result.php', {'s': search_str}, function(data) {
                // Sort the result
                data.sort(function(a, b) {
                    return parseInt(a['index']) - parseInt(b['index']);
                });

                // Pick top N
                top_n = $.map(data.slice(0, max_suggestion), function(item) {
                    return item['domain'];
                });

                process(top_n);
            });
        },
    });
});
