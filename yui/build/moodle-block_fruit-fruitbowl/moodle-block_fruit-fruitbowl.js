YUI.add('moodle-block_fruit-fruitbowl', function (Y, NAME) {

var CSS = {
        FRUIT: 'fruit',
        SELECTED: 'selected'
    },
    SELECTORS = {
        FRUIT: '.' + CSS.FRUIT
    },
    NS;
 
M.block_fruit = M.block_fruit || {};
NS = M.block_fruit.fruitbowl = {};
 
NS.init = function() {
    Y.delegate('click', this.handle_selection, Y.config.doc, SELECTORS.FRUIT, this);
};
 
NS.handle_selection = function(e) {
    // Alert users when they've clicked on some fruit to tell them the obvious.
    alert("You clicked on some fruit");
 
    // Apply the relevant class which contains indications that this fruit was selected.
    e.target.addClass(CSS.SELECTED);
};

}, '@VERSION@', {"requires": ["base", "node"]});
