$(function() {

    var save = function() {
        var data = {pieces: []};
        $('.jigsaw-piece').each(function() {
            var piece = {
                left:  parseInt($(this).find('img[data-side=left]').attr('data-edge')),
                right: parseInt($(this).find('img[data-side=right]').attr('data-edge')),
                text:  $(this).find('.jigsaw-text').text()
            };
            data.pieces.push(piece);
        });
        $.ajax('save.php?id=' + window.jigsaw.id, {
            method: 'POST',
            data: JSON.stringify(data),
            contentType: 'application/json',
            async: false
        });
    };

    window.setInterval(save, 10000);

    $('#save').click(function() {
        save();
        window.location = 'search.php?id=' + window.jigsaw.id;
    });

    $('.jigsaw-toolbar > *').prop('disabled', true);

    var handlePieceClick = function() {
        $('.jigsaw-piece').removeClass('active');
        $(this).addClass('active');
        $('.jigsaw-toolbar > *').prop('disabled', false);
        var toolbar = $(this).parents('.jigsaw').siblings('.jigsaw-toolbar');
        var buttons = toolbar.find('button');
        buttons.removeClass('active');
        buttons.filter('[data-side=left][data-edge=?]'.replace('?', $(this).find('img[data-side=left]').attr('data-edge'))).addClass('active');
        buttons.filter('[data-side=right][data-edge=?]'.replace('?', $(this).find('img[data-side=right]').attr('data-edge'))).addClass('active');
        toolbar.find('input').val($(this).find('.jigsaw-text').text());
        return false;
    };

    $('.jigsaw-piece').click(handlePieceClick);

    $('.jigsaw-toolbar button').click(function() {
        var side = $(this).data('side');
        var prevEdge = $(this).siblings('.active[data-side=?]'.replace('?', side)).removeClass('active').data('edge');
        $(this).addClass('active');
        var jigsaw = $(this).parents('.jigsaw-toolbar').siblings('.jigsaw');
        var edge = $(this).data('edge');
        jigsaw.find('.active img[data-side=S][data-edge=E]'.replace('E', prevEdge).replace('S', side))
            .attr('src', $(this).find('img').attr('src')).attr('data-edge', edge);
        generate();
        return false;
    });

    $('.jigsaw-toolbar input').keyup(function() {
        $(this).parents('.jigsaw-toolbar').siblings('.jigsaw').find('.active .jigsaw-text').text($(this).val());
        generate();
    });

    $('.jigsaw-toolbar .add').click(function() {
        $('<div class="jigsaw-piece">' +
          '<img data-side="left" data-edge="0" src="images/left/0.png" height="32">' +
          '<span class="jigsaw-text"></span>' +
          '<img data-side="right" data-edge="0" src="images/right/0.png" height="32"></div>')
            .appendTo($(this).parents('.jigsaw-toolbar').siblings('.jigsaw'))
            .click(handlePieceClick)
            .trigger('click');
        generate();
        return false;
    });

    $('.jigsaw-toolbar .remove').click(function() {
        var toolbar = $(this).parents('.jigsaw-toolbar');
        toolbar.find('input').val('');
        toolbar.find('button').removeClass('active');
        toolbar.siblings('.jigsaw').find('.active').remove();
        $('.jigsaw-toolbar > *').prop('disabled', true);
        generate();
        return false;
    });

    var generate = function() {
        var transitions = [];
        $('.jigsaw-piece').each(function() {
            var transition = {};
            transition.left = $(this).find('img[data-side=left]').attr('data-edge');
            transition.right = $(this).find('img[data-side=right]').attr('data-edge');
            transition.text = $(this).find('.jigsaw-text').text();
            transitions.push(transition);
        });
        var getTransitions = function(enteringState) {
            var result = [];
            for (var i = 0; i < transitions.length; i++) {
                if (transitions[i].left === enteringState.toString()) {
                    result.push(transitions[i]);
                }
            }
            return result;
        };
        var cutoff = 10;
        var separator = ' ';
        var depthFirstSearch = function(path, words) {
            var proceed = true;
            var state = '0';
            if (path.length > 0) {
                state = path[path.length - 1].right;
                if (state === '0') {
                    var word = path[0].text;
                    for (i = 1; i < path.length; i++) {
                        word += separator + path[i].text;
                    }
                    words.push(word);
                    return;
                } else if (path.length > cutoff) {
                    return;
                }
            }
            var transitions = getTransitions(state);
            for (var i = 0; i < transitions.length; i++) {
                path.push(transitions[i]);
                depthFirstSearch(path, words);
                path.pop();
            }
        };
        var words = [];
        depthFirstSearch([], words);
        words.sort(function(a, b) {
            return a.length - b.length;
        });
        $('.jigsaw-textarea').text(words.map(w => w.trim().replace(/\s*,\s*/g, ', ')).map(w => w[0].toUpperCase() + w.slice(1) + '.').join('\n'));
    };

    generate();
});
