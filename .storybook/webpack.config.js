// you can use this file to add your custom webpack plugins, loaders and anything you like.
// This is just the basic way to add additional webpack configurations.
// For more information refer the docs: https://storybook.js.org/configurations/custom-webpack-config

// IMPORTANT
// When you add this file, we won't add the default configurations which is similar
// to "React Create App". This only has babel loader to load JavaScript.

const path = require("path");
const globImporter = require('node-sass-glob-importer');

const marked = require("marked");
const renderer = new marked.Renderer();

const Twig = require("twig");

Twig.extendFilter('foo', function (value) {
    return value + 'foo';
});

module.exports = {
  plugins: [
    // your custom plugins
  ],
  module: {
    rules: [
      {
        test: /\.css$/,
        use: ['style-loader', 'css-loader']
      },
      {
        test: /\.scss$/,
        loaders: [
          "style-loader",
          "css-loader",
          {
            loader: "sass-loader",
            options: {
              importer: globImporter()
            }
          }
        ],
        include: path.resolve(__dirname, "../assets")
      },
      {
        test: /\.twig$/,
        loader: "twig-loader",
        include: path.resolve(__dirname, "../templates"),
        query: {
          partialDirs: [
            path.join(__dirname, '../templates')
          ]
        }
      },
      {
        test: /\.md$/,
        loader: "markdown-loader",
        options: {
            pedantic: true,
            renderer
        }
      }
    ],
  },
};
