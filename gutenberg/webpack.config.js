const defaultConfig = require("@wordpress/scripts/config/webpack.config");
const path = require('path');

module.exports = {
    ...defaultConfig,
    entry: {
        'gutenberg': '/src/block-slider.js'
    },
    output: {
        path: path.join(__dirname, '../assets/js/blocks'),
    }
}